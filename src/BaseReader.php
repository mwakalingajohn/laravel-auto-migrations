<?php

namespace MwakalingaJohn\LaravelAutoMigrations;

abstract class BaseReader{

    /**
     * Get the namespace
     */
    abstract protected function getNamespace();

    /**
     * Iterate through the directory files, and get file names
     * @return array()
     */
    public function fileNames($directory)
    {
        return array_diff(scandir($directory), array('..', '.'));
    }

    /**
     * Remove .php extension to remain with resolvable class name
     */
    public function getName($file)
    {
        return str_replace(".php", "", $file);
    }

    /**
     * Resolve class from the namespace
     */
    public function resolve($class_name)
    {
        $class = $this->getNamespace().$class_name;
        return new $class;
    }
}
