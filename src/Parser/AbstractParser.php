<?php

namespace SeoAnalyzer\Parser;

use DOMDocument;
use DOMElement;
use DOMNodeList;

abstract class AbstractParser implements ParserInterface
{
    /**
     * @var DOMDocument Dom representation of HTML document
     */
    protected $dom;

    /**
     * @param string $html Html document to parse.
     */
    public function __construct(string $html = null)
    {
        $this->dom = new DOMDocument();
        if (!empty($html)) {
            $this->setContent($html);
        }
    }

    /**
     * @inheritDoc
     */
    public function setContent($html): void
    {
        $internalErrors = libxml_use_internal_errors(true);
        $this->dom->loadHTML($html, LIBXML_NOWARNING);
        libxml_use_internal_errors($internalErrors);
    }

    /**
     * Removes specified tags with it's content from DOM.
     *
     * @param string $tag
     */
    protected function removeTags(string $tag)
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
    protected function getDomElements(string $name): DOMNodeList
    {
        return $this->dom->getElementsByTagName($name);
    }
}
