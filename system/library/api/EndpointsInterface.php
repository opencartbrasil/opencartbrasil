<?php

namespace Api;

/**
 *Endpoints class
 */
 interface EndpointsInterface
{

    /**
     * construct method
     *
     * @param string $path Api Provider Path
     * @param array  $options Options params
     * @param string $url_config Type production or sandbox
     */
    public function __construct(string $path, array $options = [], string $url_config = 'production');

    /**
     * method call magic
     *
     * @param string $method defined in json file
     * @param array  $args past arguments with called method
     */
    public function __call(string $method, array $args);

}
?>