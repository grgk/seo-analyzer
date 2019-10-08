<?php

namespace SeoAnalyzer;

use DOMDocument;
use DOMNodeList;
use DOMElement;

class Parser
{
    /**
     * @var DOMDocument Dom representation of HTML document
     */
    private $dom;

    /**
     * @param string $html Html document to parse.
     */
    public function __construct(string $html)
    {
        $this->dom = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $this->dom->loadHTML($html, LIBXML_NOWARNING);
        libxml_use_internal_errors($internalErrors);
    }

    /**
     * Returns document meta headers content.
     *
     * @return array
     */
    public function getMeta(): array
    {
        $meta = [];
        foreach ($this->getDomElements('meta') as $item) {
            $meta[$item->getAttribute('name')] = trim($item->getAttribute('content'));
        }
        return $meta;
    }

    /**
     * Returns document headers content.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = [];
        for ($x=1; $x<=5; $x++) {
            foreach ($this->getDomElements('h' . $x) as $item) {
                $headers['h' . $x][] = trim($item->nodeValue);
            }
        }
        return $headers;
    }

    /**
     * Returns page title content.
     *
     * @return string
     */
    public function getTitle(): string
    {
        if ($this->getDomElements('title')->length > 0) {
            return trim($this->getDomElements('title')->item(0)->nodeValue);
        }
        return '';
    }

    /**
     * Returns alt attributes of img tags.
     *
     * @return array
     */
    public function getAlts(): array
    {
        $alts = [];
        if ($this->getDomElements('img')->length > 0) {
            foreach ($this->getDomElements('img') as $img) {
                $alts[] = [
                    'src' => trim($img->getAttribute('src')),
                    'alt' => trim($img->getAttribute('alt'))
                ];
            }
        }
        return $alts;
    }

    /**
     * Removes all html tags and returns just text content.
     *
     * @return string
     */
    public function getText(): string
    {
        $this->removeTags('script');
        $this->removeTags('style');
        $text = strip_tags($this->dom->saveHTML());
        return preg_replace('!\s+!', ' ', strip_tags($text));
    }

    /**
     * Removes specified tags with it's content from DOM.
     *
     * @param string $tag
     */
    private function removeTags(string $tag)
    {
        $tagsToRemove = [];
        foreach ($this->getDomElements($tag) as $tag) {
            $tagsToRemove[] = $tag;
        }
        foreach ($tagsToRemove as $item) {
            $item->parentNode->removeChild($item);
        }
    }

    /**
     * Returns DOM elements by tag name.
     *
     * @param string $name
     * @return DOMNodeList|DOMElement[]
     */
    private function getDomElements(string $name): DOMNodeList
    {
        return $this->dom->getElementsByTagName($name);
    }
}
