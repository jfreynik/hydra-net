<?php

namespace hydra\net;


class HttpRequest implements HttpInterface
{
    
    public static $defaults = array (
        "verifySSL" => true,
        "tmpfile"   => false,
        "debug"     => false,
        "proxy"     => null,
        "sockaddr"  => null,
        "timeout"   => self::TIMEOUT_DEFAULT,
        "buffer"    => self::BUFFER_DEFAULT,
    );
    
    protected $options = array ();
    
    public function __construct ($options = array ())
    {
        $this->init($options);
    }
    
    public function init ($options = array ())
    {
        $this->options = array_merge(self::$defaults, $options);
        return $this;
    }
    
    public function verifySSL ($verifySSL = true)
    {
        $this->options["verifySSL"] = $verifySSL;
        return $this;
    }
    
    public function getBuffer ()
    {
        return $this->options["buffer"];
    }
    
    public function setBuffer ($buffer = self::BUFFER_SIZE)
    {
        $this->options["buffer"] = $buffer;
        return $this;
    }
    
    public function execute (HttpRequestBuilderInterface $builder)
    {
        $opt = $this->options;
        $raw = $builder->getRaw();
        $req = $builder->getRequest();
        
        // TODO - merge the builder options and the request options.
        
        // TODO check whether to use cURL / sockets / file_open
        
        // using streams
        $ctx = stream_context_create ();
        
        $sock = null;
        $socket_addr = "";
        $socket_errno = "";
        $socket_errstr = "";
        
        // stream_context_set_option($ctx, "http", "ignore_errors", false);
        
        if ($opt["proxy"]) {
            // connect to the proxy
            
            // stream_context_set_option($ctx, "http", "proxy", "tcp://{$opt["proxy"]}");
            var_dump($raw);
            
            
            die ("TODO: proxy needs work...");
        }
        
        if (strtolower($req["scheme"]) === "https") {
            $socket_addr = "ssl://{$req["host"]}:{$req["port"]}";
            if (!$opt["verifySSL"]) {
                stream_context_set_option($ctx, "ssl", "verify_peer", false);
                stream_context_set_option($ctx, "ssl", "verify_host", false);
                stream_context_set_option($ctx, "ssl", "allow_self_signed", true);
            }
        } else {
            $socket_addr = "{$req["host"]}:{$req["port"]}";
        }
        
        $sock = stream_socket_client (
            $socket_addr,
            $socket_errno,
            $socket_errstr,
            $opt["timeout"],
            STREAM_CLIENT_CONNECT,
            $ctx 
        );
        
        if (!$sock) {
            return array (
                "status" => 0,
                "errorInfo" => $socket_errstr,
                "errorCode" => $socket_errno,
            );
        }
        
        // try to send the request
        if (!fwrite ($sock, $raw)) {
            return array (
                "status" => 0,
                "errorInfo" => "Unable to write to socket.",
                "errorCode" => -1,
            );
        }
        
        // should set the timeout first 
        if (!empty($opt['timeout'])) {
            stream_set_timeout($sock, $opt["timeout"]);
        }
        
        $errors = array ();
        
        // read header information
        $headers = HttpHelper::readHeaders ($sock, $opt["buffer"]);
        
        if (!$headers["status"]) {
            // handle error
        }
        
        $headers = $headers["headers"];
        $status = explode(" ", $headers["response-code"]);
        
        // read body information
        $body = HttpHelper::readBody (
            $sock, 
            $opt["buffer"],
            isset($headers["transfer-encoding"]) ? 
                $headers["transfer-encoding"] : null,
            $opt["tmpfile"]
        );
        
        if (!$body["status"]) {
            // handle error
        }
        
        $body = implode("", $body["body"]);
        
        return array (
            "version" => array_shift($status),
            "code" => array_shift($status),
            "message" => implode(" ", $status),
            "headers" => $headers,
            "body" => $body,
            "errors" => $errors,
        );
    }
    
    
    public static function connect ($data = array ())
    {
        $data["method"] = self::METHOD_CONNECT;
        return self::custom($data);
    }
    
    public static function delete ($data = array ())
    {
        $data["method"] = self::METHOD_DELETE;
        return self::custom($data);
    }
    
    public static function get ($data = array ())
    {
        if (is_string($data)) {
            $data = array (
                "url" => $data,
            );
        }
        $data["method"] = self::METHOD_GET;
        return self::custom($data);
    }
    
    public static function head ($data = array ())
    {
        $data["method"] = self::METHOD_HEAD;
        return self::custom($data);
    }
    
    public static function options ($data = array ())
    {
        $data["method"] = self::METHOD_OPTIONS;
        return self::custom($data);
    }
    
    public static function patch ($data = array ())
    {
        $data["method"] = self::METHOD_PATCH;
        return self::custom($data);
    }
    
    public static function post ($data = array ())
    {
        $data["method"] = self::METHOD_POST;
        return self::custom($data);
    }
    
    public static function put ($data = array ())
    {
        $data["method"] = self::METHOD_PUT;
        return self::custom($data);
    }
    
    public static function trace ($data = array ())
    {
        $data["method"] = self::METHOD_TRACE;
        return self::custom($data);
    }
    
    public static function custom ($data = array ())
    {
        $payload = new HttpRequestBuilder($data);
        $request = new HttpRequest($data);
        return $request->execute($payload);
    }
    
    public static function raw ($host = "", $raw = "")
    {
        $payload = new HttpRequestBuilder(array(
            "raw" => $raw,
        ));
        $request = new HttpRequest();
        return $request->execute($payload);
    }
    

}