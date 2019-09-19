<?php

namespace SeoAnalyzer;

use SeoAnalyzer\HttpClient\Client;
use SeoAnalyzer\HttpClient\ClientInterface;
use SeoAnalyzer\HttpClient\Exception\HttpException;
use SeoAnalyzer\Metric\MetricFactory;
use ReflectionException;

class Page
{
    const LOCALE = 'locale';
    const STOP_WORDS = 'stop_words';
    const KEYWORD = 'keyword';
    const IMPACT = 'impact';
    const TEXT = 'text';
    const HEADERS = 'headers';

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
     */
    public function getContent()
    {
        $pageLoadFactors = $this->getPageLoadFactors();
        $this->setFactor(Factor::LOAD_TIME, $pageLoadFactors['time']);
        $this->content = $pageLoadFactors['content'];
        $this->setFactor(Factor::REDIRECT, $pageLoadFactors['redirect']);
        if (empty($this->getFactor(Factor::SSL))) {
            if ($this->getSSLResponseCode() == 200) {
                $this->setFactor(Factor::SSL, true);
            }
        }
    }

    /**
     * Sets page load related factors.
     *
     * @param int $ttl Cache ttl in seconds.
     * @return array
     */
    protected function getPageLoadFactors(int $ttl = 300): array
    {
        $cache = new Cache();
        return $cache->remember('page_content_'. base64_encode($this->url), function () {
            $starTime = microtime(true);
            $response = $this->client->get($this->url, $this->getHttpClientOptions());
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
        }, $ttl);
    }

    /**
     * Returns https response code.
     *
     * @param int $ttl Cache ttl in seconds.
     * @return int|false Http code or false on failure.
     */
    protected function getSSLResponseCode(int $ttl = 300)
    {
        $cache = new Cache();
        return $cache->remember(
            'https_response_code_' . base64_encode('https://' . $this->url),
            function () {
                try {
                    return $this->client->get(str_replace('http://', 'https://', $this->url))->getStatusCode();
                } catch (HttpException $e) {
                    return false;
                }
            },
            $ttl
        );
    }

    /**
     * Returns http client options used for making requests.
     *
     * @return array
     */
    protected function getHttpClientOptions()
    {
        return [
            'allow_redirects' => ['track_redirects' => true],
            self::HEADERS => [
                'User-Agent' => 'grgk-seo-analyzer/1.0'
            ]
        ];
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
     * @throws ReflectionException
     */
    public function getMetrics(): array
    {
        $this->initializeFactors();
        return $this->setUpMetrics($this->getMetricsConfig());
    }

    /**
     * Sets up and returns page metrics based on configuration specified.
     * @param array $config
     * @return array
     * @throws ReflectionException
     * @throws HttpException
     */
    public function setMetrics(array $config)
    {
        $this->initializeFactors();
        return $this->setUpMetrics($config);
    }

    private function initializeFactors()
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
                self::TEXT => $this->getFactor(Factor::TEXT),
                self::LOCALE => $this->locale,
                self::STOP_WORDS => $this->stopWords
            ],
            Factor::DENSITY_HEADERS => [
                self::HEADERS => $this->getFactor(Factor::HEADERS),
                self::LOCALE => $this->locale,
                self::STOP_WORDS => $this->stopWords
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
                self::TEXT => $this->getFactor(Factor::URL_PARSED_HOST),
                self::KEYWORD => $keyword,
                self::IMPACT => 5,
                'type' => 'URL'
            ],
            Factor::KEYWORD_PATH => [
                self::TEXT => $this->getFactor(Factor::URL_PARSED_PATH),
                self::KEYWORD => $keyword,
                self::IMPACT => 3,
                'type' => 'UrlPath'
            ],
            Factor::KEYWORD_TITLE => [
                self::TEXT => $this->getFactor(Factor::TITLE),
                self::KEYWORD => $keyword,
                self::IMPACT => 5,
                'type' => 'Title'
            ],
            Factor::KEYWORD_DESCRIPTION => [
                self::TEXT => $this->getFactor(Factor::META_DESCRIPTION),
                self::KEYWORD => $keyword,
                self::IMPACT => 3,
                'type' => 'Description'
            ],
            Factor::KEYWORD_HEADERS => [self::HEADERS => $this->getFactor(Factor::HEADERS), self::KEYWORD => $keyword],
            Factor::KEYWORD_DENSITY => [
                self::TEXT => $this->getFactor(Factor::TEXT),
                self::LOCALE => $this->locale,
                self::STOP_WORDS => $this->stopWords,
                self::KEYWORD => $keyword
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
            Factor::URL_LENGTH,
            Factor::LOAD_TIME
        ];
        if (!empty($this->keyword)) {
            $config = array_merge($config, [
                [Factor::KEYWORD_URL => self::KEYWORD],
                [Factor::KEYWORD_PATH => self::KEYWORD],
                [Factor::KEYWORD_TITLE => self::KEYWORD],
                [Factor::KEYWORD_DESCRIPTION => self::KEYWORD],
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
     * @throws \ReflectionException
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
        $dots = explode('.', $name);
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
