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
     * @var array Web page data
     */
    public $data;

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
        $this->data['parsed_url'] = parse_url($url);
        if (empty($this->data['parsed_url']['scheme'])) {
            $url = 'http://' . $url;
            $this->data['parsed_url'] = parse_url($url);
        }
        if (strcmp($this->data['parsed_url']['scheme'], 'https') === 0) {
            $this->data['https'] = true;
        }
        $this->data['url_length'] = strlen($this->getData('parsed_url.host') . $this->getData('parsed_url.path'));
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
            $loadTime = number_format(( microtime(true) - $starTime), 4);
            $redirect = null;
            if (!empty($redirects = $response->getHeader('X-Guzzle-Redirect-History'))) {
                $redirect = end($redirects);
            }
            return [
                'content' => $response->getBody()->getContents(),
                'loadTime' => $loadTime,
                'redirect' => $redirect
            ];
        }, 300);
        $this->data['loadTime'] = $response['loadTime'];
        $this->content = $response['content'];
        $this->data['redirect'] = $response['redirect'];

        if (empty($this->getData('https'))) {
            $httpsResponseCode = $cache->remember('httpsResponseCode', function () {
                return $this->client->get(str_replace('http://', 'https://', $this->url))->getStatusCode();
            }, 300);
            if ($httpsResponseCode == 200) {
                $this->data['https'] = true;
            }
        }
    }

    /**
     * Parses page's html content setting up related metrics.
     */
    public function parse()
    {
        $parser = new Parser($this->content);
        $this->data['meta'] = $parser->getMeta();
        $this->data['headers'] = $parser->getHeaders();
        $this->data['title'] = $parser->getTitle();
        $this->data['text'] = $parser->getText();
        $this->data['alts'] = $parser->getAlts();
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
        $metrics = [
            'https' => MetricFactory::get('page.https', $this->getData('https')),
            'redirect' => MetricFactory::get('page.redirect', $this->getData('redirect')),
            'page_size' => MetricFactory::get('page.size', strlen($this->content)),
            'meta' => MetricFactory::get('page.meta', [
                'title' => $this->getData('title'),
                'meta' => $this->getData('meta')
            ]),
            'headers' => MetricFactory::get('page.headers', $this->getData('headers')),
            'content_ratio' => MetricFactory::get('page.contentRatio', [
                'content_size' => strlen(preg_replace('!\s+!', ' ', $this->getData('text'))),
                'code_size' => strlen($this->content)
            ]),
            'keyword_density' => MetricFactory::get('page.keywordDensity', [
                'text' => $this->getData('text'),
                'locale' => $this->locale,
                'stop_words' => $this->stopWords
            ]),
            'keyword_density_headers' => MetricFactory::get('page.headersKeywordDensity', [
                'headers' => $this->getData('headers'),
                'locale' => $this->locale,
                'stop_words' => $this->stopWords
            ]),
            'alt_attributes' => MetricFactory::get('page.altAttributes', $this->getData('alts')),
        ];
        if (!empty($this->getData('loadTime'))) {
            $metrics['load_time'] = MetricFactory::get('page.loadTime', $this->getData('loadTime'));
        }
        if (!empty($this->getData('url_length'))) {
            $metrics['url_size'] = MetricFactory::get('page.urlSize', $this->getData('url_length'));
        }
        if (!empty($this->keyword)) {
            $metrics['keyword_url'] = MetricFactory::get('page.keyword', [
                'text' => $this->getData('parsed_url.host'),
                'keyword' => $this->keyword,
                'impact' => 5,
                'type' => 'URL'
            ]);
            $metrics['keyword_path'] = MetricFactory::get('page.keyword', [
                'text' => $this->getData('parsed_url.path'),
                'keyword' => $this->keyword,
                'impact' => 3,
                'type' => 'Url-path'
            ]);
            $metrics['keyword_title'] = MetricFactory::get('page.keyword', [
                'text' => $this->getData('title'),
                'keyword' => $this->keyword,
                'impact' => 5,
                'type' => 'Title'
            ]);
            $metrics['keyword_description'] = MetricFactory::get('page.keyword', [
                'text' => $this->getData('meta.description'),
                'keyword' => $this->keyword,
                'impact' => 3,
                'type' => 'Description'
            ]);
            $metrics['keyword_headers'] = MetricFactory::get('page.keywordHeaders', [
                'headers' => $this->getData('headers'),
                'keyword' => $this->keyword
            ]);
            $metrics['keyword_density_keyword'] = MetricFactory::get('page.keywordDensity', [
                'text' => $this->getData('text'),
                'locale' => $this->locale,
                'stop_words' => $this->stopWords,
                'keyword' => $this->keyword
            ]);
        }
        return $metrics;
    }

    /**
     * Returns data collected by it's key name.
     *
     * @param string $name
     * @return mixed
     */
    public function getData(string $name)
    {
        if (strpos($name, '.') !== false) {
            $keys = explode('.', $name);
            $data = $this->data;
            foreach ($keys as $innerKey) {
                if (!array_key_exists($innerKey, $data)) {
                    return false;
                }
                $data = $data[$innerKey];
            }
            if (!empty($data)) {
                return $data;
            }
        }
        if (!empty($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }
}
