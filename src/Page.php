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
        $this->setFactor(Factor::URL_PARSED, $parsedUrl);

        if (strcmp($parsedUrl['scheme'], 'https') === 0) {
            $this->setFactor(Factor::SSL, true);
        }
        $this->setFactor(
            Factor::URL_LENGTH,
            strlen($this->getFactor(Factor::URL_PARSED_HOST) . $this->getFactor(Factor::URL_PARSED_PATH))
        );
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
        $this->setFactor(Factor::LOAD_TIME, $response['time']);
        $this->content = $response['content'];
        $this->setFactor(Factor::REDIRECT, $response['redirect']);

        if (empty($this->getFactor(Factor::SSL))) {
            $httpsResponseCode = $cache->remember('httpsResponseCode', function () {
                return $this->client->get(str_replace('http://', 'https://', $this->url))->getStatusCode();
            }, 300);
            if ($httpsResponseCode == 200) {
                $this->setFactor(Factor::SSL, true);
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
            Factor::META_META => $parser->getMeta(),
            Factor::HEADERS => $parser->getHeaders(),
            Factor::META_TITLE => $parser->getTitle(),
            Factor::TEXT => $parser->getText(),
            Factor::ALTS => $parser->getAlts()
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
            Factor::CONTENT_HTML => $this->content,
            Factor::CONTENT_SIZE => strlen($this->content),
            Factor::CONTENT_RATIO => [
                'content_size' => strlen(preg_replace('!\s+!', ' ', $this->getFactor(Factor::TEXT))),
                'code_size' => strlen($this->content)
            ],
            Factor::DENSITY_PAGE => [
                'text' => $this->getFactor(Factor::TEXT), 'locale' => $this->locale, 'stop_words' => $this->stopWords
            ],
            Factor::DENSITY_HEADERS => [
                'headers' => $this->getFactor(Factor::HEADERS),
                'locale' => $this->locale,
                'stop_words' => $this->stopWords
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
            Factor::KEYWORD_URL => [
                'text' => $this->getFactor(Factor::URL_PARSED_HOST),
                'keyword' => $keyword,
                'impact' => 5,
                'type' => 'URL'
            ],
            Factor::KEYWORD_PATH => [
                'text' => $this->getFactor(Factor::URL_PARSED_PATH),
                'keyword' => $keyword,
                'impact' => 3,
                'type' => 'UrlPath'
            ],
            Factor::KEYWORD_TITLE => [
                'text' => $this->getFactor(Factor::TITLE), 'keyword' => $keyword, 'impact' => 5, 'type' => 'Title'
            ],
            Factor::KEYWORD_DESCRIPTION => [
                'text' => $this->getFactor(Factor::META_DESCRIPTION),
                'keyword' => $keyword,
                'impact' => 3,
                'type' => 'Description'
            ],
            Factor::KEYWORD_HEADERS => ['headers' => $this->getFactor(Factor::HEADERS), 'keyword' => $keyword],
            Factor::KEYWORD_DENSITY => [
                'text' => $this->getFactor(Factor::TEXT),
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
        $config = [
            Factor::SSL,
            Factor::REDIRECT,
            Factor::CONTENT_SIZE,
            Factor::META,
            Factor::HEADERS,
            Factor::CONTENT_RATIO,
            [Factor::DENSITY_PAGE => 'keywordDensity'],
            [Factor::DENSITY_HEADERS => 'headersKeywordDensity'],
            Factor::ALTS,
            Factor::URL_LENGTH
        ];
        if ($this->getFactor(Factor::LOAD_TIME)) {
            array_push($config, Factor::LOAD_TIME);
        }
        if (!empty($this->keyword)) {
            $config = array_merge($config, [
                [Factor::KEYWORD_URL => 'keyword'],
                [Factor::KEYWORD_PATH => 'keyword'],
                [Factor::KEYWORD_TITLE => 'keyword'],
                [Factor::KEYWORD_DESCRIPTION => 'keyword'],
                Factor::KEYWORD_HEADERS,
                [Factor::KEYWORD_DENSITY => 'keywordDensity']
            ]);
        }
        return $config;
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
            foreach ($config as $factor) {
                $metric = $factor;
                if (is_array($factor)) {
                    $metric = current($factor);
                    $factor = key($factor);
                }
                $metrics['page_' . str_replace('.', '_', $metric)] = MetricFactory::get(
                    'page.' . $metric,
                    $this->getFactor($factor)
                );
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
            $last = &$this->factors[$dots[0]];
            foreach ($dots as $k => $dot) {
                if ($k == 0) {
                    continue;
                }
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
     * Returns factor data collected by it's key name.
     *
     * @param string $name
     * @return mixed
     */
    public function getFactor($name)
    {
        if (strpos($name, '.') !== false) {
            return $this->getNestedFactor($name);
        }
        if (!empty($this->factors[$name])) {
            return $this->factors[$name];
        }
        return false;
    }

    /**
     * Returns factor data collected by it's key name.
     *
     * @param string $name
     * @return mixed
     */
    protected function getNestedFactor($name)
    {
        $keys = explode('.', $name);
        $value = $this->factors;
        foreach ($keys as $innerKey) {
            if (!array_key_exists($innerKey, $value)) {
                return false;
            }
            $value = $value[$innerKey];
        }
        return $value;
    }
}
