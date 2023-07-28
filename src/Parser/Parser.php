<?php

namespace SeoAnalyzer\Parser;

class Parser extends AbstractParser
{
    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getTitle(): string
    {
        if ($this->getDomElements('title')->length > 0) {
            return trim($this->getDomElements('title')->item(0)->nodeValue);
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getAlts(): array
    {
        $alts = [];
        if ($this->getDomElements('img')->length > 0) {
            foreach ($this->getDomElements('img') as $img) {
                $alts[] = trim($img->getAttribute('alt'));
            }
        }
        return $alts;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        $this->removeTags('script');
        $this->removeTags('style');
        $text = strip_tags($this->dom->saveHTML());
        return preg_replace('!\s+!', ' ', strip_tags($text));
    }

    /**
     * @param $url
     * @param $formatSize
     * @param $useHead
     * @return array|int|mixed|string
     */
    private function getRemoteFilesize($url, $formatSize = true, $useHead = true)
    {
        if (false !== $useHead) {
            stream_context_set_default(array('http' => array('method' => 'HEAD')));
        }
        $head = array_change_key_case(get_headers($url, 1));
        // content-length of download (in bytes), read from Content-Length: field
        $clen = isset($head['content-length']) ? $head['content-length'] : 0;
        // cannot retrieve file size, return "-1"
        if (!$clen) {
            return -1;
        }

        if (!$formatSize) {
            return $clen; // return size in bytes
        }
        $data = [];
        $status = false;
        $size = $clen;
        switch ($clen) {
            case $clen < 1024:
                $size = $clen .' B';
                $status = true;
                break;
            case $clen < 1048576:
                $size = round($clen / 1024, 2) .' KiB';
                $status = true;
                break;
            case $clen < 1073741824:
                $size = round($clen / 1048576, 2) . ' MiB';

                break;
            case $clen < 1099511627776:
                $size = round($clen / 1073741824, 2) . ' GiB';

                break;
        }

        return $data = ['size'=>$size,'status'=>$status]; // return formatted size
    }

    /**
     * @param $url
     * @return array
     */
    public function getImg($url): array{
        $src = [];
        if ($this->getDomElements('img')->length > 0) {
            foreach ($this->getDomElements('img') as $key=> $img) {
                $parseUrl = parse_url($url);
                $attr = filter_var(trim($img->getAttribute('src')), FILTER_VALIDATE_URL);
                $source = trim($img->getAttribute('src'));
                //if(isset($parseUrl['scheme'])) {
                if(!strstr($source,'data:image')) {
                    if (($attr)) {
                        $dataImg = $this->getRemoteFilesize($source);
                        $src[$key]['src'] = $source;
                    } else {
                        $dataImg = $this->getRemoteFilesize($parseUrl['scheme'] . '://' . $parseUrl['host'] . $source);
                        $src[$key]['src'] = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $source;
                    }
                    $src[$key]['weight'] = $dataImg['size'];
                    $src[$key]['status'] = $dataImg['status'];//'Your image is optimized';
                }
            }
        }
        return $src;
    }
}
