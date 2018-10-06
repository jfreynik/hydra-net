<?php

namespace hydra\net\http;

interface SimpleHttpClientInterface extends HttpConstants
{
    public function __construct ($options = array ());
    
    public function init ($data = array ());
    
    public function execute (HttpRequest $request);
    
    public function getVerifySSL ();
    
    public function setVerifySSL ($verify = true);
    
    // TODO
    // public function useTempFile ($temp = true);
    
    public function setProxy ($proxy = "");
    
    public function getProxy ();
    
    public function setTimeout ($timeout = self::TIMEOUT_DEFAULT);
    
    public function getTimeout ();
    
    public function getMaxRedirects ();
    
    public function setMaxRedirects ($max = self::MAX_REDIRECTS);
    
    public function setBuffer ($buff = self::BUFFER_DEFAULT);
    
    public function getBuffer ();
    
}