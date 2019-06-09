<?php

namespace Api;

/**
 *Endpoints class
 */
interface EndpointsInterface
{
    /**
     * method call magic
     *
     * @param string $method defined in json file
     * @param array  $args past arguments with called method
     */
    public function __call(string $method, array $args);
}