<?php
/**
 * File analyze example
 *
 * Executes file seo analyze.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;

$results = (new Analyzer())->analyzeFile(__DIR__ . '/data/example.html');

print_r($results);
