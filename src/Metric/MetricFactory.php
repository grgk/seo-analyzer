<?php

namespace SeoAnalyzer\Metric;

use ReflectionException;

class MetricFactory
{
    /**
     * @param string $key
     * @param null $inputData
     * @return mixed
     * @throws ReflectionException
     */
    public static function get(string $key, $inputData = null)
    {
        $class = __NAMESPACE__;
        $path = explode(".", $key);
        foreach ($path as $level) {
            $class.= '\\' . ucfirst($level);
        }
        $class.= 'Metric';
        if (class_exists($class)) {
            return new $class($inputData);
        }
        throw new ReflectionException('Metric class ' . $class .' not exists');
    }
}
