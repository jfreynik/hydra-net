<?php

namespace hydra\net\http\api;

use hydra\net\http\SimpleHttpClient;

class ClosureCompiler
{
    protected $client;

    protected $options;

    public function __construct ($options = array ())
    {
        $this->client = new SimpleHttpClient();
    }

    public function getClient ()
    {
        return $this->client;
    }

    public function compile ($options = array ())
    {
        if (is_string($options)) {
            $options = array (
                "code" => $options
            );
        }
    }

}