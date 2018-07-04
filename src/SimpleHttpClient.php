<?php 

namespace hydra\net;

class SimpleHttpClient implements SimpleHttpClientInterface
{
    protected $logs = array ();
    
    protected $options = array (
        "follow_redirects"  => true,
        "max_redirects"     => 20,
    );
    
    protected $cookies = array ();
    
    protected $history = array ();
    
    protected $requests = array ();
    
    public function __construct ($options = array ()) 
    {
        $this->options = array_merge($this->options, $options);
    }
    
    public function goBack () 
    {
        
    }
    
    public function goForward () 
    {
        
    }
    
    public function getAttribute ($name = "") 
    {
        
    }
    
    public function custom ($method = "GET", $options = array ()) { 
        $resp = HTTP::CUSTOM ($method, $options);
        $head = $resp["reponse"]["head"];
        
        
        /* check response for redirect - 
        if ($this->options["follow_redirects"] &&
            $head[""]
        ) {
            
        }
        */
        
        
        
    }
    
    public function get ($options = array ()) {
        return $this->custom("GET", $options);
    } 
    
    public function post ($options = array ()) {
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

        return $this->custom("POST", $options);
    }
    
    public function patch ($options = array()) {
        return $this->custom("PATCH", $options);
    }
    
    public function put ($options = array ()) {
        return $this->custom("PUT", $options);
    }
    
    public function delete ($options = array ()) {
        return $this->custom("DELETE", $options);
    }
    
    public function head ($options = array()) {
        return $this->custom("HEAD", $options);
    }

}