<?php

namespace hydra\net;

/**
 * HttpBuilder
 * 
 * This class will build a raw http request from the data provided.
 * 
 * TODO: allow class to populate getters from setRaw method
 */
class HttpRequestBuilder implements HttpRequestBuilderInterface
{
    
    protected $version = "0.0.1";
    
    // Was the encoding explicitly set
    protected $encset = false;
    
    protected static $defaults = array (
        "version"   => "HTTP/1.1",
        "method"    => "GET",
        "host"      => "localhost",
        "path"      => "/",
        "raw"       => "",
        "boundary"  => "",
        "enctype"   => null,
        "url"       => null,
        "scheme"    => null,
        "port"      => null,
        "proxy"     => null,
        "ssl"       => false,
        "headers"   => array (),
        "cookies"   => array (),
        "files"     => array (),
        "data"      => array (),
        // "tmpfile"   => false,
    );
    
    protected $options = array ();
    
    public function __construct ($options = array ())
    {
        $this->init($options);
    }
    
    public function init ($options = array())
    {
        $this->options = array_merge(self::$defaults, $options);
        
        if (!empty($this->options["url"])) {
            $this->setURL($this->options["url"]);
        }
        
        if (!empty($this->options["enctype"])) {
            $this->encset = true;
        }
        
        if (!empty($this->options["raw"])) {
            $this->setRaw($this->options["raw"]);
        }
        
        return $this;
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
    
    // move to http request class
    public function getVerifySSL ()
    {
        return $this->options["verifySSL"];
    }
    
    public function setVerifySSL ($verify = true)
    {
        $this->options["verifySSL"] = $verify;
        return $this;
    }
    
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
    
    public function setCookie ($cookie = "", $value = "")
    {
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
        return $this->options["data"][$name];
    }
    
    public function addData ($name = "", $value = "")
    {
        $this->options["data"][$name] = $value;
        return $this;
    }
    
    public function setData ($data = array ())
    {
        $this->options["data"] = $data;
        return $this;
    }
    
    public function unsetData ($name = "")
    {
        unset ($this->options["data"][$name]);
        return $this;
    }
    
    public function getURL ()
    {
        if (!empty($this->options["url"])) {
            return $this->options["url"];
        }
        
        // build url from parts
        
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
    
    public function getEncType ()
    {
        return $this->options["enctype"];
    }
    
    public function setEncType ($enctype = self::ENC_URL)
    {
        $this->options["enctype"] = $enctype;
        $this->encset = true;
        return $this;
    }
    
    public function getRaw ()
    {
        // build the request
        if (!empty($this->options["raw"])) {
            return $this->options["raw"];    
        }
        
        $rn = self::CRLF;
        $op = $this->options;
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
            $cookies[] = "{$name}={$value}; ";
        }
        
        if (1 < count($cookies)) {
            $rq[] = implode ("", $cookies);
        }
        
        // create the body
        $body = array ();
        
        // if we have a request bode we need to check encoding types
        if (!empty($op["data"]) || !empty($op["files"])) {
            
            if (empty($op["enctype"]) && 
                !$this->encset
            ) {
                // for now set to url encoded if no files
                if (empty($op["files"])) {
                    // $this->options["enctype"] = 
                        $op["enctype"] = self::ENC_URL;
                }
                // otherwise multi encoded
                else {
                    // $this->options["enctype"] = 
                        $op["enctype"] = self::ENC_MULTI;
                }
            }
            
            // do we need a boundary
            if ($op["enctype"] === self::ENC_MULTI && 
                empty($op["boundary"])
            ) {
                // $this->options["boundary"] = 
                    $op["boundary"] = md5(microtime(true));
            }
        
            if (!empty($op["data"]) && (
                $data = HttpHelper::encodeData(
                    $op["data"], 
                    $op["enctype"], 
                    $op["boundary"]
                ))
            ) {
                $body[] = $data;
            }
            
            if (!empty($op["files"]) && (
                $data = HttpHelper::encodeFiles(
                    $op["files"],
                    $op["enctype"],
                    $op["boundary"]
                ))
            ) {
                $body[] = $data;
            }
        }
        
        if (!empty($body)) {
            
            // use single root element for json request
            if ($op["enctype"] === self::ENC_JSON &&
                count($body) === 2
            ) {
                // if data and files being sent through json
                // the two payloads need merged
                $body[0] = substr($body[0], 0, strrpos($body[0], "}")) . ",";
                $body[1] = substr($body[1], strpos($body[1], "{") + 1);
            }
            
            $body = implode(self::CRLF, $body);
            $ln = strlen($body);
            $rq[] = "Content-Type: {$op["enctype"]};" . 
                ($op["enctype"] === self::ENC_MULTI ? 
                    " boundary={$op["boundary"]}" : "");
            $rq[] = "Content-Length: {$ln}";
        }
        
        $rq[] = "Connection: close";
        $rq[] = "";
        
        if (!empty($body)) {
            $rq[] = $body;
        } else {
            $rq[] = "";
        }
        
        return implode(self::CRLF, $rq);
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
    
    public function getRequest ()
    {
        return $this->options;
    }
    
    public function setRequest ($data = array ())
    {
        if (is_string($data)) {
            return $this->setRaw($data);
        }
        return $this->init ($data);
    }
    
}