<?php

namespace hydra\net;

interface FtpConstants
{
    
    const CRLF = "\r\n";
    
    const TRANSFER_TYPE_ASCII = 1;
    const TRANSFER_TYPE_BINARY = 2;
    
    const TRANSFER_MODE_PASSIVE = 1;
    const TRANSFER_MODE_ACTIVE = 2;
    
    // https://www.serv-u.com/features/file-transfer-protocol-server-linux/commands
    // http://www.nsftools.com/tips/RawFTP.htm
    const CMD_ACCOUNT = "account";
    const CMD_APPEND = "append";
    const CMD_ASCII = "ascii";
    
    
}