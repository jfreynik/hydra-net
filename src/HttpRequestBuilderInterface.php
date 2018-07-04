<?php

namespace hydra\net;

interface HttpRequestBuilderInterface extends HttpInterface
{
    
    /* 
    Implementation Detail - not needed in the interface
    public function getOption ($option = "");
    public function setOption ($option = "", $value = "");
    public function unsetOption ($option = "");
    
    public function getOptions ();
    public function setOptions ($options = array ());
    public function unsetOptions ($options = array ());
    
    public function getDebug ();
    public function setDebug ($debug = true);
    */
    
    public function getProxy ();
    public function setProxy ($proxy = "");
    
    /* implementation details
    public function getTimeout ();
    public function setTimeout ($timeout = self::TIMEOUT_DEFAULT);
    */
    
    public function getVersion ();
    public function setVersion ($version = self::VER_1_1);
    
    /* implementation details
    public function getVerifySSL ();
    public function setVerifySSL ($verify = true);
    public function useSSL ($use = true);
    
    public function getTempFile ();
    public function setTempFile ();
    public function useTempFile ();
    */
    
    public function getCookie ($cookie = "");
    public function setCookie ($cookie = "", $value = "");
    public function unsetCookie ($cookie = "");
    
    public function getCookies ();
    public function setCookies ($cookies = array ());
    public function unsetCookies ($cookies = array ());
    
    public function getFile ($name = "");
    public function setFile ($name = "", $file = "");
    public function unsetFile ($name = "");
    
    public function getFiles ();
    public function setFiles ($files = array ());
    public function unsetFiles ($files = array ());
    
    public function getHeader ($name = "");
    public function setHeader ($name = "", $value = "");
    public function unsetHeader ($name = "");
    
    public function getHeaders ();
    public function setHeaders ($headers = array ());
    public function unsetHeaders ($headers = array ());
    
    public function getData ($name = "");
    public function addData ($name = "", $value = "");
    public function setData ($data = array ());
    public function unsetData ($name = "");
    
    public function getURL ();
    public function setURL ($url = "");
    
    public function getScheme ();
    public function setScheme ($scheme = "");
    
    public function getPort ();
    public function setPort ($port = self::PORT_HTTP);
    
    public function getHost ();
    public function setHost ($host = self::LOCALHOST);
    
    public function getPath ();
    public function setPath ($path = "/");
    
    public function getRaw ();
    public function setRaw ($raw = "");
    
    public function getMethod ();
    public function setMethod ($method = "");
    
    public function getEncType ();
    public function setEncType ($enctype = self::ENC_URL);
    
    public function getRequest ();
    public function setRequest ($data = array ());
    
}