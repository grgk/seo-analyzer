<?php

use SeoAnalyzer\Factor;
use SeoAnalyzer\HttpClient\Client;
use SeoAnalyzer\Parser\Parser;

return [
    'locale' => 'en_GB',
    'client' => new Client(),
    'parser' => new Parser(),
    'factors' => [
        Factor::SSL,
        Factor::REDIRECT,
        Factor::CONTENT_SIZE,
        Factor::META,
        Factor::HEADERS,
        Factor::CONTENT_RATIO,
        [Factor::DENSITY_PAGE => 'keywordDensity'],
        [Factor::DENSITY_HEADERS => 'headersKeywordDensity'],
        Factor::ALTS,
        Factor::IMG_SIZE,
        Factor::URL_LENGTH,
        Factor::LOAD_TIME,
        [Factor::KEYWORD_URL => 'keyword'],
        [Factor::KEYWORD_PATH => 'keyword'],
        [Factor::KEYWORD_TITLE => 'keyword'],
        [Factor::KEYWORD_DESCRIPTION => 'keyword'],
        Factor::KEYWORD_HEADERS,
        [Factor::KEYWORD_DENSITY => 'keywordDensity']
    ]
];
