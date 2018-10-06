<?php

namespace hydra\net\http\encoders;

class HttpBodyChunkedEncoder implements HttpBodyEncoderInterface
{
    
    public function encode (
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        die ("TODO - encode HttpBodyChunkedEncoder");
    }
    
    
    public function decode (
        $body = "",
        $options = array ()
    ) {
        $decoded = array ();
        $length = 0;
        
        while (true) {
            $length = substr($body, 0, 
                strpos($body, self::CRLF) + strlen(self::CRLF));
            $lengthChars = strlen($length);
            $length = hexdec(trim($length)); // convert to decimal
            if (!$length) {
                break;
            }
            $chunk = substr($body, $lengthChars, $length);
            $decoded[] = $chunk;
            $body = substr($body, strlen($chunk) + $lengthChars + strlen(self::CRLF));
        }
        return implode("", $decoded);
    }
    
}