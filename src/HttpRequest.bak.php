<?php

namespace hydra\net;

/**
 * Class to help with making HTTP requests.
 * 
 * Raw HTTP requests can be troublesome to build. 
 * 
 * @package hydra
 * @author JFreynik
 * @since 1.0
 */

class HttpRequest implements HttpConstants
{
    protected $request = "";
    
    protected $options = array ();
    
    protected $errors = array ();
    
    protected static $defaults = array (
        "version"   => "HTTP/1.1",
        "method"    => "GET",
        "enctype"   => null,
        "url"       => null,
        "scheme"    => null,
        "port"      => null,
        "host"      => "localhost",
        "path"      => "/",
        "proxy"     => null,
        "verifySSL" => true,
        "ssl"       => false,
        "debug"     => false,
        "headers"   => array (),
        "cookies"   => array (),
        "files"     => array (),
        "data"      => array (),
        "buffer"    => 4096,
        "timeout"   => 60,
        "tmpfile"   => false,
    );
    
    public function __construct ($options = array ())
    {
        $this->options = array_merge(self::$defaults, $options);
        $this->context = stream_context_create();
        
        // set URL if provided
        if (isset($this->options["url"])) {
            $this->setURL($this->options["url"]);
        }
        
    }
    
    public function getErrors ()
    {
        return $this->errors;
    }
    
    public function hasErrors ()
    {
        return !empty($this->errors);
    }
    
    public function getOptions ()
    {
        return $this->options;
    }
    
    public function setOptions ($options = array ())
    {
        $this->options = array_merge(self::$defaults, $options);
        return $this;
    }
    
    public function setOption ($key = "", $value = "")
    {
        $this->options[$key] = $value;
        return $this;
    }
    
    public function unsetOption ($key = "")
    {
        if (isset($this->options[$key])) {
            unset($this->options[$key]);
        }
        return $this;
    }
    
    public function getProxy ()
    {
        return $this->options["proxy"];
    }
    
    public function setProxy ($proxy = "")
    {
        $this->options["proxy"] = $proxy;
        return $this;
    }
    
    public function getURL ()
    {
        return $this->options["url"];
    }
    
    public function setURL ($url = "")
    {
        // clean up if missing protocol
        $url = trim($url);
        if (strpos($url, "http") !== 0) {
            $url = "http://{$url}";
        }
        $this->options["url"] = $url;
        $url = parse_url($url);
        
        // set the different url components
        $url["query"] = empty($url["query"]) ? 
            "" : "?{$url["query"]}";
        $url["path"] = empty($url["path"]) ? 
            "/" : $url["path"];
        $url["port"] = (empty($url["port"]) ? 
            (strtolower($url["scheme"]) === "https" ? 443 : 80) : 
            $url["port"]);
        
        // add to the options
        foreach ($url as $key => $value) {
            $this->options[$key] = $value;
        }
        return $this;
    }
    
    
    public function getTimeout () 
    {
        return $this->options["timeout"];
    }
    
    public function setTimeout ($timeout = 60) 
    {
        $this->options["timeout"] = $timeout;
        return $this;
    }
    
    public function getVersion () 
    {
        return $this->options["version"];
    }
    
    public function setVersion ($version = "") 
    {
        $this->options["version"] = $version;
        return $this;
    }
    
    public function getVerifySSL () 
    {
        return $this->options["verifySSL"];
    }
    
    public function setVerifySSL ($verify = true) 
    {
        $this->options["verifySSL"] = $verify;
        return $this;
    }
    
    public function getCookie ($name = "") 
    {
        return $this->options["cookies"][$name];
    }
    
    public function getCookies () 
    {
        return $this->options["cookies"];
    }
    
    public function setCookie ($name = "", $value = "") 
    {
        if ($value === null) {
            unset($this->options["cookies"][$name]);
        } else {
            $this->options["cookies"][$name] = $value;
        }
        return $this;
    }
    
    public function setCookies ($cookies = array ())
    {
        foreach ($cookies as $cookie => $value) {
            $this->setCookie($cookie, $value);
        }
        return $this;
    }
    
    public function unsetCookie ($name = "")
    {
        if (isset($this->options["cookies"][$name])) {
            unset($this->options["cookies"][$name]);
        }
        return $this;
    }
    
    public function getEncType () 
    {
        if (empty($this->options["enctype"])) {
            if (empty($this->options["files"])) {
                return self::ENC_URL;
            }
            return self::ENC_MULTI;
        }
        return $this->options["enctype"];
    }
    
    public function setEncType ($type = "")
    {
        $this->options["enctype"] = $type;
        return $this;
    }
    
    public function getFile ($name = "")
    {
        return isset($this->options["files"][$name]) ?
            $this->options["files"][$name] : "";
    }
    
    public function setFile ($name = "", $file = "")
    {
        $this->options["files"][$name] = $file;
        return $this;
    }
    
    public function getFiles ($enctype = null) {
        if ($enctype) {
            
        }
        return $this->options["files"];
    }
    
    public function unsetFile ($name = "")
    {
        unset($this->options["files"][$name]);
        return $this;
    }
    
    public function reset ()
    {
        
    }
    
    /**
     * Build HTTP request from provided options.
     */
    public function getRequest ()
    {
        if (empty($this->request)) {
            $op = $this->options;
            $op["method"] = strtoupper($op["method"]);
            
            // init request
            $rq = array (
                "{$op["method"]} {$op["path"]}{$op["query"]} {$op["version"]}",
                "Host: {$op["host"]}",
            );
            
            // add custom headers
            foreach ($op["headers"] as $key => $value) {
                $rq[] = (strpos($key, ":") === false) ? 
                    "{$key}: {$value}" : "{$key} {$value}";
            }
            
            // add any cookie data
            $cookies = array ("Cookie: ");
            foreach ($op["cookies"] as $key => $value) {
                $cookies[] = "{$name}={$value}; ";
            }
            if (1 < count($cookies)) {
                $rq[] = implode("", $cookies);
            }
            
            // build the body of the request if present
            $body = array ();
            if (!empty($op["data"])) {
                
            }
            
            if (!empty($op["files"])) {
                
            }
        }
        return $this->request;
    }
    
    /**
     * Encode an array of data into an acceptable HTTP format.
     */
    public function encodeData (
        $data = array (), 
        $encoding = self::ENC_URL
    ) {
        if (!is_array($data)) {
            return $data;
        }
        
        switch ($encoding) {
            case self::ENC_MULTI:
                $ret = array ();
                $bnd = $this->getMultiBoundary();
                foreach ($data as $name => $value) {
                    if (is_array($value)) {
                        if (strpos($name, "[]") === false) {
                            $name = "{$name}[]";
                        }
                        foreach ($value as $i => $val) {
                            $ret[] = $bnd;
                            $ret[] = "Content-Disposition: form-data; name=\"{$name}\"";
                            $ret[] = "";
                            $ret[] = $val;
                        }
                    } else {
                        $ret[] = $bnd;
                        $ret[] = "Content-Disposition: form-data; name=\"{$name}\"";
                        $ret[] = "";
                        $ret[] = $value;
                    }
                }
                return implode (self::CRLF, $ret);
                break;
            case self::ENC_PLAIN:
                return var_export($data);
            case self::ENC_JSON:
                return json_encode($data);
            case self::ENC_URL:
                return http_build_query($data);
            default:
                $this->errors[] = 
                    "Unknown encoding used for data: [{$encoding}]";
                return false;
        }
    }
    
    /**
     * encodeFiles
     * 
     * Encode an array of files into an acceptable HTTP format.
     */
    public function encodeFiles (
        $files = array (),
        $encoding = self::ENC_MULTI
    ) {
        if (!is_array($files)) {
            return $files;
        }
        
        switch ($encoding) {
            case ENC_MULTI:
                $bnd = $this->getMultiBoundary();
                $ret = array ();
                foreach ($files as $name => $file) {
                    if (is_array($file)) {
                        if (strpos($name, "[]") === false) {
                            $name = "{$name}[]";
                        }
                        foreach ($file as $i => $f) {
                            $fname = basename($f);
                            $mime = mime_content_type($f);
                            $ret[] = $bnd;
                            $ret[] = "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$fname}\"";
                            $ret[] = "Content-Type: {$mime}";
                            $ret[] = "";
                            $ret[] = file_get_contents($f);
                        }
                    } else {
                        $fname = basename($file);
                        $mime = mime_content_type($file);
                        $ret[] = $bnd;
                        $ret[] = "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$fname}\"";
                        $ret[] = "Content-Type: {$mime}";
                        $ret[] = "";
                        $ret[] = file_get_contents($file);
                    }
                }
                
                return implode(self::CRLF, $ret);
            case ENC_PLAIN:
                
                return;
            case ENC_JSON:
                
                return;
            case ENC_URL:
            default:
                
                return;
        }
    }
    
    public function getMultiBoundary ()
    {
        static $boundary;
        if (!$boundary) {
            $boundary = "-----------------------------".
                md5(microtime(true));
        }
        return $boundary;
    }
    
    /**
     * Set raw HTTP request.
     */
    public function setRequest ($request = "")
    {
        $this->request = $request;
        return $this;
    }
    
    /**
     * Execute the request.
     */
    public function execute ()
    {
        $rq = $this->getRequest();
        
        
    }
    
    
    // Helpers
    
    public static function get ($options = array ())
    {
        return self::custom ("GET", $options);
    }
    
    public static function post ($options = array ())
    {
        return self::custom("POST", $options);
    }
    
    public static function patch ($options = array ())
    {
        return self::custom("PATCH", $options);
    }
    
    public static function put ($options = array ())
    {
        return self::custom("PUT", $options);
    }
    
    public static function delete ($options = array ())
    {
        return self::CUSTOM("DELETE", $options);
    }
    
    public static function head ($options = array ())
    {
        return self::custom("HEAD", $options);
    }
    
    public static function custom (
        $type = "", 
        $options = array ()
    ) {
        $req = new HttpRequest($options);
        return $req->execute();
    }
    
    
}