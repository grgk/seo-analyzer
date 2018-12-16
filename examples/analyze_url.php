<?php
/**
 * URL analyze example
 *
 * Executes full seo analyze of web page on specified URL
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;
use SeoAnalyzer\HttpClient\Exception\HttpException;

try {
    $results = (new Analyzer())->analyzeUrl('http://www.msn.com/en-us');
} catch (HttpException $e) {
    echo "Error loading page: " . $e->getMessage();
} catch (ReflectionException $e) {
    echo "Error loading metric file: " . $e->getMessage();
}

print_r($results);
