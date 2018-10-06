<?php

namespace hydra\net\http\encoders;

class HttpBodyBase64UrlEncoder implements HttpBodyEncoderInterface
{
    
    public function encode (
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        $return = array ();
        
        if (!empty($data)) {
            // $return["data"] = $data;
            $return = $data;
        }
        
        if (!empty($files)) {
            $temp = array ();
            foreach ($files as $name => $file) {
                if (is_array($file)) {
                    $temp[$name] = array ();
                    foreach ($file as $i => $f) {
                        $mime = mime_content_type($f);
                        $filename = basename($f);
                        $contents = file_get_contents($f);
                        $temp[$name][] = array (
                            "name" => $filename,
                            "type" => $mime,
                            "size" => strlen($contents),
                            "data" => $contents,
                        );
                    }
                }
                
                else {
                    $mime = mime_content_type($file);
                    $filename = basename($file);
                    $contents = file_get_contents($file);
                    $temp[$name] = array (
                        "name" => $filename,
                        "type" => $mime,
                        "size" => strlen($contents),
                        "data" => $contents
                    );
                }
            }
            
            // $return["files"] = $temp;
            $return = array_merge($return, $temp);
        }
        
        $return = json_encode($return);
        $return = base64_encode($return);
        $return = urlencode($return);
        return str_replace("%", "_", $return);
    }
    
    public function decode (
        $body = "",
        $options = array ()
    ) {
        $result = str_replace("_", "%", $body);
        $result = urldecode($result);
        $result = base64_decode($result);
        $result = json_decode($result, !empty($options["assoc"]));
        return $result;
    }
    
}