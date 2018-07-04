<?php

namespace hydra\net;


class HttpResponse implements HttpConstants
{
    
    /**
     * List of official HTTP Status Codes
     * https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */
    public static $statusCodes = array (
        100 => "",
        101 => "",
        102 => "",
        
        200 => "OK",
        201 => "",
        202 => "",
        203 => "",
        204 => "",
        205 => "",
        206 => "",
        207 => "",
        208 => "",
        226 => "",
        
        300 => "",
        301 => "",
        302 => "",
        303 => "",
        304 => "",
        305 => "",
        306 => "",
        307 => "",
        308 => "",
        
        400 => "",
        401 => "",
        402 => "",
        403 => "",
        404 => "",
        405 => "",
        406 => "",
        407 => "",
        408 => "",
        409 => "",
        410 => "",
        411 => "",
        412 => "",
        413 => "",
        414 => "",
        415 => "",
        416 => "",
        417 => "",
        418 => "",
        421 => "",
        422 => "",
        423 => "",
        424 => "",
        426 => "",
        428 => "",
        429 => "",
        431 => "",
        451 => "",
        
        500 => "",
        501 => "",
        502 => "",
        503 => "",
        504 => "",
        505 => "",
        506 => "",
        507 => "",
        508 => "",
        510 => "",
        511 => "",
        
    );
    
    protected static $defaults = array (
        "statusCode" => 200,
        "statusText" => "OK",
        "useTempFile" => true,
        "headers" => array (),
        "body" => "",
        "request" => "" // do we need this
    );
    
    protected $options = array ();
    
    public function __construct ($options = array ()) {
        $this->options = array_merge($options, self::$default);
    }
    
    public static function create (
        $body = "",
        $code = 200,
        $headers = array ()
    ) {
        return new HttpResponse(array(
            "statusCode" => $code,
            "statusText" => self::$statusCodes[$code],
            "body" => $body,
            "headers" => $headers,
        ));
    }
    
    public function getStatusCode ()
    {
        return $this->statusCode;
    }
    
    public function setStatusCode ($code = 200)
    {
        $this->statusCode = $code;
        $this->statusText = (isset(self::$statusCodes[$code])) ?
            self::$statusCodes[$code] : "Unknown Status Code";
        return $this;
    }
    
    public function getStatusText ()
    {
        return $this->statusText;
    }
    
    public function setStatusText ($text = "")
    {
        $this->statusText = $text;
        return $this;
    }
    
    public function getHeaders ()
    {
        return $this->options["headers"];
    }
    
    public function setHeaders ($headers = array ())
    {
        if (is_array($headers)) {
            $this->options["headers"] = $headers;
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
    
    public function getBody ()
    {
        return $this->options["body"];
    }
    
    public function setBody ($body = "")
    {
        $this->options["body"] = $body;
        return $this;
    }
    
}