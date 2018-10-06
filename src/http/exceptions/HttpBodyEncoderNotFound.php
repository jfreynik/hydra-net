<?php

namespace hydra\net\exceptions;

class HttpBodyEncoderNotFound extends \Exception
{
    public function __construct ($encoderType = "")
    {
        parent::__construct (
            "Http Body Encoder Not Found: {$encoderType}");
    }
}