<?php
return [
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
    ]
];
