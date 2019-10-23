<?php

namespace SeoAnalyzer\Parser;

interface ParserInterface
{
    /**
     * Sets html doc content to be parsed.
     *
     * @param $html
     */
    public function setContent($html): void;

    /**
     * Returns document meta headers content.
     *
     * @return array
     */
    public function getMeta(): array;

    /**
     * Returns document headers content.
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Returns page title content.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns alt attributes of img tags.
     *
     * @return array
     */
    public function getAlts(): array;

    /**
     * Returns plain text content without html tags.
     *
     * @return string
     */
    public function getText(): string;
}
