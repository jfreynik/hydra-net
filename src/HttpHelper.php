<?php 

namespace hydra\net;

/**
 * TODO - update errors to throw exceptions instead of return error data
 */
class HttpHelper implements HttpInterface
{
    public static function encodeData (
        $data = array (),
        $enctype = self::ENC_URL,
        $boundary = ""
    ) {
        if (!is_array($data)) {
            $data = array ("d" => $data);
        }
        
        if ($enctype == self::ENC_MULTI) {
            $return = array ();
            foreach ($data as $name => $value) {
                if (is_array($value)) {
                    if (strpos($name, "[]") === false) {
                        $name = "{$name}[]";
                    }
                    foreach ($value as $i => $v) {
                        $return[] = implode(self::CRLF, array (
                            "--{$boundary}",
                            "Content-Disposition: form-data; name=\"{$name}\"",
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
            return implode(self::CRLF, $return);
        }
        
        else if ($enctype == self::ENC_JSON) {
            return json_encode($data);
        }
        
        else if ($enctype == self::ENC_B64URL) {
            $data = json_encode($data);
            $data = base64_encode($data);
            $data = urlencode($data);
            $data = str_replace("%", "_", $data);
            return $data;
        }
        
        else if ($enctype == self::ENC_PLAIN) {
            return var_export($data, true);
        }
        
        // default to url dara
        return http_build_query($data);
    }
    
    public static function encodeFiles (
        $files = array (),
        $enctype = self::ENC_MULTI,
        $boundary = ""
    ) {
        if (!is_array($files)) {
            $files = array ("f" => $files);
        }
        
        $return = array ();
        
        if ($enctype != self::ENC_MULTI) {
            
            foreach ($files as $name => $file) {
                if (is_array($file)) {
                    if (($pos = strpos($name, "[]")) !== false) {
                        $name = substr($name, 0, $pos);
                    }
                    $return[$name] = array();
                    foreach ($file as $i => $f) {
                        $mime = mime_content_type($f);
                        $filename = basename($f);
                        $contents = file_get_contents($f);
                        $contents = base64_encode($contents);
                        $return[$name][] = array (
                            "name" => $filename,
                            "type" => $mime,
                            "size" => strlen($contents),
                            "data" => $contents,
                        );
                    }
                } else {
                    $mime = mime_content_type($file);
                    $filename = basename ($file);
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
        
            if ($enctype == self::ENC_JSON) {
                return json_encode ($return);
            }
            
            else if ($enctype == self::ENC_PLAIN) {
                return var_export($return, true);
            }
            
            else if ($enctype == self::ENC_B64URL) {
                $return = json_encode($return);
                $return = base64_encode($return);
                $return = urlencode($return);
                $return = str_replace("%", "_", $return);
                return $return;
            }
            
            else if ($enctype == self::ENC_URL) {
                return http_build_query ($return);
            }
            
        }
        
        // default to ENC_MULTI
        foreach ($files as $name => $file) {
            if (is_array($file)) {
                if (strpos($name, "[]") === false) {
                    $name = "{$name}[]";
                }
                foreach ($file as $i => $f) {
                    $fname = basename($f);
                    $mime = mime_content_type($f);
                    $return[] = implode(self::CRLF, array (
                        "--{$bd}",
                        "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$fname}\"",
                        "Content-Type: {$mime}",
                        "",
                        file_get_contents($f),
                    ));
                }
            } else {
                $fname = basename($file);
                $mime = mime_content_type($file);
                $return[] = implode(self::CRLF, array (
                    "--{$bd}",
                    "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$fname}\"",
                    "Content-Type: {$mime}",
                    "",
                    file_get_contents($file),
                ));
            }
        }
        return implode(self::CRLF, $return);
    }
    
    public static function decodeData (
        $data = "",
        $enctype = self::ENC_URL
    ) {
        
    }
    
    public static function decodeFiles (
        $files = "",
        $enctype = self::ENC_URL
    ) {
        
    }
    
    public static function readHeaders (
        $socket = null,
        $buffer = self::BUFFER_DEFAULT
        // $timeout = self::TIMEOUT_DEFAULT
    ) {
        if (!$socket) {
            return array (
                "status" => 0,
                "message" => "Invalid socket provided.",
            );
        }
        
        $headers = array ();
        while (!feof($socket)) {
            $line = fgets($socket, $buffer);
            if ($line === self::CRLF || $line === false || feof($socket)) {
                break;
            }
            
            // check if timeout occurred
            $info = stream_get_meta_data($socket);
            if (!empty($info["timed_out"])) {
                return array (
                    "status" => 0,
                    "message" => "Stream timeout reached.",
                    "headers" => $headers,
                );
            }
            
            $val = explode (":", $line);
            $key = array_shift($val);
            $key = trim ($key);
            $key = strtolower($key);
            
            $val = implode(":", $val);
            $val = trim ($val);
            
            if (empty($headers)) {
                $headers["response-code"] = $key;
            } else {
                if (!empty($headers[$key])) {
                    if (is_array($headers[$key])) {
                        $headers[$key][] = $val;
                    } else {
                        $headers[$key] = array (
                            $headers[$key],
                            $val
                        );
                    }
                } else {
                    $headers[$key] = $val;
                }
            }
        }
        
        return array (
            "status" => 1,
            "headers" => $headers
        );
    }
    
    public static function readBody (
        $socket = null,
        $buffer = self::BUFFER_DEFAULT,
        $encoding = self::ENC_TRN_IDENTITY,
        // $timeout = self::TIMEOUT_DEFAULT,
        $tempfile = false
    ) {
        $return = array (
            "status" => 1,
            "body" => array (),
            "message" => array (),
        );
        
        if ($tempfile) {
            $tempfile = tmpfile();
        }
        
        $encoding = strtolower($encoding);
        
        // force to a proper encoding
        switch ($encoding) {
            case self::ENC_TRN_CHUNKED:
            case self::ENC_TRN_COMPRESS:
            case self::ENC_TRN_DEFLATE:
            case self::ENC_TRN_GZIP:
            case self::ENC_TRN_IDENTITY:
                break;
            default:
                $return["message"][] = "Unknown transfer encoding used: " .
                    "[{$encoding}] - using [identity] instead.";
                $encoding = self::ENC_TRN_IDENTITY;
                break;
        }
        
        file_put_contents(dirname(__FILE__)."/temp.txt", "");
        
        // read from socket 
        while (!feof($socket)) {
            $line = fgets($socket, $buffer);
            
            // check if timeout occurred
            $info = stream_get_meta_data ($socket);
            if (!empty($info["timed_out"])) {
                $return["message"][] = "Stream timeout reached.";
                $return["status"] = 0;
                break;
            }
            
            // if chunked - read line properly
            if ($encoding == self::ENC_TRN_CHUNKED) {
                $length = hexdec($line);
                if ($length == 0) {
                    break;
                }
                
                $line = fread($socket, $length);
                
                $read = strlen($line);
                while ($read < $length && !feof($socket)) {
                    $temp = fread($socket, ($length - $read));
                    $read += strlen($temp);
                    $line .= $temp;
                    $info = stream_get_meta_data($socket);
                    if (!empty($info["timed_out"])) {
                        $return["message"][] = "Stream timeout reached.";
                        $return["status"] = 0;
                        break 2;
                    }
                }
                // need to read trailing CRLF
                fgets($socket);
            }
            
            if ($tempfile) {
                file_put_contents($tempfile, $line, FILE_APPEND);
            } else {
                $return["body"][] = $line;
            }
        }
        
        if ($tempfile) {
            rewind($tempfile);
            $return["body"] = $tempfile;
        }
        
        return $return;
    }
    
    
    
    
}