<?php

namespace hydra\net\http;

/**
 * HttpResponse
 * 
 * 
 */

class HttpResponse extends HttpRequest implements HttpResponseInterface
{
    
    /**
     * List of official HTTP Status Codes
     * https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */
    public static $responseCodes = array (
        100 => "Continue",
        101 => "Switching Protocols",
        102 => "Processing",
        103 => "Early Hints",
        
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        207 => "Multi Status",
        208 => "Already Reported",
        226 => "IM Used",
        
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        306 => "Switch Proxy",
        307 => "Temporary Redirect",
        308 => "Permanent Redirect",
        
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden ",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Payload Too Large",
        414 => "URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Range Not Satisfiable",
        417 => "Expectation Failed",
        418 => "I'm a teapot",
        421 => "Misdirected Request",
        422 => "Unprocessable Entity",
        423 => "Locked",
        424 => "Failed Dependency",
        426 => "Upgrade Required",
        428 => "Precondition Required",
        429 => "Too Many Requests",
        431 => "Request Header Fields Too Large",
        451 => "Unavailable For Legal Reasons",
        
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported",
        506 => "Variant Also Negotiates",
        507 => "Insufficient Storage",
        508 => "Loop Detected",
        510 => "Not Extended",
        511 => "Network Authentication Required",
        
    );
    
    protected static $default = array (
        "responseCode" => 200,
        "responseText" => "OK",
        "useTempFile" => false,
        "headers" => array (),
        "body" => "",
        "request" => "" // do we need this
    );
    
    protected $options = array ();
    
    public function __construct ($options = array ()) {
        parent::__construct();
        $this->options = array_merge($options, self::$default);
    }
    
    /* - can't remember reasoning behind this function
    public static function create (
        $body = "",
        $code = 200,
        $headers = array ()
    ) {
        return new HttpResponse(array(
            "statusCode" => $code,
            "statusText" => self::$responseCodes[$code],
            "body" => $body,
            "headers" => $headers,
        ));
    }
    */
    
    public function getResponseCode ()
    {
        return $this->options["responseCode"];
    }
    
    public function setResponseCode ($code = 200)
    {
        $code = (int) $code;
        $this->options["responseCode"] = $code;
        $this->options["responseText"] = (isset(self::$responseCodes[$code])) ?
            self::$responseCodes[$code] : "Unknown Response Code";
        return $this;
    }
    
    public function setResponseText ($text = "")
    {
        $this->options["responseText"] = $text;
        return $this;
    }
    
    public function getResponseText ()
    {
        return $this->options["responseText"];
    }
    
    public function getHeaders ()
    {
        return $this->options["headers"];
    }
    
    public function setHeaders ($headers = array ())
    {
        if (is_array($headers)) {
            $this->options["headers"] = $headers;
        } else if (is_string($headers)) {
            $this->setHeadersRaw($headers);
        }
        return $this;
    }
    
    public function getHeader ($key = "") 
    {
        if (isset($this->options["headers"][$key])) {
            return $this->options["headers"][$key];
        }
        return null;
    }
    
    public function setHeader ($key = "", $value = "")
    {
        $this->options["headers"][$key] = $value;
        return $this;
    }
    
    public function getHeadersRaw ()
    {
        $headers = array (
            implode(" ", array (
                $this->getVersion (),
                $this->getResponseCode (),
                $this->getResponseText (),
            ))
        );
        
        foreach ($this->options["headers"] as $key => $value) {
            $headers[] = "{$key}: {$value}";
        }
        
        return implode(self::CRLF, $headers);
    }
    
    public function setHeadersRaw ($headers = "")
    {
        $this->options["headers"] = array ();
        $headers = explode(self::CRLF, $headers);
        foreach ($headers as $i => $header) {
            if ($i === 0) {
                list ($version, $code, $phrase) = explode(" ", $header);
                $this->setVersion ($version);
                $this->setResponseCode ($code);
                $this->setResponseText ($phrase);
                continue;
            }
            $line = explode(":", $header);
            $key = trim(strtolower(array_shift($line)));
            $value = trim(implode(":", $line));
            $this->setHeader($key, $value);
        }
        return $this;
    }
    
    public function getBody ()
    {
        if (!empty($this->options["body"])) {
            return $this->options["body"];
        }
        
        $return = array ();
        
        if (!empty($this->options["data"])) {
            $return["data"] = $this->options["data"];
        }
        
        if (!empty($this->options["files"])) {
            $return["files"] = $this->options["files"];
        }
        
        return $return;
    }
    
    public function setBody (
        $data = array (),
        $files = array ()
    ) {
        if (is_string($data)) {
            return $this->setBodyRaw($data);
        }
        
        die ("TODO - HttpResponse - setBody");
        
        return $this;
    }
    
    public function getBodyRaw ($needsTransferEncoded = false)
    {
        if (!empty($this->options["body"])) {
            return $this->options["body"];
        }
        
        die ("TODO" . __FILE__ . __LINE__);
        
    }
    
    public function setBodyRaw ($body = "", $needsTransferDecoded = false)
    {
        
        // set the raw body ... and decode if needed
        $this->options["body"] = $body;
        
        // decode the raw body and set the received data
        // only decode if needed ...
        
        if ($needsTransferDecoded) {
            $transfer = $this->getHeader("transfer-encoding");
            if ($transfer) {
                $body = $this->encoders->decodeBody($transfer, $body);
            }
        }
            
        // only decode if needed
        $content = $this->getHeader("content-type");
        if (($pos = strpos($content, ";")) !== false) {
            $content = substr($content, 0, $pos);
        }
        
        // if not a "text" content type decode it
        if (strpos($content, "text/") === false) {
            $body = $this->encoders->decodeBody($content, $body, array (
                "assoc" => 1
            ));
            
            // reset any data / files in the response
            $this->options["data"] = array ();
            foreach ($body as $key => $value) {
                // set the data
                $this->setData($key, $value);
                
                // should we handle files
            }
        } 
            
        /*
        else {
            $this->options["body"] = $body;
        }
        */
        
        return $this;
    }
    
    public function output ()
    {
        
    }
    
    public function __toString ()
    {
        return "HttpResponse ".spl_object_hash($this);
    }
    
}