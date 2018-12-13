<?php

namespace SeoAnalyzer;

use SeoAnalyzer\HttpClient\Client;
use SeoAnalyzer\HttpClient\ClientInterface;
use SeoAnalyzer\HttpClient\Exception\HttpException;
use SeoAnalyzer\Metric\MetricFactory;

class Page
{
    /**
     * @var string URL of web page
     */
    public $url;

    /**
     * @var string Page locale
     */
    public $locale = 'en_GB';

    /**
     * @var string Keyword to use in analyse
     */
    public $keyword;

    /**
     * @var array Stop word used in keyword density analyse
     */
    public $stopWords = [];

    /**
     * @var string Web page content (html)
     */
    public $content;

    /**
     * @var array Web page factors values
     */
    public $factors;

    /**
     * @var ClientInterface
     */
    public $client;

    /**
     * Page constructor.
     *
     * @param string|null $url
     * @param string|null $locale
     * @param ClientInterface|null $client
     * @throws HttpException
     */
    public function __construct(string $url = null, string $locale = null, ClientInterface $client = null)
    {
        $this->client = $client;
        if (empty($client)) {
            $this->client = new Client();
        }
        if (!empty($url)) {
            $this->url = $this->setUpUrl($url);
            $this->getContent();
        }
        if (!empty($locale)) {
            $this->locale = $locale;
        }
    }

    /**
     * Verifies URL and sets up some basic metrics.
     *
     * @param string $url
     * @return string URL
     */
    protected function setUpUrl(string $url): string
    {
        $parsedUrl = parse_url($url);
        if (empty($parsedUrl['scheme'])) {
            $url = 'http://' . $url;
            $parsedUrl = parse_url($url);
        }
        $this->setFactor('parsed_url', $parsedUrl);

        if (strcmp($parsedUrl['scheme'], 'https') === 0) {
            $this->setFactor('ssl', true);
        }
        $this->setFactor('url_length', strlen($parsedUrl['host'] . $this->getFactor('parsed_url.path')));
        return $url;
    }

    /**
     * Downloads page content from URL specified and sets up some base metrics.
     *
     * @throws HttpException
     */
    public function getContent()
    {
        $cache = new Cache();
        $response = $cache->remember('response', function () {
            $starTime = microtime(true);
            $response = $this->client->get($this->url, ['allow_redirects' => ['track_redirects' => true]]);
            $loadTime = number_format((microtime(true) - $starTime), 4);
            $redirect = null;
            if (!empty($redirects = $response->getHeader('X-Guzzle-Redirect-History'))) {
                $redirect = end($redirects);
            }
            return [
                'content' => $response->getBody()->getContents(),
                'time' => $loadTime,
                'redirect' => $redirect
            ];
        }, 300);
        $this->setFactor('loadTime', $response['time']);
        $this->content = $response['content'];
        $this->setFactor('redirect', $response['redirect']);

        if (empty($this->getFactor('ssl'))) {
            $httpsResponseCode = $cache->remember('httpsResponseCode', function () {
                return $this->client->get(str_replace('http://', 'https://', $this->url))->getStatusCode();
            }, 300);
            if ($httpsResponseCode == 200) {
                $this->setFactor('ssl', true);
            }
        }
    }

    /**
     * Parses page's html content setting up related metrics.
     */
    public function parse()
    {
        $parser = new Parser($this->content);
        $this->setFactors([
            'meta.meta' => $parser->getMeta(),
            'headers' => $parser->getHeaders(),
            'meta.title' => $parser->getTitle(),
            'text' => $parser->getText(),
            'alts' => $parser->getAlts()
        ]);
    }

    /**
     * Returns page metrics.
     *
     * @return array
     * @throws HttpException
     */
    public function getMetrics(): array
    {
        if (empty($this->content)) {
            $this->getContent();
        }
        if (empty($this->dom)) {
            $this->parse();
        }
        $this->setUpContentFactors();
        if (!empty($this->keyword)) {
            $this->setUpContentKeywordFactors($this->keyword);
        }
        return $this->setUpMetrics($this->getMetricsConfig());
    }

    /**
     * Sets up page content related factors for page metrics.
     */
    public function setUpContentFactors()
    {
        $this->setFactors([
            'content.html' => $this->content,
            'content.size' => strlen($this->content),
            'content.ratio' => [
                'content_size' => strlen(preg_replace('!\s+!', ' ', $this->getFactor('text'))),
                'code_size' => strlen($this->content)
            ],
            'density.page' => [
                'text' => $this->getFactor('text'), 'locale' => $this->locale, 'stop_words' => $this->stopWords
            ],
            'density.headers' => [
                'headers' => $this->getFactor('headers'), 'locale' => $this->locale, 'stop_words' => $this->stopWords
            ]
        ]);
    }

    /**
     * Sets up page content factors keyword related .
     *
     * @param string $keyword
     */
    public function setUpContentKeywordFactors(string $keyword)
    {
        $this->setFactors([
            'keyword.url' => [
                'text' => $this->getFactor('parsed_url.host'), 'keyword' => $keyword, 'impact' => 5, 'type' => 'URL'
            ],
            'keyword.path' => [
                'text' => $this->getFactor('parsed_url.path'), 'keyword' => $keyword, 'impact' => 3, 'type' => 'UrlPath'
            ],
            'keyword.title' => [
                'text' => $this->getFactor('title'), 'keyword' => $keyword, 'impact' => 5, 'type' => 'Title'
            ],
            'keyword.description' => [
                'text' => $this->getFactor('meta.description'),
                'keyword' => $keyword,
                'impact' => 3,
                'type' => 'Description'
            ],
            'keyword.headers' => ['headers' => $this->getFactor('headers'),'keyword' => $keyword],
            'keyword.density' => [
                'text' => $this->getFactor('text'),
                'locale' => $this->locale,
                'stop_words' => $this->stopWords,
                'keyword' => $keyword
            ]
        ]);
    }

    /**
     * Return metrics configuration.
     *
     * @return array
     */
    public function getMetricsConfig()
    {
        $metrics = ['page' => [
            'https' => ['factor' => 'ssl'],
            'redirect' => ['factor' => 'redirect'],
            'size' => ['factor' => 'content.size'],
            'meta' => ['factor' => 'meta'],
            'headers' => ['factor' => 'headers'],
            'contentRatio' => ['factor' => 'content.ratio'],
            'keywordDensity' => ['metric' => 'keywordDensity', 'factor' => 'density.page'],
            'headersKeywordDensity' => ['factor' => 'density.headers'],
            'altAttributes' => ['factor' => 'alts'],
            'urlSize' => ['factor' => 'url_length']
        ]];
        if ($this->getFactor('loadTime')) {
            $metrics['page']['loadTime'] = ['factor' => 'loadTime'];
        }
        if (!empty($this->keyword)) {
            $metrics['page'] = array_merge($metrics['page'], [
                'keywordUrl' => ['metric' => 'keyword', 'factor' => 'keyword.url'],
                'keywordPath' => ['metric' => 'keyword', 'factor' => 'keyword.path'],
                'keywordTitle' => ['metric' => 'keyword', 'factor' => 'keyword.title'],
                'keywordDescription' => ['metric' => 'keyword', 'factor' => 'keyword.description'],
                'keywordHeaders' => ['factor' => 'keyword.headers'],
                'keywordDensityKeyword' => ['metric' => 'keywordDensity', 'factor' => 'keyword.density']
            ]);
        }
        return $metrics;
    }

    /**
     * Sets up page metrics.
     *
     * @param array $config Metrics config
     * @return array
     */
    public function setUpMetrics(array $config)
    {
        $metrics = [];
        if (!empty($config)) {
            foreach ($config as $groupName => $groupContent) {
                foreach ($groupContent as $metricName => $metricData) {
                    if (!empty($metricData['metric'])) {
                        $metricName = $metricData['metric'];
                    }
                    $metrics[$groupName . '_' . $metricName] = MetricFactory::get(
                        $groupName . '.' . $metricName,
                        $this->getFactor($metricData['factor'])
                    );
                }
            }
        }
        return $metrics;
    }

    /**
     * Sets page factor value.
 *
     * @param string $name
     * @param mixed $value
     * @return array
     */
    public function setFactor(string $name, $value)
    {
        $dots = explode(".", $name);
        if (count($dots) > 1) {
            $last = &$this->factors[ $dots[0] ];
            foreach ($dots as $k => $dot) {
                if ($k == 0) continue;
                $last = &$last[$dot];
            }
            $last = $value;
        } else {
            $this->factors[$name] = $value;
        }
        return $this->factors;
    }

    /**
     * Sets multiple page factors values at once.
     *
     * @param array $factors
     */
    public function setFactors(array $factors)
    {
        foreach ($factors as $factorName => $factorValue) {
            $this->setFactor($factorName, $factorValue);
        }
    }

    /**
     * Returns data collected by it's key name.
     *
     * @param string $name
     * @return mixed
     */
    public function getFactor($name)
    {
        if (strpos($name, '.') !== false) {
            $keys = explode('.', $name);
            $factors = $this->factors;
            foreach ($keys as $innerKey) {
                if (!array_key_exists($innerKey, $factors)) {
                    return false;
                }
                $factors = $factors[$innerKey];
            }
            if (!empty($factors)) {
                return $factors;
            }
        }
        if (!empty($this->factors[$name])) {
            return $this->factors[$name];
        }
        return false;
    }
}
