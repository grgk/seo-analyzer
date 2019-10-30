<?php

namespace Tests\TestCase\Metric\Mock;

use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

class TestCacheAdapter extends FilesystemCache
{
    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        throw new InvalidArgumentException();
    }
}
