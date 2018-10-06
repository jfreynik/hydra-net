<?php

namespace hydra\net\http\encoders;

/**
 * 
 * 
 * TODO - look into using a recursive function to encode data / files so that multi-dimensional arrays are not missed
 * 
 */
class HttpBodyMultipartEncoder implements HttpBodyEncoderInterface
{
    
    public function encode (
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        $return = array ();
        
        if (empty($options["boundary"])) {
            throw new \BadMethodCallException(
                "Required key 'boundary' is missing from 'options' parameter."
            );
        }
        
        $boundary = $options["boundary"];
        
        if (!empty($data)) {
            $return = array ();
            foreach ($data as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $i => $v) {
                        $temp = $name;
                        if (is_numeric($i) && 
                            strpos($name, "[") === false
                        ) {
                            $temp = "{$name}[]"; 
                        } else if (!is_numeric($i)) {
                            $temp = "{$name}[{$i}]";
                        }
                        $return[] = implode(self::CRLF, array (
                            "--{$boundary}",
                            "Content-Disposition: form-data; name=\"{$temp}\"",
                            "",
                            $v,
                        ));
                    }
                } else {
                    $return[] = implode(self::CRLF, array (
                        "--{$boundary}",
                        "Content-Disposition: form-data; name=\"{$name}\"",
                        "",
                        $value,
                    ));
                }
            }
        }
        
        if (!empty($files)) {
            foreach ($files as $name => $file) {
                if (is_array($file)) {
                    if (strpos($name, "[]") === false) {
                        $name = "{$name}[]";
                    }
                    foreach ($file as $i => $f) {
                        $mime = mime_content_type($f);
                        $filename = basename($f);
                        $contents = file_get_contents($f);
                        
                        $return[] = implode(self::CRLF, array (
                            "--{$boundary}",
                            "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$filename}\"",
                            "Content-Type: {$mime}",
                            "",
                            $contents,
                        ));
                    }
                } 
                
                else {
                    $mime = mime_content_type($file);
                    $filename = basename($file);
                    $contents = file_get_contents($file);
                    $return[] = implode(self::CRLF, array (
                        "--{$boundary}",
                        "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$filename}\"",
                        "Content-Type: {$mime}",
                        "",
                        $contents,
                    ));
                }
            }
        }
        
        return implode(self::CRLF, $return);
    }
    
    public function decode (
        $body = "",
        $options = array ()
    ) {
        die ("TODO - " . __FUNCTION__ . ":" . __FILE__);
    }
    
}