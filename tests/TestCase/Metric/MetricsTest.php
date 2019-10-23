<?php
namespace Tests\TestCase\Metric;

use ReflectionException;
use SeoAnalyzer\Analyzer;
use SeoAnalyzer\Metric\MetricFactory;
use Symfony\Component\Translation\Translator;
use Tests\TestCase;

class MetricsTest extends TestCase
{
    /**
     * @var Translator
     */
    protected $translator;

    public function setUp()
    {
        parent::setUp();
        $analyzer = new Analyzer();
        $this->translator = $analyzer->getTranslator('pl_PL');
    }

    /**
     * @param string $metricKey
     * @param mixed $input
     * @param array $expected
     * @dataProvider metricsDataProvider
     * @throws ReflectionException
     */
    public function testAnalyzePass(string $metricKey, $input, array $expected)
    {
        $metric = MetricFactory::get($metricKey, $input);
        $this->assertInstanceOf($expected['class'], $metric);
        $analysis = $metric->analyze();
        if (isset($expected['value'])) {
            $this->assertSame($expected['value'], $metric->value);
        }
        $this->assertEquals($expected['impact'], $metric->impact);
        $this->assertContains($expected['analysis'], $analysis);
        $this->assertNotEquals($metric->description, $this->translator->trans($metric->description));
        $this->assertNotEquals($analysis, $this->translator->trans($analysis));
    }

    public function metricsDataProvider()
    {
        return [
            [
                'file.robots',
                "User-agent: *\nDisallow:\n",
                [
                    'class' => '\SeoAnalyzer\Metric\File\RobotsMetric',
                    'value' => "User-agent: *\nDisallow:\n",
                    'impact' => 0,
                    'analysis' => 'yes'
                ]
            ],
            [
                'file.robots',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\File\RobotsMetric',
                    'value' => false,
                    'impact' => 1,
                    'analysis' => 'no'
                ]
            ],
            [
                'file.robots',
                'Disallow: /*',
                [
                    'class' => '\SeoAnalyzer\Metric\File\RobotsMetric',
                    'value' => 'Disallow: /*',
                    'impact' => 5,
                    'analysis' => 'Robots.txt file blocks some parts of your site'
                ]
            ],

            [
                'file.sitemap',
                '<?xml version="1.0" encoding="UTF-8"?>',
                [
                    'class' => '\SeoAnalyzer\Metric\File\SitemapMetric',
                    'value' => '<?xml version="1.0" encoding="UTF-8"?>',
                    'impact' => 0,
                    'analysis' => 'yes'
                ]
            ],
            [
                'file.sitemap',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\File\SitemapMetric',
                    'value' => false,
                    'impact' => 1,
                    'analysis' => 'You should consider adding a sitemap.xml'
                ]
            ],

            [
                'page.ssl',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\SSLMetric',
                    'value' => false,
                    'impact' => 3,
                    'analysis' => 'You should use encrypted connection'
                ]
            ],
            [
                'page.ssl',
                true,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\SSLMetric',
                    'value' => true,
                    'impact' => 0,
                    'analysis' => 'yes'
                ]
            ],

            [
                'page.content.size',
                4795,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\SizeMetric',
                    'value' => 4795,
                    'impact' => 0,
                    'analysis' => 'The size of your page is ok'
                ]
            ],
            [
                'page.content.size',
                0,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\SizeMetric',
                    'value' => 0,
                    'impact' => 10,
                    'analysis' => 'Looks that your site content is empty'
                ]
            ],
            [
                'page.content.size',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\SizeMetric',
                    'value' => false,
                    'impact' => 10,
                    'analysis' => 'Can not read your page content'
                ]
            ],
            [
                'page.content.size',
                30001,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\SizeMetric',
                    'value' => 30001,
                    'impact' => 1,
                    'analysis' => 'You should consider some optimisation'
                ]
            ],
            [
                'page.content.size',
                80001,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\SizeMetric',
                    'value' => 80001,
                    'impact' => 3,
                    'analysis' => 'The site is very big. You should consider rebuilding'
                ]
            ],

            [
                'page.meta',
                ['title' => 'Lorem ipsum dolor', 'meta' => ['description' => 'Lorem ipsum dolor sit ipsum dolor sit']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\MetaMetric',
                    'value' => ['title' => 'Lorem ipsum dolor', 'meta' => ['description' => 'Lorem ipsum dolor sit ipsum dolor sit']],
                    'impact' => 0,
                    'analysis' => 'The site meta tags look good'
                ]
            ],
            [
                'page.meta',
                ['title' => false, 'meta' => false],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\MetaMetric',
                    'value' => ['title' => false, 'meta' => false],
                    'impact' => 5,
                    'analysis' => 'The page title length should be between'
                ]
            ],
            [
                'page.meta',
                ['title' => 'Lorem ipsum dolor sit', 'meta' => false],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\MetaMetric',
                    'value' => ['title' => 'Lorem ipsum dolor sit', 'meta' => false],
                    'impact' => 5,
                    'analysis' => 'Missing page meta description tag'
                ]
            ],
            [
                'page.meta',
                ['title' => false, 'meta' => ['description' => 'Lorem ipsum dolor sit ipsum dolor sit']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\MetaMetric',
                    'value' => ['title' => false, 'meta' => ['description' => 'Lorem ipsum dolor sit ipsum dolor sit']],
                    'impact' => 5,
                    'analysis' => 'The page title length should be between'
                ]
            ],
            [
                'page.meta',
                [
                    'title' => 'Lorem ipsum dolor sit ipsum dolor sit ipsum dolor sit ipsum dolor sit ipsum dolor sit',
                    'meta' => ['description' => 'Lorem ipsum dolor sit']
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\MetaMetric',
                    'impact' => 5,
                    'analysis' => 'The page title length should be between'
                ]
            ],
            [
                'page.meta',
                [
                    'title' => 'Lorem ipsum dolor sit',
                    'meta' => ['description' => 'Lorem ipsum dolor sit ipsum dolor sit ipsum dolor sit ipsum dolor sit ipsum dolor sit ipsum dolor sit ipsum dolor sit ipsum dolor sit']
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\MetaMetric',
                    'impact' => 3,
                    'analysis' => 'The page meta description length should be between'
                ]
            ],

            [
                'page.headers',
                ['h1' => ['Lorem ipsum'], 'h2' => ['Lorem ipsum', 'dolor sit'], 'h3' => ['lorem ipsum']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => ['h1' => ['Lorem ipsum'], 'h2' => ['Lorem ipsum', 'dolor sit'], 'h3' => ['lorem ipsum']],
                    'impact' => 0,
                    'analysis' => 'The headers structure on the site looks very good'
                ]
            ],
            [
                'page.headers',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => false,
                    'impact' => 7,
                    'analysis' => 'Looks the site has no headers at all'
                ]
            ],
            [
                'page.headers',
                ['h3' => ['lorem ipsum', 'dolor sit']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => ['h3' => ['lorem ipsum', 'dolor sit']],
                    'impact' => 5,
                    'analysis' => 'There is no H1 header on the site'
                ]
            ],
            [
                'page.headers',
                ['h1' => ['lorem ipsum dolor sit lorem ipsum dolor sit']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => ['h1' => ['lorem ipsum dolor sit lorem ipsum dolor sit']],
                    'impact' => 3,
                    'analysis' => 'The H1 header is too long'
                ]
            ],
            [
                'page.headers',
                ['h1' => ['lorem ipsum', 'dolor sit lorem']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => ['h1' => ['lorem ipsum', 'dolor sit lorem']],
                    'impact' => 3,
                    'analysis' => 'There are multiple H1 headers on the site'
                ]
            ],
            [
                'page.headers',
                ['h1' => ['lorem ipsum dolor sit']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => ['h1' => ['lorem ipsum dolor sit']],
                    'impact' => 3,
                    'analysis' => 'There are no H2 headers on the site'
                ]
            ],
            [
                'page.headers',
                ['h1' => ['lorem ipsum dolor sit'], 'h2' => ['a', 'b', 'c', 'd', 'e', 'f']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => ['h1' => ['lorem ipsum dolor sit'], 'h2' => ['a', 'b', 'c', 'd', 'e', 'f']],
                    'impact' => 1,
                    'analysis' => 'There are a lot of H2 headers'
                ]
            ],
            [
                'page.headers',
                ['h1' => ['lorem ipsum dolor sit'], 'h2' => ['a', 'b']],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersMetric',
                    'value' => ['h1' => ['lorem ipsum dolor sit'], 'h2' => ['a', 'b']],
                    'impact' => 1,
                    'analysis' => 'There are no H3 header on the site'
                ]
            ],

            [
                'page.content.ratio',
                ['content_size' => 1980, 'code_size' => 6960],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\RatioMetric',
                    'value' => 28.0,
                    'impact' => 0,
                    'analysis' => 'Page has good content to code ratio'
                ]
            ],
            [
                'page.content.ratio',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\RatioMetric',
                    'value' => 0,
                    'impact' => 8,
                    'analysis' => 'Content to code ratio is too low'
                ]
            ],
            [
                'page.content.ratio',
                ['content_size' => 19, 'code_size' => 100],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\RatioMetric',
                    'value' => 19.0,
                    'impact' => 5,
                    'analysis' => 'Consider adding more text to your page or remove unnecessary html code'
                ]
            ],
            [
                'page.content.ratio',
                ['content_size' => 8, 'code_size' => 100],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Content\RatioMetric',
                    'value' => 8.0,
                    'impact' => 8,
                    'analysis' => 'Content to code ratio is too low'
                ]
            ],

            [
                'page.keywordDensity',
                [
                    'text' => 'But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?',
                    'locale' => 'en_GB',
                    'stop_words' => ['to', 'you', 'all', 'of', 'and', 'the', 'who', 'has', 'can', 'but', 'or', 'how']
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\KeywordDensityMetric',
                    'impact' => 0,
                    'analysis' => 'The keywords density on the site looks good'
                ]
            ],
            [
                'page.keywordDensity',
                [
                    'text' => 'But I pleasure explain to you how pleasure this mistaken idea of denouncing pleasure and praising pleasure was born and I will give you a complete account of the system, and expound the actual teachings of the great pleasure of the truth, the master-builder of human pleasure. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely pleasure. Nor again is there anyone who loves or pursues or desires to obtain pleasure of itself, because it is pleasure, but because occasionally circumstances occur in which toil and pleasure can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from pleasure? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?',
                    'locale' => 'en_GB',
                    'stop_words' => ['sit']
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\KeywordDensityMetric',
                    'impact' => 3,
                    'analysis' => 'There are some overused keywords on site'
                ]
            ],

            [
                'page.headersKeywordDensity',
                [
                    'headers' => [
                        'h1' => ['lorem sit ipsum dolor sit'],
                        'h2' => ['lorem sit ipsum oko', 'dolor sit oko ipsa', 'lorem sit dolor', 'sit kok ipsum lorem'],
                        'h3' => ['lorem etui sit', 'ipsum dfdrtuw fhfhfgh', 'dolor sit eui'],
                    ],
                    'locale' => 'en_GB',
                    'stop_words' => ['sit', 'eui']
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersKeywordDensityMetric',
                    'impact' => 0,
                    'analysis' => 'The keywords density in headers looks good'
                ]
            ],
            [
                'page.headersKeywordDensity',
                [
                    'headers' => [
                        'h1' => ['lorem sit lorem sit dolor lorem sit dolor ipsum'],
                        'h2' => ['lorem sit ipsum lorem', 'dolor sit lorem ipsa', 'lorem sit dolor', 'sit lorem ipsum'],
                        'h3' => ['lorem etui sit', 'ipsum dfdrtuw fhfhfgh', 'dolor sit eui'],
                    ],
                    'locale' => 'en_GB',
                    'stop_words' => ['sit', 'eui']
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersKeywordDensityMetric',
                    'impact' => 4,
                    'analysis' => 'There are some overused keywords in headers'
                ]
            ],
            [
                'page.headersKeywordDensity',
                [
                    'headers' => [
                        'h1' => ['lorem sit lorem sit dolor lorem sit dolor ipsum'],
                        'h2' => ['lorem sit ipsum lorem', 'dolor sit lorem ipsa', 'lorem sit dolor', 'sit lorem ipsum'],
                        'h3' => ['lorem etui sit', 'ipsum dfdrtuw fhfhfgh', 'dolor sit eui'],
                    ],
                    'locale' => 'en_GB',
                    'stop_words' => []
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\HeadersKeywordDensityMetric',
                    'impact' => 4,
                    'analysis' => 'There are some overused keywords in headers'
                ]
            ],

            [
                'page.alts',
                ['description1', 'description2', 'description3'],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\AltsMetric',
                    'value' => ['description1', 'description2', 'description3'],
                    'impact' => 0,
                    'analysis' => 'Good! All images on site have alternate descriptions'
                ]
            ],
            [
                'page.alts',
                ['description1', '', ''],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\AltsMetric',
                    'value' => ['description1', '', ''],
                    'impact' => 3,
                    'analysis' => 'You should optimise your site adding missing alt descriptions'
                ]
            ],
            [
                'page.alts',
                ['desc1', '', '', 'desc2', '', '', '', '', '', 'desc3', '', '', '', ''],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\AltsMetric',
                    'value' => ['desc1', '', '', 'desc2', '', '', '', '', '', 'desc3', '', '', '', ''],
                    'impact' => 5,
                    'analysis' => 'There is a lot of images without alternate texts'
                ]
            ],
            [
                'page.alts',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\AltsMetric',
                    'impact' => 0,
                    'analysis' => 'There is nothing to do here as there is no images on the site'
                ]
            ],

            [
                'page.loadTime',
                0.34,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\LoadTimeMetric',
                    'value' => 0.34,
                    'impact' => 0,
                    'analysis' => 'The site loads very fast'
                ]
            ],
            [
                'page.loadTime',
                1.26,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\LoadTimeMetric',
                    'value' => 1.26,
                    'impact' => 2,
                    'analysis' => 'You should optimise your site for faster loading'
                ]
            ],
            [
                'page.loadTime',
                4.11,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\LoadTimeMetric',
                    'value' => 4.11,
                    'impact' => 8,
                    'analysis' => 'The site takes very long to load'
                ]
            ],

            [
                'page.url.length',
                16,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Url\LengthMetric',
                    'value' => 16,
                    'impact' => 0,
                    'analysis' => 'The size of URL is ok'
                ]
            ],
            [
                'page.url.length',
                30,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Url\LengthMetric',
                    'value' => 30,
                    'impact' => 1,
                    'analysis' => 'You should consider using some shorter URL'
                ]
            ],
            [
                'page.url.length',
                44,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Url\LengthMetric',
                    'value' => 44,
                    'impact' => 4,
                    'analysis' => 'The site URL is very long'
                ]
            ],

            [
                'page.keyword',
                [
                    'text' => 'But I pleasure explain to you how pleasure this mistaken idea of denouncing pleasure and praising pleasure was born and I will give you a complete account of the system, and expound the actual teachings of the great pleasure of the truth, the master-builder of human pleasure. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely pleasure. Nor again is there anyone who loves or pursues or desires to obtain pleasure of itself, because it is pleasure, but because occasionally circumstances occur in which toil and pleasure can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from pleasure? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?',
                    'keyword' => 'pleasure',
                    'impact' => 5,
                    'type' => 'Test'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\KeywordMetric',
                    'impact' => 0,
                    'analysis' => 'Good! Found the keyword phrase'
                ]
            ],
            [
                'page.keyword',
                [
                    'text' => 'But I pleasure explain to you how pleasure this mistaken idea of denouncing pleasure and praising pleasure was born and I will give you a complete account of the system, and expound the actual teachings of the great pleasure of the truth, the master-builder of human pleasure. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely pleasure. Nor again is there anyone who loves or pursues or desires to obtain pleasure of itself, because it is pleasure, but because occasionally circumstances occur in which toil and pleasure can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from pleasure? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?',
                    'keyword' => 'ugly',
                    'impact' => 5,
                    'type' => 'Test'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\KeywordMetric',
                    'impact' => 5,
                    'analysis' => 'Can not find the keyword phrase'
                ]
            ],

            [
                'page.keyword.headers',
                [
                    'headers' => [
                        'h1' => ['lorem ipsum dolor sit lorem sit dolor ipsum'],
                        'h2' => ['lorem sit ipsum', 'dolor sit ipsa', 'lorem sit dolor', 'sit ipsum'],
                        'h3' => ['lorem etui sit', 'ipsum dfdrtuw fhfhfgh', 'dolor sit eui'],
                    ],
                    'keyword' => 'lorem'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Keyword\HeadersMetric',
                    'impact' => 0,
                    'analysis' => 'Good! The site headers contain the keyword phrase'
                ]
            ],
            [
                'page.keyword.headers',
                [
                    'headers' => [],
                    'keyword' => 'lorem'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Keyword\HeadersMetric',
                    'impact' => 7,
                    'analysis' => 'The main H1 header does not contain the keyword phrase'
                ]
            ],
            [
                'page.keyword.headers',
                [
                    'headers' => [
                        'h1' => ['lorem ipsum dolor sit lorem sit dolor ipsum']
                    ],
                    'keyword' => 'some'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Keyword\HeadersMetric',
                    'impact' => 7,
                    'analysis' => 'The main H1 header does not contain the keyword phrase'
                ]
            ],
            [
                'page.keyword.headers',
                [
                    'headers' => [
                        'h1' => ['some lorem ipsum dolor sit lorem sit dolor ipsum'],
                        'h2' => ['lorem sit ipsum', 'dolor sit ipsa', 'lorem sit dolor', 'sit ipsum'],
                        'h3' => ['lorem etui sit', 'ipsum dfdrtuw fhfhfgh', 'dolor sit eui'],
                    ],
                    'keyword' => 'some'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\Keyword\HeadersMetric',
                    'impact' => 3,
                    'analysis' => 'The site H2 headers does not contain the keyword phrase'
                ]
            ],

            [
                'page.keywordDensity',
                [
                    'text' => 'But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?',
                    'locale' => 'en_GB',
                    'stop_words' => ['to', 'you', 'all', 'of', 'and', 'the', 'who', 'has', 'can', 'but', 'or', 'how'],
                    'keyword' => 'pleasure'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\KeywordDensityMetric',
                    'impact' => 0,
                    'analysis' => 'Good! The key phrase is present in most popular keywords on the site'
                ]
            ],
            [
                'page.keywordDensity',
                [
                    'text' => 'I or of you how and',
                    'locale' => 'en_GB',
                    'stop_words' => [],
                    'keyword' => 'pleasure'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\KeywordDensityMetric',
                    'impact' => 4,
                    'analysis' => 'You should consider adding your keyword to the site content'
                ]
            ],
            [
                'page.keywordDensity',
                [
                    'text' => 'Lorem again is there lorem who loves or pursues or lorem to obtain pain of lorem, because it is pain, but lorem occasionally circumstances occur in lorem toil and pain can lorem him some great pleasure. To take a lorem example, which of us lorem undertakes laborious lorem exercise, lorem to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a lorem that has no annoying lorem, or one who avoids a pain that lorem no resultant lorem?',
                    'locale' => 'en_GB',
                    'stop_words' => ['to', 'you', 'all', 'of', 'and', 'the', 'who', 'has', 'can', 'but', 'or', 'how'],
                    'keyword' => 'lorem'
                ],
                [
                    'class' => '\SeoAnalyzer\Metric\Page\KeywordDensityMetric',
                    'impact' => 4,
                    'analysis' => 'The key phrase is overused on the site'
                ]
            ],

            [
                'page.redirect',
                false,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\RedirectMetric',
                    'impact' => 0,
                    'analysis' => 'no'
                ]
            ],
            [
                'page.redirect',
                true,
                [
                    'class' => '\SeoAnalyzer\Metric\Page\RedirectMetric',
                    'impact' => 2,
                    'analysis' => 'You should avoid redirects'
                ]
            ]
        ];
    }
}
