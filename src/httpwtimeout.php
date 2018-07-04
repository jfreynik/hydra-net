<?php

/**
 * This class uses PHP Sockets to make HTTP requests over the internet. 
 * This can be useful because not all hosting environments have cURL
 * enabled.
 *
 */

class HTTP {
    
    const CRLF = "\r\n";
    
    const ENC_PLAIN = "text/plain";
    
    const ENC_MULTI = "multipart/form-data";
    
    const ENC_JSON = "application/json";
    
    const ENC_URL = "application/x-www-form-urlencoded";
    
    const METHOD_PATCH = "PATCH";
    
    const METHOD_POST = "POST";
    
    const METHOD_PUT = "PUT";
    
    const METHOD_HEAD = "HEAD";
    
    const METHOD_GET = "GET";
    
    const METHOD_DELETE = "DELETE";
    
    protected $encset = false;
    
    protected $errors = array();
    
    protected $options = array (
        "enctype"   => "application/x-www-form-urlencoded",
        "boundary"  => "--------------------hydra-001",
        "version"   => "HTTP/1.1",
        "method"    => "GET",
        "url"       => "",
        "scheme"    => "",
        "host"      => "localhost",
        "port"      => "",
        "path"      => "/",
        "verifySSL" => false,
        "ssl"       => false,
        "debug"     => false,
        "headers"   => array (
            "User-Agent" => "HydraHTTP", //< fyi google redirects by user agent
        ),
        "cookies"   => array (),
        "files"     => array (),
        "data"      => array (),
        "buffer"    => 4096,
        "timeout"   => 60,
    );
    
    
    public function __construct ($options = array ()) {
        $this->options["boundary"] .= md5(microtime(true));
        $this->options = array_merge ($this->options, $options);
        
        if (!empty($this->options["url"])) {
            $this->setURL($this->options["url"]);
        }
    }
    
    public function getTimeout () {
        return $this->options["timeout"];
    }
    
    public function setTimeout ($timeout = 60) {
        $this->options["timeout"] = $timeout;
        return $this;
    }
    
    public function getVersion () {
        return $this->options["version"];
    }
    
    public function setVersion ($version = "") {
        $this->options["version"] = $version;
        return $this;
    }
    
    public function getVerifySSL () {
        return $this->options["verifySSL"];
    }
    
    public function setVerifySSL ($verify = true) {
        $this->options["verifySSL"] = $verify;
        return $this;
    }
    
    public function getURL () {
        return $this->payload["url"];
    }
    
    public function setQuery ($params = array()) {
        $this->options["query"] = http_build_query($params);
        return $this;
    }
    
    public function getQuery () {
        return $this->options["query"];
    }
    
    public function setURL ($url = "") {
        $this->payload["url"] = $url;
        $url = parse_url($url);
        // fix some of the url elements
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
    
    public function getCookie ($name = "") {
        return $this->options["cookies"][$name];
    }
    
    public function setCookie ($name = "", $value = "") {
        if ($value === null) {
            unset($this->options["cookies"][$name]);
        } else {
            $this->options["cookies"][$name] = $value;
        }
        return $this;
    }
    
    public function getCookies () {
        return $this->options["cookies"];
    }
    
    public function getEncType () {
        return $this->options["enctype"];
    }
    
    public function setEncType ($enctype = "") {
        $this->encset = true;
        $this->options["enctype"] = $enctype;
        return $this;
    }
    
    public function execute () {
        $crlf = self::CRLF;
        $op = $this->options;
        $op["method"] = strtoupper($op["method"]);
        
        // build the request
        $rq = array (
            "{$op["method"]} {$op["path"]}{$op["query"]} {$op["version"]}",
            "Host: {$op["host"]}",
        );
        
        // add custom headers
        foreach ($op["headers"] as $key => $value) {
            $rq[] = (strpos($key, ":") === false) ? 
                "{$key}: {$value}" : "{$key} {$value}";
        }
        
        // add cookie data
        $cookies = array ("Cookie: ");
        foreach ($op["cookies"] as $key => $value) {
            $cookies[] = "{$name}={$value}; ";
        }
        
        if (1 < count($cookies)) {
            $rq[] = implode("", $cookies);
        }
        
        
        // create the body of the request - if needed
        $body = array();
        if (count($op["data"]) || count($op["files"])) {
            if ($data = $this->encodeData($op["data"])) {
                $body[] = $data;
            }
            if ($data = $this->encodeFiles($op["files"])) {
                $body[] = $data;
            }
        }
        
        if (count($body)) {
            $body = implode("", $body);
            $len = strlen($body);
            $rq[] = "Content-Type: {$op["enctype"]}; boundary={$op["boundary"]}";
            $rq[] = "Content-Length: {$len}";
        }
        
        $rq[] = "Connection: close";
        $rq[] = "";
        
        if (!empty($body)) {
            $rq[] = $body;
        } else {
            $rq[] = "";
        }
        
        $rq = implode (self::CRLF, $rq);
        
        // create the context
        $ctx = stream_context_create();
        
        if (strtolower($op["scheme"]) === "https") {
            $op["host"] = "ssl://{$op["host"]}";
            if (!$op["verifySSL"]) {
                stream_context_set_option($ctx, "ssl", "verify_peer", false);
                stream_context_set_option($ctx, "ssl", "verify_host", false);
                stream_context_set_option($ctx, "ssl", "allow_self_signed", true);
            }
        }
        
        $sock = stream_socket_client(
            "{$op["host"]}:{$op["port"]}",
            $errorNumber,
            $errorMessage,
            $op["timeout"],
            STREAM_CLIENT_CONNECT,
            $ctx
        );
        
        if (!$sock) {
            $this->errors[] = array ($errorNumber, $errorMessage);
            return false;
        }
        
        if (!fwrite($sock, $rq)) {
            $this->errors[] = array (0, "Unable to write to socket.");
            return false;
        }
        
        // same timeout is used when reading from stream
        stream_set_timeout($sock, $op["timeout"]);
    
        $timeout = false;
        
        // parse the header of the request
        $header = array ();
        while (!feof($sock) && !$timeout) {
            $line = fgets($sock, $op["buffer"]);
            if ($line === self::CRLF ||
                $line === false ||
                feof($sock)
            ) {
                // no longer in the head of the response
                break;
            }
            
            // check if timeout has occurred - is this the best place for it?
            $info = stream_get_meta_data($sock);
            if (!empty($info["timed_out"]))
            {
                $timeout = true;
            }
            
            $line = explode(":", $line);
            $key = array_shift($line);
            $key = trim($key);
            $key = strtolower($key); //< we're lowercasing all header keys...
            
            // if we have value put back together
            if (0 < count($line)) {
                $val = implode(":", $line);
                $val = trim($val);
                if (!empty($header[$key])) {
                    if (is_array($header[$key])) {
                        $header[$key][] = $val;
                    } else {
                        $header[$key] = array ($header[$key], $val);
                    }
                } else {
                    $header[$key] = $val;
                }
            } else {
                // first row should contain the response code
                if (count($header) == 0) {
                    $header["response-code"] = $key;
                } else {
                    $header[$key] = $key;
                }
            }
        }
        
        // parse the body of the request
        $body = array();
        $mode = empty($header["transfer-encoding"]) ? 
            "identity" : $header["transfer-encoding"];
        
        // force mode to be of certain types    
        switch ($mode) {
            case "chunked":
            case "compress":
            case "deflate":
            case "gzip":
            case "identity":
                break;
            default:
                $mode = "identity";
                break;
        }
        
        while (!feof($sock) && !$timeout) {
            $line = fgets($sock, $op["buffer"]);
            
            // check if timeout has occurred - is this the best place for it?
            $info = stream_get_meta_data($sock);
            if (!empty($info["timed_out"]))
            {
                $timeout = true;
            }
            
            if ($mode == "chunked") {
                $length = hexdec($line);
                
                if ($length == 0) 
                {
                    // we're at the end
                    break;
                }
                
                $line = fread($sock, $length);
                $read = strlen($line);
                while ($sock && $read < $length && !feof($sock) && !$timeout) {
                    $tmp = fread($sock, ($length - $read));
                    $read += strlen($tmp);
                    $line .= $tmp;
                    
                    // check if timeout has occurred - is this the best place for it?
                    $info = stream_get_meta_data($sock);
                    if (!empty($info["timed_out"]))
                    {
                        $timeout = true;
                    }
                }
                
                // need to digest trailing crlf
                fgets($sock);
            }
            $body[] = $line;
            switch ($mode) {
                case "identity":
                    break;
            }
        }
        
        if ($timeout) {
            $this->errors[] = "Stream timeout of [{$op["timeout"]}] reached.";
            return false;
        }
        
        
        return array (
            "request" => array (
                "raw" => $rq,
                "options" => $op
            ),
            "response" => array (
                "head" => $header,
                "body" => implode("", $body)
            )
        );
        
    }
    
    protected function encodeData ($data = array()) {
        // if not an array - simply return it
        if (!is_array($data)) {
            return $data;
        }
        
        switch ($this->options["enctype"]) {
            case self::ENC_MULTI:
                $rt = array ();
                $boundary = $this->options["boundary"];
                foreach ($data as $name => $value) {
                    if (is_array ($value)) {
                        foreach ($value as $i => $v) {
                            if (strpos($name, "[]") === false) {
                                $name = "{$name}[]";
                            }
                            $rt[] = "--{$boundary}";
                            $rt[] = "Content-Disposition: form-data; name=\"{$name}\"";
                            $rt[] = "";
                            $rt[] = $v;
                        }
                    } else {
                        $rt[] = "--{$boundary}";
                        $rt[] = "Content-Disposition: form-data; name=\"{$name}\"";
                        $rt[] = "";
                        $rt[] = $value;
                    }
                }
                return implode (self::CRLF, $rt);
            case self::ENC_PLAIN:
                if (count($data)) {
                    return var_export($data);
                }
                return "";
            case self::ENC_JSON:
                if (count($data)) {
                    return json_encode ($data);
                }
                return "";
            case self::ENC_URL:
            default:
                return http_build_query ($data);
        }
    }
    
    protected function encodeFiles ($files = array())  {
        // if not an array - simply return it
        if (!is_array($files)) {
            return $files;
        }
        
        switch ($this->options["enctype"]) {
            case self::ENC_MULTI:
                $rt = array();
                $boundary = $this->options["boundary"];
                foreach ($files as $name => $file) {
                    if (is_array($file)) {
                        if (strpos($name, "[]") === false) {
                            $name = "{$name}[]";
                        }
                        foreach ($file as $i => $f) {
                            $filename = basename($f);
                            $mime = mime_content_type($f);
                            $rt[] = "--{$boundary}";
                            $rt[] = "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$filename}\"";
                            $rt[] = "Content-Type: {$mime}";
                            $rt[] = "";
                            $rt[] = file_get_contents($f);
                        }
                    } else {
                        $filename = basename($file);
                        $mime = mime_content_type($file);
                        $rt[] = "--{$boundary}";
                        $rt[] = "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$filename}\"";
                        $rt[] = "Content-Type: {$mime}";
                        $rt[] = "";
                        $rt[] = file_get_contents($file);
                    }
                }
                return implode(self::CRLF, $rt);
            case self::ENC_PLAIN:
                
                return;
            case self::ENC_JSON:
                $rt = array ();
                foreach ($files as $name => $file) {
                    if (is_array($file)) {
                        if (($pos = strpos($name, "[]")) !== false) {
                            $name = substr($name, 0, $pos);
                        }
                        $rt[$name] = array();
                        foreach ($file as $i => $f) {
                            $filename = basename($f);
                            $contents = file_get_contents($f);
                            $rt[$name][$filename] = base64_encode($contents);
                        }
                    } else {
                        $filename = basename($file);
                        $contents = file_get_contents($file);
                        $rt[$name] = array (
                            $filename => base64_encode($contents)
                        );
                    }
                }
                if (count($rt)) {
                    return json_encode($rt);
                }
                return "";
            case self::ENC_URL:
            default:
                $rt = array ();
                foreach ($files as $name => $file) {
                    if (is_array($file)) {
                        if (($pos = strpos($name, "[]")) !== false) {
                            $name = substr($name, 0, $pos);
                        }
                        $rt[$name] = array();
                        foreach ($file as $i => $f) {
                            $filename = basename($f);
                            $contents = file_get_contents($f);
                            $contents = base64_encode($contents);
                            $rt[$name][$filename] = urlencode($contents);
                        }
                    } else {
                        $filename = basename($file);
                        $contents = file_get_contents($file);
                        $contents = base64_encode($contents);
                        $rt[$name] = array (
                            $filename => $contents
                        );
                    }
                }
                return http_build_query($rt);
        }
    }
    
    public function getHeader ($name = "") {
        return $this->options["headers"][$name];
    }
    
    public function getHeaders () {
        return $this->options["headers"];
    }
    
    public function setHeader ($name = "", $value = "") {
        $this->options["headers"][$name] = $value;
        return $this;
    }
    
    public function setHeaders ($headers = array()) {
        $this->options["headers"] = $headers;
        return $this;
    }
    
    
    public function getFile ($name = "") {
        return $this->options["files"][$name];
    }
    
    public function setFile ($name = "", $file = "") {
        if (!$this->encset) {
            $this->options["enctype"] = self::ENC_MULTI;
        }
        
        if ($file == null) {
            unset ($this->options["files"][$name]);
        } else {
            $this->options["files"][$name] = $file;
        }
        return $this;
    }
    
    public function getFiles () {
        return $this->options["files"];
    }
    
    public function setFiles ($files = array ()) {
        if (!$this->encset) {
            $this->options["enctype"] = self::ENC_MULTI;
        }
        $this->options["files"] = $files;
        return $this;
    }
    
    public static function custom ($method = "GET", $options = array ()) { 
        
        if (is_array($options)) {
            $http = new HTTP (array_merge($options, array( 
                "method" => $method
            )));
        }
        
        else if (is_string($options)) {
            $http = new HTTP (array(
                "url" => $options,
                "method" => $method
            ));
        }
        
        else {
            throw new Exception(
                "Invalid argument supplied to HTTP::{$method}");
        }
        
        return $http->execute();
    }
    
    public static function get ($options = array ()) {
        return self::custom("GET", $options);
    } 
    
    public static function post ($options = array ()) {
        $args = func_get_args();
        switch (count($args)) {
            case 3:
                $options = array (
                    "url" => $args[0],
                    "data" => $args[1],
                    "files" => $args[2]
                );
                break;
            case 2:
                $options = array (
                    "url" => $args[0],
                    "data" => $args[1],
                );
                break;
        }

        return self::custom("POST", $options);
    }
    
    public static function patch ($options = array()) {
        return self::custom("PATCH", $options);
    }
    
    public static function put ($options = array ()) {
        return self::custom("PUT", $options);
    }
    
    public static function delete ($options = array ()) {
        return self::custom("DELETE", $options);
    }
    
    public static function head ($options = array()) {
        return self::custom("HEAD", $options);
    }
    
}