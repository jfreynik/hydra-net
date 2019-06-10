<?php

namespace hydra\net;

/**
 * /search/user/{type}/{keyword}
 * 
 */
class UrlTemplate 
{
    
    protected $template = "";
    
    public function __construct ($template = "") 
    {
        $this->template = $template;
    }
    
    public function build ($data = array (), $separator = "--")
    {
        $temp = $this->template;
        foreach ($data as $key => $value) {
            if ($value === "" || 
                $value === null ||
                (
                    is_array($value) &&
                    empty($value)
                )
            ) {
                // allow zero and other falsey values
                continue;
            }
            
            else if (is_array($value)) {
                $value = implode($separator, $value);
            }
            
            if (strpos($temp, "{{$key}}") !== false) {
                $temp = str_replace("{{$key}}", "/{$value}/", $temp);
            }
            
            else if (strpos($temp, "{?{$key}}") !== false) {
                $temp = str_replace("{?{$key}}", "/{$key}/{$value}/", $temp);
            }
        }
        
        // remove excess tokens
        $temp = preg_replace ("/{[^}]+}/", "", $temp);
        
        // remove any excess slashes
        do { 
            $temp = str_replace("//", "/", $temp, $count);
        } while ($count);
        
        // is this necessary?
        return rtrim($temp, "/");
    }
    
    public function __toString ()
    {
        return $template;
    }
    
}