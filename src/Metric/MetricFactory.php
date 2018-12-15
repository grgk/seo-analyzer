<?php

namespace SeoAnalyzer\Metric;

class MetricFactory
{
    /**
     * @param string $key
     * @param null $inputData
     * @return mixed
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
            $metric = new $class($inputData);
            if (empty($metric->name)) {
                throw new \InvalidArgumentException('Missing metric name for class: ' . $class);
            }
            return $metric;
        }
        throw new \ReflectionException('Metric class ' . $class .' not exists');
    }
}
