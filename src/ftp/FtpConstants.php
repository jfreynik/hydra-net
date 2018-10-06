<?php

namespace hydra\net\ftp;

interface FtpConstants
{
    
    const CRLF = "\r\n";
    
    const TRANSFER_TYPE_ASCII = "A";
    
    const TRANSFER_TYPE_BINARY = "I";
    
    const TRANSFER_MODE_PASSIVE = 1;
    
    const TRANSFER_MODE_ACTIVE = 2;
    
    const PORT_DEFAULT = 21;
    
    const PERMISSION_ALL = 0777;
    
    const PERMISSION_DIR_DEFAULT = 0755;
    
    const PERMISSION_FILE_DEFAULT = 0644;
    
    const LOCALHOST = "localhost";
    
    const TIMEOUT_DEFAULT = 60;
    
    const TIMEOUT_SHORT = 30;
    
    const TIMEOUT_LONG = 90;
    
    const BUFFER_SMALL = 1024;
    
    const BUFFER_MEDIUM = 4096;
    
    const BUFFER_LARGE = 8192;
    
    // https://www.serv-u.com/features/file-transfer-protocol-server-linux/commands
    // http://www.nsftools.com/tips/RawFTP.htm
    const CMD_ACCOUNT = "account";
    
    const CMD_APPEND = "append";
    
    const CMD_ASCII = "ascii";
    
    const SEP_UNIX = "/";
    
    const SEP_WINDOWS = "\\";
    
    
    
    
}