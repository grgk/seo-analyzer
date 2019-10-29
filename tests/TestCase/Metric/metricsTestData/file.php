<?php
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
    ]
];
