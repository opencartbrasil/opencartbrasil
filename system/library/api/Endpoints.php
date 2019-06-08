<?php

namespace Api;

/**
 *Endpoints class
 */
class Endpoints implements EndpointsInterface
{

    /** @var object|Api */
    private $api;
    
    /** @var string */
    private $methods;

    /** @static */
    private static $file;

    public function __construct(string $endpoint, array $options = [], string $url_config = 'production')
    {
        self::$file = file_get_contents(__DIR__ . '/endpoints/' . $endpoint . '.json');

        $uri = self::get('base_uri')[$url_config];

        $this->api = new Api($uri, $options);
        
        $this->endpoints = self::get('endpoints');

        $this->map();
    }

   public function __call(string $method, array $args)
    {
        if (isset($this->methods[$method])) {
            return $this->methods[$method]($args[0]);
        } 
        
        throw new Exception('non existent endpoint');
    }

    /**
     * get method
     *
     * @param string $param to get json file property
     *
     * @return mixed
     */
    private static function get(string $key)
	{        
        $config = json_decode(self::$file, true);
        
        return isset($config[$key]) ? $config[$key] : false;
    }

    /**
     * map method
     *
     * call Request response from Guzzle Client
     *
     * @return mixed
     */    
    private function map()
    {
        $this->methods = array_map(function ($endpoint) {
            return function ($params) use ($endpoint) {
                $route = $this->getRoute($endpoint, $params);
                
                $assoc = isset($endpoint['assoc']);

                return $this->api->send((string)$endpoint['method'], $route, $params, $assoc);
            };
        }, $this->endpoints);
    }

    /**
     * getRoute method
     *
     * @param string $endpoint defined in json file
     * @param array  $params past with called method
     *
     * @return string
     */
    private function getRoute($endpoint, &$params)
    {
        $route = $endpoint['endpoint'];

        $placeholders = '/\:(\w+)/im';
        
        preg_match_all($placeholders, $route, $matches);
        
        $variables = $matches[1];

        foreach ($variables as $value) {
            if (isset($params[$value])) {
                
                $route = str_replace(':' . $value, $params[$value], $route);
                
                unset($params[$value]);
            }
        }

        return $route;
    }
}