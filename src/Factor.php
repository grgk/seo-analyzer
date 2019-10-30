<?php

namespace SeoAnalyzer;

class Factor
{
    const PAGE = 'page';
    const PARSED = 'parsed';
    const PATH = 'path';
    const HOST = 'host';
    const LENGTH = 'length';
    const SSL = 'SSL';
    const LOAD_TIME = 'loadTime';
    const REDIRECT = 'redirect';
    const HEADERS = 'headers';
    const TEXT = 'text';
    const ALTS = 'alts';
    const HTML = 'html';
    const SIZE = 'size';
    const RATIO = 'ratio';
    const DESCRIPTION = 'description';

    const URL = 'url';
    const URL_PARSED = self::URL . '.' . self::PARSED;
    const URL_PARSED_PATH = self::URL . '.' . self::PARSED . '.' . self::PATH;
    const URL_PARSED_HOST = self::URL . '.' . self::PARSED . '.' . self::HOST;
    const URL_LENGTH = self::URL . '.' . self::LENGTH;

    const META = 'meta';
    const TITLE = 'title';
    const META_META = self::META . '.' . self::META;
    const META_TITLE = self::META . '.' . self::TITLE;
    const META_DESCRIPTION = self::META . '.' . self::DESCRIPTION;

    const CONTENT = 'content';
    const CONTENT_HTML = self::CONTENT . '.' . self::HTML;
    const CONTENT_SIZE = self::CONTENT . '.' . self::SIZE;
    const CONTENT_RATIO = self::CONTENT . '.' . self::RATIO;

    const DENSITY = 'density';
    const DENSITY_PAGE = self::DENSITY . '.' . self::PAGE;
    const DENSITY_HEADERS = self::DENSITY . '.' . self::HEADERS;

    const KEYWORD = 'keyword';
    const KEYWORDS = 'keywords';
    const KEYWORD_URL = self::KEYWORD . '.' . self::URL;
    const KEYWORD_PATH = self::KEYWORD . '.' . self::PATH;
    const KEYWORD_TITLE = self::KEYWORD . '.' . self::TITLE;
    const KEYWORD_DESCRIPTION = self::KEYWORD . '.' . self::DESCRIPTION;
    const KEYWORD_HEADERS = self::KEYWORD . '.' . self::HEADERS;
    const KEYWORD_DENSITY = self::KEYWORD . '.' . self::DENSITY;
}
