<?php

namespace hydra\net\http;

use hydra\net\http\exceptions\HttpClientException;
use hydra\net\http\exceptions\HttpClientSocketException;

class SimpleHttpClient implements SimpleHttpClientInterface
{
    
    public static $defaults = array (
        "verifySSL"     => true,
        
        // TODO - add temp file usage
        // "tmpfile"       => false,
        
        // will most likely need more proxy details...
        "proxy"         => "",

        "timeout"       => self::TIMEOUT_DEFAULT,

        // is the buffer even used?
        "buffer"        => self::BUFFER_DEFAULT,
        "maxRedirects"  => self::MAX_REDIRECTS,
    );
    
    protected $options = array ();
    
    protected $history = array ();
    
    protected $links = array ();
    
    protected $images = array ();

    protected $domain = "";
    
    protected $request = null;
    
    protected $response = null;
    
    protected $redirects = 0;
    
    public function __construct ($options = array ())
    {
        $this->init($options);
    }
    
    public function init ($options = array ())
    {
        $this->options = array_merge(self::$defaults, $options);
        return $this;
    }
    
    public function getVerifySSL ()
    {
        return $this->options["verifySSL"];
    }
    
    public function setVerifySSL ($verifySSL = true)
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
    
    public function getProxy ()
    {
        return $this->options["proxy"];
    }
    
    public function setProxy ($proxy = "")
    {
        $this->options["proxy"] = $proxy;
        return $this;
    }
    
    public function getTimeout ()
    {
        return $this->options["timeout"];
    }
    
    public function setTimeout ($timeout = "")
    {
        $this->options["timeout"] = $timeout;
        return $this;
    }
    
    public function getMaxRedirects ()
    {
        return $this->options["maxRedirects"];
    }
    
    public function setMaxRedirects ($redirects = "")
    {
        $this->options["maxRedirects"] = $redirects;
    }
    
    
    // TODO CLEAN UP HERE

    /**
     * 
     */
    public function execute (HttpRequest $request)
    {
        // TODO check whether to use cURL / sockets / file_open
        
        $opt = $this->options;
        
        // if redirected too many times throw exception
        if ($opt["maxRedirects"] < $this->redirects) {
            throw new HttpClientException(
                "Max number of redirects reached for: {$request->getUrl()}"
            );
        }
        
        // set the current request
        $this->request = $request;
        
        // append the request into the client's history
        $this->history[] = $request;
        
        $req = $request->getOptions ();
        $raw = $request->getRaw ();
        $rsp = new HttpResponse ();
        
        $sock = null;
        $addr = "";
        $errn = "";
        $errs = "";
        
        $ctx = stream_context_create ();
        
        // figure out the address for the socket - if proxy is
        // set, use that instead.
        
        if (strtolower ($req["scheme"]) === "https") {
            $addr = "ssl://{$req["host"]}:{$req["port"]}";
            if (empty ($opt["verifySSL"])) {
                stream_context_set_option (
                    $ctx, "ssl", "verify_peer", false);
                stream_context_set_option (
                    $ctx, "ssl", "verify_host", false);
                stream_context_set_option (
                    $ctx, "ssl", "allow_self_signed", true);
            }
        } else {
            $addr = "{$req["host"]}:{$req["port"]}";
        }
        
        // open the socket connection
        $sock = stream_socket_client (
            $addr,
            $errn,
            $errs,
            $opt["timeout"],
            STREAM_CLIENT_CONNECT,
            $ctx 
        );
        
        // how is this timeout different than the above timeout?
        if (!empty($opt["timeout"])) {
            stream_set_timeout($sock, $opt["timeout"]);
        }
        
        if (!$sock) {
            throw new HttpClientSocketException($errs, $errn);
        }
        
        // try to send the request
        if (!fwrite ($sock, $raw)) {
            throw new HttpClientSocketException(
                "Unable to write to the socket.");
        }

        // TODO - should we provide ability to use temporary file?
        
        // grab the headers
        $data = array ();
        while (!feof ($sock)) {
            $line = fgets ($sock, $opt["buffer"]);
            if (!trim ($line)) {
                break;
            }
            $data[] = $line;
        }
        
        $data = implode ("", $data);
        $rsp->setHeadersRaw ($data);
        
        // TODO - should we provide ability to use temporary file?

        // grab the body of the request
        $data = array ();
        while (!feof ($sock)) {
            $line = fgets ($sock, $opt["buffer"]);
            $data[] = $line;
        }
        
        // close the socket
        fclose($sock);
        
        $data = implode ("", $data);
        
        if ($data) {
            // the data needs decoded
            $rsp->setBodyRaw ($data, true);
        }
        
        // check if redirecting
        $code = $rsp->getResponseCode ();
        
        if ($code == self::STATUS_CODE_MOVED_PERMANENTLY ||
            $code == self::STATUS_CODE_FOUND
        ) {
            $this->redirects++;

            // store in history ... ?
            
            // send get request to the new URL
            $url = $rsp->getHeader("location");
            $request = new HttpRequest(array (
               "url" => $url 
            ));

            // call again ...
            return $this->execute($request);
        } 
        
        else {
            // set redirects back to 0
            $this->redirects = 0;
        }
        
        $this->response = $rsp;
        return $this;
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
    
    public static function get ($data = array (), $options = array ())
    {
        if (is_string($data)) {
            $data = array (
                "url" => $data,
            );
        }
        $data["method"] = self::METHOD_GET;
        return self::custom($data, $options);
    }
    
    public static function head ($data = array (), $options = array ())
    {
        $data["method"] = self::METHOD_HEAD;
        return self::custom($data, $options);
    }
    
    public static function options ($data = array (), $options = array ())
    {
        $data["method"] = self::METHOD_OPTIONS;
        return self::custom($data, $options);
    }
    
    public static function patch ($data = array (), $options = array ())
    {
        $data["method"] = self::METHOD_PATCH;
        return self::custom($data, $options);
    }
    
    public static function post ($data = array (), $options = array ())
    {
        $data["method"] = self::METHOD_POST;
        return self::custom($data, $options);
    }
    
    public static function put ($data = array (), $options = array ())
    {
        $data["method"] = self::METHOD_PUT;
        return self::custom($data, $options);
    }
    
    public static function trace ($data = array (), $options = array ())
    {
        $data["method"] = self::METHOD_TRACE;
        return self::custom($data, $options);
    }
    
    public static function custom (
        $request = array (),
        $options = array ()
    ) {

        // if request is array - convert to actual request
        if (is_array ($request)) {
            $request = new HttpRequest($request);
        }

        $client = new SimpleHttpClient($options);
        return $client->execute($request);
    }
    
    public static function raw ($host = "", $raw = "")
    {
        die("TODO - SimpleClient Raw");
    }
    
    
    public function getHistory ()
    {
        return $this->history;
    }
    
    public function getRequest ()
    {
        return $this->request;
    }
    
    public function getResponse ()
    {
        return $this->response;
    }
    
    public function getLinks ()
    {
        $body = $this->response->getBodyRaw();
        
        // if data payload simply return empty array
        if (!is_string($body)) {
            return array ();
        }
        
        preg_match_all (
            "#<a[^>]+href=['\"](.*?)['\"]#",
            $body,
            $results
        );
        
        return $results[1];
    }

    public function getStyles ()
    {

    }

    public function getScripts ()
    {

    }
    
    public function getImages ()
    {
        $body = $this->response->getBodyRaw();
        
        // if data payload simply return empty array
        if (!is_string($body)) {
            return array ();
        }
        
        preg_match_all (
            "#<img[^>]+src=['\"](.*?)['\"]#",
            $body,
            $results
        );
        
        return $results[1];
    }

}