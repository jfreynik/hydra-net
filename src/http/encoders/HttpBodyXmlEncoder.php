<?php

namespace hydra\net\http\encoders;

class HttpBodyXmlEncoder implements HttpBodyEncoderInterface
{
    
    public function encode (
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        die ("TODO - encode XML encoder");
    }
    
    public function decode (
        $body = "",
        $options = array ()
    ) {
        die ("TODO - decode XML encoder");
    }
    
}