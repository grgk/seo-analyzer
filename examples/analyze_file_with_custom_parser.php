<?php
/**
 * File analyze example
 *
 * Executes file seo analyze on local html file using custom parser.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use SeoAnalyzer\Analyzer;
use SeoAnalyzer\Factor;
use SeoAnalyzer\HttpClient\Exception\HttpException;
use SeoAnalyzer\Page;
use SeoAnalyzer\Parser\ExampleCustomParser;

try {
    $page = new Page('https://www.msn.com/pl-pl');
    $parser = new ExampleCustomParser();
    $page->parser = $parser;
    $analyzer = new Analyzer($page);
    $analyzer->metrics = $page->setMetrics([Factor::ALTS]);
    $results = $analyzer->analyze();
} catch (HttpException $e) {
    echo "Error loading page: " . $e->getMessage();
} catch (ReflectionException $e) {
    echo "Error loading metric file: " . $e->getMessage();
}

print_r($results);
