<?php

namespace hydra\net\http\encoders;

use hydra\net\http\HttpConstants;

/**
 * HttpBodyEncoderInterface
 * 
 * 
 */
interface HttpBodyEncoderInterface extends HttpConstants
{
    
    /**
     * Generic function used to encode the body section of an HTTP request.
     * 
     * @param {array} data
     * @param {array} files
     * @param {array} options
     * @return {string}
     */
    public function encode (
        $data = array (), 
        $files = array (),
        $options = array ()
    );
    
    /**
     * Generic function used to decode the body section of an HTTP request.
     * This function should return the data in an array with two different
     * keys, one being "data" and the other being "files". This is done
     * to match the encoding function.
     * 
     * @param {string} body
     * @param {array} options
     * @return {array}
     */
    public function decode (
        $body = "", 
        $options = array ()
    );
    
}