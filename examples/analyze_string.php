<?php
/**
 * HTML string analyze example
 *
 * Executes file seo analyze on local html file.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;

try {
    $htmlString = file_get_contents(__DIR__ . '/data/example.html');
    $results = (new Analyzer())->analyzeHtml($htmlString);
} catch (ReflectionException $e) {
    echo "Error loading metric file: " . $e->getMessage();
}

print_r($results);
