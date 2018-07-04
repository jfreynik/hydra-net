<?php

namespace hydra\net;

interface SimpleFtpClientInterface extends FtpInterface
{
    
    public function __construct (
        $host = "",
        $port = 21,
        $mode = 
    );
    
    public function login (
        
    );
    
}