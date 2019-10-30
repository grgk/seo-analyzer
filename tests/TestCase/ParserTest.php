<?php
namespace Tests\TestCase;

use SeoAnalyzer\Parser\ExampleCustomParser;
use SeoAnalyzer\Parser\Parser;
use Tests\TestCase;

class ParserTest extends TestCase
{
    private $parser;

    public function setUp()
    {
        parent::setUp();
        $this->parser = new Parser(file_get_contents(dirname(__DIR__) . '/data/test.html'));
    }

    public function testGetMetaPass()
    {
        $this->assertContains(
            '{"":"","description":"Some good, valid and proper testing description for our test site","viewport":"',
            json_encode($this->parser->getMeta())
        );
    }

    public function testGetHeadersPass()
    {
        $this->assertContains(
            '{"h1":["Header tells about testing"],"h2":["We like testing","Search engine optimization"],"h3":["',
            json_encode($this->parser->getHeaders())
        );
    }

    public function testGetTitlePass()
    {
        $this->assertEquals(
            'Testing title',
            $this->parser->getTitle()
        );
    }

    public function testGetAltsPass()
    {
        $this->assertContains(
            '["see me testing","check it out","description of image"]',
            json_encode($this->parser->getAlts())
        );
    }

    public function testGetTextPass()
    {
        $text = $this->parser->getText();
        $this->assertEquals(3857, strlen($text));
        $this->assertContains('Testing title Header tells about testing We like testing Search engine', $text);
        $this->assertContains('SEO may target different kinds of search, including image search, video search', $text);
        $this->assertContains('increase its relevance to specific keywords and to remove barriers', $text);
        $this->assertContains('increase its relevance to specific keywords and to remove barriers', $text);
        $this->assertNotContains('alert', $text);
        $this->assertNotContains('testAlert', $text);
        $this->assertNotContains('<html>', $text);
        $this->assertNotContains('<style>', $text);
        $this->assertNotContains('<script>', $text);
        $this->assertNotContains('<h2>', $text);
    }

    public function testExampleCustomParserGetAltsPass()
    {
        $parser = new ExampleCustomParser(file_get_contents(dirname(__DIR__) . '/data/test.html'));
        $this->assertContains(
            '[{"alt":"see me testing","src":"image.jpg"},{"alt":"check it out","src":"image.jpg"},{"alt":"description',
            json_encode($parser->getAlts())
        );
    }
}
