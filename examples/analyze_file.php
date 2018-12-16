<?php
/**
 * File analyze example
 *
 * Executes file seo analyze on local html file.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;
use SeoAnalyzer\HttpClient\Exception\HttpException;

try {
    $results = (new Analyzer())->analyzeFile(__DIR__ . '/data/example.html');
} catch (HttpException $e) {
    echo "Error loading page: " . $e->getMessage();
} catch (ReflectionException $e) {
    echo "Error loading metric file: " . $e->getMessage();
}

print_r($results);
