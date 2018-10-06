<?php

namespace hydra\net\http;

use hydra\net\http\encoders\HttpBodyEncoders;

/**
 * HttpRequest
 * 
 * TODO - set class so the rawbody and rawhead are set when "get" is called
 * TODO - set class to reset the rawhead and rawbody anytime new data / files are added to the request
 * TODO - link all getters to pull from rawhead rawbody if available / otherwise build them
 * TODO - add temp file usage ...
 */
class HttpRequest implements HttpRequestInterface
{
    
    // internal property to track if the encoding was explicitly set
    protected $encset = false;
    
    protected $encoders = null;
    
    /*
    protected $dataEncoder = null;
    
    protected $fileEncoder = null;
    */
    
    protected static $defaults = array (
        "version"       => "HTTP/1.1",
        "method"        => "GET",
        "host"          => "localhost",
        "path"          => "/",
        
        // replaced by rawHead and rawBody below
        // "raw"           => "",
        
        "boundary"      => "",
        
        // "encType"       => null, -replaced with contentType
        "contentType"   => null,
        "transferEnc"   => "",
        "url"           => null,
        "scheme"        => null,
        "port"          => null,
        "proxy"         => null,
        "ssl"           => false,
        "headers"       => array (),
        "cookies"       => array (),
        "files"         => array (),
        "data"          => array (),
        
        "rawHead"       => "",
        "rawBody"       => "",
        
        // TODO - add temp file later
        "tmpfile"       => false,
    );
    
    protected $options = array ();
    
    public function __construct ($options = array ())
    {
        $this->encoders = new HttpBodyEncoders();
        $this->init($options);
    }
    
    public function init ($options = array())
    {
        $this->options = array_merge(self::$defaults, $options);
        
        if (!empty($this->options["url"])) {
            $this->setURL($this->options["url"]);
        }
        
        if (!empty($this->options["contentType"])) {
            $this->encset = true;
        } 
        
        else {
            
            if (empty($this->options["files"])) {
                $this->options["contentType"] = self::ENC_URL;
            } 
            
            else {
                $this->options["contentType"] = self::ENC_MULTI;
            }
        }
        
        if (!empty($this->options["raw"])) {
            $this->setRaw($this->options["raw"]);
        }
        
        return $this;
    }
    
    public function getBoundary ()
    {
        if (empty($this->options["boundary"])) {
            $boundary = str_repeat("=", 10).md5(microtime(true));
            $this->options["boundary"] = $boundary;
        }
        
        return $this->options["boundary"];
    }
    
    public function setBoundary ($boundary = "")
    {
        $this->options["boundary"] = $boundary;
        return $this;
    }
    
    public function getHttpBodyEncoders ()
    {
        return $this->encoders;
    }
    
    
    
    // HttpRequestBuilderInterface
    // ...
    
    public function getProxy ()
    {
        return $this->options["proxy"];
    }
    
    public function setProxy ($proxy = "")
    {
        $this->options["proxy"] = $proxy;
        return $this;
    }
    
    /*
    public function getTimeout ()
    {
        return $this->options["timeout"];
    }
    
    public function setTimeout ($timeout = self::TIMEOUT_DEFAULT)
    {
        $this->options["timeout"] = $timeout;
        return $this;
    }
    */
    
    public function getVersion ()
    {
        return $this->options["version"];
    }
    
    public function setVersion ($version = self::VER_1_1)
    {
        $this->options["version"] = $version;
        return $this;
    }
    
    /* move to http client class
    public function getVerifySSL ()
    {
        return $this->options["verifySSL"];
    }
    
    public function setVerifySSL ($verify = true)
    {
        $this->options["verifySSL"] = $verify;
        return $this;
    }
    */
    
    public function useSSL ($ssl = true)
    {
        $this->options["ssl"] = $ssl;
        if ($ssl && $this->getPort() === self::PORT_HTTP) {
            $this->setPort(self::PORT_HTTPS);
        }
        return $this;
    }
    
    public function getCookie ($cookie = "")
    {
        return isset($this->options["cookies"][$cookie]) ? 
            $this->options["cookies"][$cookie] : "";
    }
    
    public function setCookie ($cookie = "", $value = "", $options = array ())
    {
        // $options is not needed in the request
        $this->options["cookies"][$cookie] = $value;
        return $this;
    }
    
    public function unsetCookie ($cookie = "")
    {
        unset ($this->options["cookies"][$cookie]);
        return $this;
    }
    
    public function getCookies () 
    {
        return $this->options["cookies"];
    }
    
    public function setCookies ($cookies = array ())
    {
        $this->options["cookies"] = array_merge(
            $this->options["cookies"], $cookies);
        return $this;
    }
    
    public function unsetCookies ($cookies = array ())
    {
        if (empty($cookies)) {
            $this->cookies = array ();
        } else {
            foreach ($cookies as $key => $val) {
                if (isset($this->cookies[$key])) {
                    unset($this->cookies[$key]);
                }
            }
        }
        return $this;
    }
    
    public function getFile ($name = "")
    {
        return $this->options["files"][$name];
    }
    
    public function setFile ($name = "", $file = "")
    {
        $this->options["files"][$name] = $file;
        return $this;
    }
    
    public function unsetFile ($name = "")
    {
        unset($this->options["files"][$name]);
        return $this;
    }
    
    public function getFiles ()
    {
        return $this->options["files"];
    }
    
    public function setFiles ($files = array ())
    {
        $this->options["files"] = $files;
        return $this;
    }
    
    public function unsetFiles ($files = array ())
    {
        if (!empty($files)) {
            foreach ($files as $name => $file) {
                if (is_numeric($name)) {
                    foreach ($this->options["files"] as $key => $val) {
                        if ($val === $file) {
                            unset ($this->options["files"][$key]);
                        }
                    }
                } else if (isset($this->options["files"][$name])) {
                    unset ($this->options["files"][$name]);
                }
            }
        }
        
        else {
            $this->options["files"] = array ();
        }
        return $this;
    }
    
    public function getHeader ($name = "")
    {
        if ($name) {
            return $this->options["headers"][$name];
        }
        return $this->options["headers"];
    }
    
    public function setHeader ($name = "", $value = "")
    {
        $this->options["headers"][$name] = $value;
        return $this;
    }
    
    public function unsetHeader ($name = "")
    {
        if ($name) {
            unset($this->options["headers"][$name]);
        }
        
        else {
            $this->options["headers"] = array ();
        }
        return $this;
    }
    
    public function setHeadersRaw ($headers = "")
    {
        die ("TODO - set raw headers " . __FILE__);
    }

    public function getHeadersRaw ()
    {
        $op = $this->options;
        $rq = false;
        
        // TODO - add temp file functionality
        $tf = false;
        
        $op["method"] = strtoupper($op["method"]);
        $rq = array (
            "{$op["method"]} {$op["path"]}{$op["query"]} {$op["version"]}",
            "Host: {$op["host"]}",
        );
        
        // header data
        foreach ($op["headers"] as $key => $value) {
            $rq[] = (strpos($key, ":") === false) ?
                "{$key}: {$value}" : "{$key} {$value}";
        }
        
        // cookie data
        $cookies = array ("Cookie: ");
        foreach ($op["cookies"] as $name => $value) {
            $cookies[] = "{$name}={$value};";
        }
        
        if (1 < count($cookies)) {
            $rq[] = implode (" ", $cookies);
        }
        
        if (!empty($op["files"]) || !empty($op["data"])) {
            $length = $this->getContentLength();
            $rq[] = "Content-Type: {$op["contentType"]};" . 
                ($op["contentType"] === self::ENC_MULTI ? 
                    " boundary={$this->getBoundary()}" : "");
            $rq[] = "Content-Length: {$length}";
        }
        
        // should we always close the connection
        $rq[] = "Connection: close";
        $rq[] = "";
        
        return implode(self::CRLF, $rq);
    }
    
    public function getHeaders ()
    {
        return $this->options["headers"];
    }
    
    public function setHeaders ($headers = array ())
    {
        $this->options["headers"] = $headers;
        return $this;
    }
    
    public function unsetHeaders ($headers = array ())
    {
        $this->options["headers"] = array ();
        return $this;
    }
    
    public function getData ($name = "")
    {
        if (!$name) {
            return $this->options["data"];
        }
        return $this->options["data"][$name];
    }
    
    /*
    public function addData ($name = "", $value = "")
    {
        $this->options["data"][$name] = $value;
        return $this;
    }
    */
    
    public function setData ($name = "", $value = "")
    {
        if (is_array($name)) {
            $this->options["data"] = $name;
        } else {
            $this->options["data"][$name] = $value;
        }
        return $this;
    }
    
    public function unsetData ($name = "")
    {
        if (!$name) {
            $this->options["data"] = array ();
        } else {
            unset ($this->options["data"][$name]);
        }
        
        return $this;
    }
    
    public function getURL ()
    {
        if (!empty($this->options["url"])) {
            return $this->options["url"];
        }
        
        // build url from parts
        die("TODO - build URL from parts " . __FILE__);
        
        return "";
    }
    
    public function setURL ($url = "")
    {
        if (strpos($url, "http") !== 0 && 
            strpos($url, "//") !== 0
        ) {
            $url = "http://{$url}";
        }
        
        $this->options["url"] = $url;
        $url = parse_url($url);
        $url["query"] = empty($url["query"]) ? "" : "?{$url["query"]}";
        $url["path"] = empty($url["path"]) ? "/" : $url["path"];
        $url["port"] = empty($url["port"]) ? 
            (strtolower($url["scheme"]) === "https" ? 443 : 80) :
            ($url["port"]);
            
        foreach ($url as $key => $value) {
            $this->options[$key] = $value;
        }
        
        return $this;
    }
    
    public function getScheme ()
    {
        return $this->options["scheme"];
    }
    
    public function setScheme ($scheme = "")
    {
        $this->options["scheme"] = $scheme;
        return $this;
    }
    
    public function getPort ()
    {
        return $this->options["port"];
    }
    
    public function setPort ($port = self::PORT_HTTP)
    {
        $this->options["port"] = $port;
        return $this;
    }
    
    public function getHost ()
    {
        return $this->options["host"];
    }
    
    public function setHost ($host = self::LOCALHOST)
    {
        $this->options["host"] = $host;
        return $this;
    }
    
    public function getPath ()
    {
        return $this->options["path"];
    }
    
    public function setPath ($path = "/")
    {
        $this->options["path"] = $path;
        return $this;
    }
    
    public function getMethod ()
    {
        return $this->options["method"];
    }
    
    public function setMethod ($method = "")
    {
        $this->options["method"] = $method;
        return $this;
    }
    
    public function getContentType ()
    {
        return $this->options["contentType"];
    }
    
    public function setContentType ($enctype = self::ENC_URL)
    {
        $this->options["contentType"] = $enctype;
        $this->encset = true;
        return $this;
    }
    
    public function getTransferEncoding ()
    {
        return $this->options["transferEnc"];
    }
    
    public function setTransferEncoding ($encType = "")
    {
        $this->options["transferEnc"] = $encType;
        return $this;
    }
    
    public function getContentLength ()
    {
        $body = $this->getBodyRaw();
        return strlen($body);
    }
    
    public function getRaw ()
    {
        $headers = $this->getHeadersRaw();
        $body = $this->getBodyRaw();
        return $headers . self::CRLF . $body;
        
    }
    
    public function setRaw ($raw = "")
    {
        $this->options["raw"] = $raw;
        
        $eol = (
            strpos($raw, "\r") !== false && 
            strpos($raw, "\n") === false
        ) ? "\r" : "\n";
        
        $options = array ();
        while (strlen($raw)) {
            $pos = strpos($raw, $eol);
            $line = substr($raw, 0, $pos);
            $raw = substr($raw, strlen($line) + 1);
            
            // if (empty())
        }
        
        exit;
        
        // pull out important socket information
        preg_match("/^(.*) /", $raw, $method);
        
        var_dump($method);
        exit;
        
        preg_match("/Host: (.*)/", $raw, $host);
        
        return $this;
    }
    
    public function getOptions ()
    {
        return $this->options;
    }
    
    public function setOptions ($data = array ())
    {
        // is this needed?
        if (is_string($data)) {
            return $this->setRaw($data);
        }
        return $this->init ($data);
    }
    
    public function getBody ()
    {
        
        if (!empty($this->options["body"])) {
            return $this->options["body"];
        }
        
        $body = array ();
        
        if (!empty($this->options["data"])) {
            $body["data"] = $this->options["data"];
        }
        
        if (!empty($this->options["files"])) {
            $body["files"] = $this->options["files"];
        }
        
        return $body;
    }
    
    public function setBody (
        $data = array (),
        $files = array ()
    ) {
        
        /* - this calling style could cause confusion
        if ($data && 
            isset($data["data"]) && 
            isset($data["files"]) &&
            func_num_args() === 1
        ) {
            $this->options["data"] = $data["data"];
            $this->options["files"] = $data["files"];
        }
        */
        
        // if the function is called with a string - simply set as 
        // the raw body
        
        if (is_string($data)) {
            return $this->setBodyRaw($data);
        }
        
            
        if (isset($data)) {
            $this->options["data"] = $data;
        }
        
        if (isset($files)) {
            $this->options["files"] = $files;
        }
        
        return $this;
    }
    
    
    // TODO - look into transfer encoding
    public function getBodyRaw ($needsTransferEncoded = false)
    {
        $op = $this->options;
        
        if ($needsTransferEncoded) {
            die ("TODO - transfer encode " . __LINE__);
        }
        
        // if the raw body is set - return that
        if (isset($op["body"])) {
            return $op["body"];
        }
        
        return $this->encoders->encodeBody(
            $this->getContentType(),
            $op["data"],
            $op["files"],
            array (
                "boundary" => $this->getBoundary(),
            )
        );
    }
    
    
    /*
     * NOTE - getting the request body does not need to perform transfer 
     * encoding ... only getting the response body should do this.
     * BUT the raw body may need decoded when set - hence the reason for the
     * flag.
     */
    public function setBodyRaw ($body = "", $needsTransferDecoded = false)
    {
        $this->options["body"] = $body;
        
        if ($needsTransferDecoded) {
            $transferEncoding = $this->getTransferEncoder();
        }
        
        $contentType = $this->getContentType();
        
        if (strpos($contentType, "text/") !== false) {
            
            // if a text encoding is used - set the raw body and adjust the 
            // files and data keys.
            $this->options["body"] = $body;
            $this->options["data"] = array ();
            $this->options["files"] = array ();
        }
        
        else {
            
            $body = $this->decodeBody($body, $contentType);
            
            if (!empty($body["data"])) { 
                $this->options["data"] = $body["data"];
            }
            
            if (!empty($body["files"])) {
                $this->options["files"] = $body["files"];
            }
        }
        
        return $this;
    }
    
    public function __toString ()
    {
        return "HttpRequest ".spl_object_hash($this);
    }
    
}