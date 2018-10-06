<?php

namespace hydra\net\http;

interface HttpResponseInterface extends HttpRequestInterface
{
    /**
     * 
     */
    public function getResponseCode ();
    
    /**
     * 
     */
    public function setResponseCode ($code = 200);
    
    /**
     * 
     */
    public function getResponseText ();
    
    /**
     * 
     */
    public function setResponseText ($text = "");
    
    
    /**
     * This will return the response to the calling client.
     */
    public function output ();
    
}