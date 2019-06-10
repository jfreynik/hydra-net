<?php

namespace hydra\net\http;

interface HttpRequestInterface extends HttpConstants
{
 
    /* 
    Implementation Detail - not needed in the interface
    Will be used in any working client
    =======================================================
    public function getOption ($option = "");
    public function setOption ($option = "", $value = "");
    public function unsetOption ($option = "");
    public function getOptions ();
    public function setOptions ($options = array ());
    public function unsetOptions ($options = array ());
    public function getDebug ();
    public function setDebug ($debug = true);
    public function getProxy ();
    public function setProxy ($proxy = "");
    public function getTimeout ();
    public function setTimeout ($timeout = self::TIMEOUT_DEFAULT);
    public function getVerifySSL ();
    public function setVerifySSL ($verify = true);
    public function useSSL ($use = true);
    
    // TODO - add these functions into request and response object.
    // ============================================================
    public function getTempFile (); -
    public function setTempFile ();
    public function useTempFile ();
    */
    
    /**
    * Returns the version of HTTP that the request will use.
    */
    public function getVersion ();
    
    /**
    * Set the version of HTTP that the request will use.
    */
    public function setVersion ($version = self::VER_1_1);
    
    /**
    * Returns the requested cookie from the request/response.
    */
    public function getCookie ($cookie = "");
    
    /**
    * Set the requested cookie in the request/response
    */
    public function setCookie ($cookie = "", $value = "", $options = array ());
    
    /**
    * Unset a cookie from the request/response.
    */
    public function unsetCookie ($cookie = "");
    
    /**
    * Returns all of the set cookies from the request/response.
    */
    public function getCookies ();
    
    /**
    * Set multiple cookies at once.
    */
    public function setCookies ($cookies = array ());
    
    /**
    * Unset multiple cookies at once.
    */
    public function unsetCookies ($cookies = array ());
    
    /**
    * Returns the requested file from the request/response.
    */
    public function getFile ($name = "");
    
    /**
    * Sets the requsrted file in the request/respnse.
    */
    public function setFile ($name = "", $file = "");
    
    /**
    * Unsets the requested file from the request/response.
    */
    public function unsetFile ($name = "");
    
    /**
    * Returns all of the files within the request/response.
    */
    public function getFiles ();
    
    /**
    * 
    */
    public function setFiles ($files = array ());
    
    /**
    * 
    */
    public function unsetFiles ($files = array ());
    
    /**
    * 
    */
    public function getHeader ($name = "");
    
    /**
    * 
    */
    public function setHeader ($name = "", $value = "");
    
    /**
    * 
    */
    public function unsetHeader ($name = "");
    
    /**
    * 
    */
    public function getHeaders ();
    
    /**
    * 
    */
    public function setHeaders ($headers = array ());
    
    /**
    * 
    */
    public function unsetHeaders ($headers = array ());
    
    /**
    * 
    */
    public function getHeadersRaw ();
    
    /**
    * 
    */
    public function setHeadersRaw ($headers = "");
    
    /**
    * 
    */
    public function getData ($name = "");
    
    
    /**
    * 
    */
    public function setData ($name = "", $value = "");
    
    /**
    * 
    */
    public function unsetData ($name = "");
    
    /**
    * 
    */
    public function getURL ();
    
    /**
    * 
    */
    public function setURL ($url = "");
    
    /**
    * 
    */
    public function getScheme ();
    
    /**
    * 
    */
    public function setScheme ($scheme = "");
    
    /**
    * 
    */
    public function getPort ();
    
    /**
    * 
    */
    public function setPort ($port = self::PORT_HTTP);
    
    /**
    * 
    */
    public function getHost ();
    
    /**
    * 
    */
    public function setHost ($host = self::LOCALHOST);
    
    /**
    * 
    */
    public function getPath ();
    
    /**
    * 
    */
    public function setPath ($path = "/");
    
    /**
    * 
    */
    public function getRaw ();
    
    /**
    * 
    */
    public function setRaw ($raw = "");
    
    /**
    * 
    */
    public function getMethod ();
    
    /**
    * 
    */
    public function setMethod ($method = "");
    
    /**
    * 
    */
    public function getContentType ();
    
    /**
    * 
    */
    public function setContentType ($enctype = self::ENC_URL);
    
    /**
     * 
     */
    public function getTransferEncoding ();
    
    /**
     * 
     */
    public function setTransferEncoding ($encType = "");
    
    /**
    * 
    */
    public function getContentLength ();
    
    /**
    * 
    * /
    public function getEncType ();
    
    /**
    * 
    * /
    public function setEncType ($enctype = self::ENC_URL);
    */
    
    /**
    * 
    */
    public function getOptions ();
    
    /**
    * 
    */
    public function setOptions ($data = array ());
    
    /**
    * 
    */
    public function getBoundary ();
    
    /**
    * 
    */
    public function setBoundary ($boundary = "");
    
    /**
    * 
    */
    public function getBody ();
    
    /**
    * 
    */
    public function setBody ($data = array (), $files = array ());
    
    /**
    * 
    */
    public function getBodyRaw ($needsTransferEncoded = false);
    
    /**
    * 
    */
    public function setBodyRaw ($body = "", $needsTransferDecoded = false);


    // NEWLY ADDED

    public function setParams ($params = array ());

    public function getParams ();

    public function setParam ($name = "", $data = "");

    public function getParam ($name = "");

}