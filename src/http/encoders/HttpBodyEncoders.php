<?php

namespace hydra\net\http\encoders;

use hydra\net\http\exceptions\HttpBodyEncoderNotFound;
use hydra\net\http\HttpConstants;

class HttpBodyEncoders implements HttpConstants
{
    
    protected $encoders = array ();
    
    public function __construct ()
    {
        // register all default body encoders
        // ...
        
        $this->registerBodyEncoder (self::ENC_TEXT_PLAIN, 
            new HttpBodyPlainEncoder ());
            
        $this->registerBodyEncoder (self::ENC_URL,
            new HttpBodyUrlEncoder ());
            
        $this->registerBodyEncoder (self::ENC_MULTI,
            new HttpBodyMultipartEncoder ());
            
        $this->registerBodyEncoder (self::ENC_JSON,
            new HttpBodyJsonEncoder ());
            
        $this->registerBodyEncoder (self::ENC_TEXT_JSON,
            new HttpBodyJsonEncoder ());
            
        $this->registerBodyEncoder (self::ENC_XML,
            new HttpBodyXmlEncoder ());
            
        $this->registerBodyEncoder (self::ENC_TEXT_XML,
            new HttpBodyXmlEncoder ());
            
        $this->registerBodyEncoder (self::ENC_B64URL,
            new HttpBodyBase64UrlEncoder ());
            
        // register transfer encoders
        $this->registerBodyEncoder (self::ENC_TRN_CHUNKED,
            new HttpBodyChunkedEncoder ());
    }
    
    public function getBodyEncoder ($type = "")
    {
        return empty($this->encoders[$type]) ? null : 
            $this->encoders[$type];
    }
    
    public function setBodyEncoder (
        $type = "",
        HttpBodyEncoderInterface $encoder
    ) {
        $this->encoders[$type] = $encoder;
        return $this;
    }
    
    public function registerBodyEncoder (
        $type = "",
        HttpBodyEncoderInterface $encoder
    ) {
        $this->encoders[$type] = $encoder;
        return $this;
    }
    
    public function deregisterBodyEncoder (
        $type = ""
    ) {
        unset($this->encoders[$type]);
        return $this;
    }
    
    public function unsetBodyEncoder (
        $type = ""
    ) {
        unset($this->encoders[$type]);
        return $this;
    }
    
    public function encodeBody (
        $type = "", 
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        if (!isset($this->encoders[$type])) {
            throw new HttpBodyEncoderNotFound($type);
        }
        
        $encoder = $this->encoders[$type];
        return $encoder->encode($data, $files, $options);
    }
    
    public function decodeBody (
        $type = "",
        $body = "",
        $options = array ()
    ) {
        if (!isset($this->encoders[$type])) {
            throw new HttpBodyEncoderNotFound($type);
        }
        
        $encoder = $this->encoders[$type];
        return $encoder->decode($body, $options);
    }
    
    public function hasBodyEncoder ($type = "")
    {
        return isset($this->encoders[$type]);
    }
    
}