<?php

namespace SeoAnalyzer\Parser;

class ExampleCustomParser extends Parser
{
    /**
     * @inheritDoc
     */
    public function getAlts(): array
    {
        $alts = [];
        if ($this->getDomElements('img')->length > 0) {
            foreach ($this->getDomElements('img') as $img) {
                $alts[] = [
                    'alt' => trim($img->getAttribute('alt')),
                    'src' => trim($img->getAttribute('src')),
                ];
            }
        }
        return $alts;
    }
}
