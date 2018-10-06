<?php

namespace hydra\net\http\encoders;

class HttpBodyJsonEncoder implements HttpBodyEncoderInterface
{
    
    public function encode (
        $data = array (),
        $files = array (),
        $options = array ()
    ) {
        $return = array ();
        
        if (!empty($data)) {
            $return = $data;
        }
        
        if (!empty($files)) {
            $temp = array ();
            foreach ($files as $name => $file) {
                if (is_array($file)) {
                    
                    if (($pos = strpos($name, "[]"))) {
                        $name = substr($name, 0, $pos);
                    }
                    
                    // check if previously encoded
                    if ((isset($file[0]) &&
                        isset($file[0]["data"])
                    ) || (
                        isset($file["data"])
                    )) {
                        
                        if (isset($file["data"])) {
                            $file["data"] = base64_encode($file["data"]);
                            $file["size"] = strlen($file["data"]);
                        }
                        
                        else {
                            foreach ($file as $i => $v) {
                                $file[$i]["data"] = 
                                    base64_encode($file[$i]["data"]);
                                $file[$i]["size"] = 
                                    strlen($file[$i]["data"]);
                            }
                        }
                        
                        $temp[$name] = $file;
                    }
                    
                    else {
                        
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
                }
                
                else {
                    $mime = mime_content_type($file);
                    $filename = basename($file);
                    $contents = file_get_contents($file);
                    $contents = base64_encode($contents);
                    $return[$name] = array (
                        "name" => $filename,
                        "type" => $mime,
                        "size" => strlen($contents),
                        "data" => $contents,
                    );
                }
            }
            $return = array_merge($return, $temp);
        }
        
        if (empty($return)) {
            return "";
        }
        
        return json_encode($return);
    }
    
    public function decode (
        $body = "",
        $options = array ()
    ) {
        try {
            $result = json_decode($body, true); // !empty($options["assoc"]));
            
            $return = array ();
            $files = array ();
            
            var_dump($body);
            var_dump($result);
            exit;
            
            
            // the files should only be on the first dimension of the array
            foreach ($result as $key => $value) {
                if (is_array($value)) {
                    if ((
                        isset($value["name"]) &&
                        isset($value["type"]) &&
                        isset($value["size"]) &&
                        isset($value["data"])
                    ) || (
                        isset($value[0]) && 
                        isset($value[0]["name"]) &&
                        isset($value[0]["type"]) &&
                        isset($value[0]["size"]) &&
                        isset($value[0]["data"])
                    )) {
                        
                        if (isset($value[0])) {
                            foreach ($value as $i => $v) {
                                $value[$i]["data"] = 
                                    base64_decode($value[$i]["data"]);
                                $value[$i]["size"] = 
                                    strlen($value[$i]["data"]);
                            }
                        } 
                        
                        else {
                            $value["data"] = base64_decode($value["data"]);
                            $value["size"] = strlen($value["data"]);
                        }
                        
                        $files[$key] = $value;
                        unset ($result[$key]);
                    }
                }
            }
            
            if (!empty($result)) {
                $return["data"] = $result;
            }
            
            if (!empty($files)) {
                $return["files"] = $files;
            }
            
            return $return;
        } catch (\Exception $e) { }
        return "";
    }
    
}