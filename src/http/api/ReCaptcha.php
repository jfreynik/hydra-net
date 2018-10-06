<?php

namespace hydra\net\api;

use hydra\net\HTTP as HTTP;

/**
 * Class that can be used to authorize requests against Google's
 * ReCaptcha service.
 * 
 */ 

class ReCaptcha {

    protected $endpoint = "https://";

    /*
    public function validate ($data = array (), $options = array ()) {
        $http = new HTTP (array_merge (array (
            "data" => array_merge (array (
                "private_key" => "",
                "verify_hash" => "",
            ), $data),
            "method" => "POST",
            "url" => $this->endpoint,
        ), $options));
        
        $rq = $http->execute();
    }
    */
    
    public static function validate (
        $privateKey = "",
        $verifyHash = ""
    ) {
        
        
        return false;
    }

}