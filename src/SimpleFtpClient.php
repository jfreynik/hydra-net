<?php


namespace hydra\net;

class SimpleFtpClient implements FtpConstants
{
    protected $handle = "";
    
    public function __construct (
        $host = "",
        $port = 21,
        $transferMode = self::TRANSFER_MODE_PASSIVE,
        $transferType = self::TRANSFER_TYPE_ASCII
    ) {
        
    }
    
    protected function makeRequest ($request = "")
    {
        $request .= self::CRLF;
    }
    
    protected function getResponse ()
    {
        $code = 0;
        $message = "";
        
        while (true) {
			$line = fgets($this->connection, 8129);
			$message .= $line;
			if (preg_match('/^[0-9]{3} /', $line)) {
				break;
			}
		}
		$code = intval(substr(ltrim($message), 0, 3));
        return array (
            "code" => $code,
            "message" => $message,
        );
    }
    
}