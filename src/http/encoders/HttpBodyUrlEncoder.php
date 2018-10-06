<?php

namespace hydra\net\http\encoders;

class HttpBodyUrlEncoder implements HttpBodyEncoderInterface
{
    
    public function encode (
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        $return = array ();
        
        if (!empty($data)) {
            // the below would change the $_POST array 
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
                        $contents = base64_encode($contents);
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
                    $contents = base64_encode($contents);
                    $temp[$name] = array (
                        "name" => $filename,
                        "type" => $mime,
                        "size" => strlen($contents),
                        "data" => $contents
                    );
                }
            }
            
            // the below would change the $_POST array
            // $return["files"] = $temp;
            $return = array_merge($return, $temp);
        }
        
        return http_build_query($return);
    }
    
    public function decode (
        $body = "",
        $options = array ()
    ) {
        parse_str($body, $result);
        return $result;
    }
    
}