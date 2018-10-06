<?php

namespace hydra\net\http\encoders;

class HttpBodyPlainEncoder implements HttpBodyEncoderInterface
{
    
    public function encode (
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        $return = array ();
        if (!empty($data)) {
            $return["data"] = $data;
        }
        
        if (!empty($files)) {
            $temp = array ();
            foreach ($files as $name => $file) {
                if (is_array($file)) {
                    if (($pos = strpos($name, "[]")) !== false) {
                        $name = substr($name, 0, $pos);
                    }
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
                            "data" => $contents
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
            $return["files"] = $temp;
        }
        
        return serialize($return);
    }
    
    public function decode (
        $body = "",
        $options = array ()
    ) {
        die ("TODO - " . __FUNCTION__ . ":" . __FILE__);
    }
    
}