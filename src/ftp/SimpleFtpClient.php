<?php

namespace hydra\net\ftp;

use hydra\net\ftp\exceptions\FtpClientException;

/**
 * Forked from: https://github.com/suin/php-ftp-client/blob/master/Source/Suin/FTPClient/FTPClient.php
 * TODO >>> update to use multiple data channels 
 * TODO >>> see if active connections can be done (server to client).
 */
class SimpleFtpClient implements SimpleFtpClientInterface
{
    
    protected static $defaults = array (
        "host" => self::LOCALHOST,
        "port" => self::PORT_DEFAULT,
        "mode" => self::TRANSFER_MODE_PASSIVE,
        
        "timeout" => self::TIMEOUT_DEFAULT,
        "recursive" => false,
    );
    
    protected $options = array ();
    
    protected $connection = null;
    
    protected $passive = null;
    
	protected $system = null;
	
	protected $features = null;
	
	protected $history = array ();
    
    public function __construct ($options = array ())
    {
        $this->init($options);
    }
    
    public function init ($options = array ())
    {
        $this->options = array_merge(self::$defaults, $options);
        return $this;
    }
    
    protected function connect ()
    {
        if (!is_resource($this->connection)) {
            
            $op = $this->options;
            $this->connection = fsockopen(
                $op["host"], 
                $op["port"], 
                $errorCode, 
                $errorMessage
            );
                
            if (!is_resource($this->connection)) {
                throw new RuntimeException($errorMessage, $errorCode);
            }
            
            stream_set_blocking($this->connection, true);
		    stream_set_timeout($this->connection, $op["timeout"]);
		    
		    $this->history[] = array (
		        "connecting" => $this->getResponse(),
	        );
	        
	        // try to get the system details
	        $rsp = $this->request("SYST");
	        if ($rsp["code"] === 215) {
    	        $tok = explode(" ", $rsp["message"]);
    	        $this->system = $tok[1];
	        }
        }
        
        return $this;
    }
    
    public function login (
        $username = "",
        $password = ""
    ) {
        
		$response = $this->request(sprintf("USER %s", $username));
		
		$code = $response["code"];
		if ($code !== 331 && $code !== 220) {
		    die ("error connecting");
		}
		
		$response = $this->request(sprintf("PASS %s", $password));
		
		return $this;
    }
    
    public function getSystem ()
    {
        return $this->system;
    }
    
    public function getFeatures ()
    {
        if ($this->features === null) {
            $rsp = $this->request("FEAT");
    		if ($rsp["code"] !== 211) {
    		    var_dump($rsp);
    		    die("unable to get features -- throw exception");
    		}
    		
    		$lines = explode("\n", $rsp["message"]);
    		$lines = array_map("trim", $lines);
    		$lines = array_filter($lines);
    		
    		if (count($lines) < 2) {
    			return false;
    		}
    		
    		$lines = array_slice($lines, 1, count($lines) - 2);
    		$features = array();
    		foreach ($lines as $line) {
    			$tokens = explode(" ", $line);
    			$feature =$tokens[0];
    			$features[$feature] = $line;
    		}
		    $this->features = $features;
        }
        
        return $this->features;
    }
    
    public function disconnect ()
    {
        if ($this->connection) {
            $this->request("QUIT");
    		$this->connection = null;
        }
    }
    
    public function getCurrentDirectory ()
    {
        $rsp = $this->request("PWD");
        
		if ($rsp["code"] !== 257) {
			return false;
		}
		
		preg_match(
	        "/\"([^\"])+\"/", 
	        $rsp["message"], 
	        $matches
        );
        
        return $matches[1];
		
		/*
		$from = strpos($rsp["message"], '"') + 1;
		$to = strrpos($rsp["message"], '"') - $from;
		$dir = substr($rsp["message"], $from, $to);
		return $dir;
		*/
    }
    
    public function changeDirectory (
        $directory = "/"
    ) {
        $rsp = $this->request(sprintf("CWD %s", $directory));
        if (!$rsp["code"] === 250) {
            die ("change directory failed - throw exception");
        }
        
		return $directory;
    }
    
    public function removeDirectory (
        $directory = ""
    ) {
        // TODO >>> update to allow recursive removal
        $response = $this->request(sprintf("RMD %s", $directory));
		return ($response["code"] === 250);
    }
    
    // TODO >>> we should check that the directory does not exist
    // TODO >>> create recursively
    public function createDirectory (
        $directory = ""
    ) {
        $response = $this->request(sprintf("MKD %s", $directory));
		return ($response["code"] === 257);
    }
    
    // we should check that the newname does not exist ...
    public function rename (
        $oldname = "",
        $newname = ""
    ) {
        $response = $this->request(sprintf("RNFR %s", $oldname));
		if ($response["code"] !== 350) {
		    var_dump($response);
			die ("unable to begin rename - throw exception");
		}
		$response = $this->request(sprintf("RNTO %s", $newname));
		if ($response["code"] !== 250) {
			die ("unable to do rename - throw exception");
		}
		return true;
    }
    
    public function removeFile (
        $filename = ""
    ) {
        $response = $this->request(sprintf("DELE %s", $filename));
		return ($response["code"] === 250);
    }
    
    public function setPermission (
        $filename = "",
        $mode = self::PERMISSION_ALL
    ) {
        if (is_integer($mode) === false || 
            $mode < 0 ||
            0777 < $mode 
        ) {
			throw new InvalidArgumentException(sprintf('Invalid permission "%o" was given.', $mode));
		}
		
		$response = $this->request(sprintf("SITE CHMOD %o %s", $mode, $filename));
		return ($response["code"] === 200);
    }
    
    public function getList (
        $directory = "."
    ) {
        $opt = $this->options;
        
        if ($directory === ".") {
            $directory = $this->getCurrentDirectory();
        }
        
        if ($opt["mode"] === self::TRANSFER_MODE_PASSIVE) {
        
            $con = $this->getPassiveConnection();
    		$rsp = $this->request(sprintf("NLST %s", $directory));
    		
    		if ($rsp['code'] !== 150 && 
    		    $rsp['code'] !== 125 
    	    ) {
    	        die ("getList return code failed");
    			return false;
    		}
    		
    		$list = "";
    		while (!feof($con)) {
    			$list .= fread($con, 1024);
    		}
    		
    		// should be a message about closing up the connection
    		$rsp = $this->getResponse ();
		    $this->history[] = array (
		        "</NLST>" => $rsp,
	        );
	        
	        $list = trim($list);
        	$list = preg_split("/[\n\r]+/", $list);
        	return $list;
        }
        
        die ("TODO - active connection");
    }
    
    public function getRawList (
        $directory = ".",
        $parse = true
    ) {
       
        $opt = $this->options;
       
        if ($opt["mode"] === self::TRANSFER_MODE_PASSIVE) {
            $con = $this->getPassiveConnection();
            $rsp = $this->request(sprintf("LIST -a %s", $directory));
    		
    		if ($rsp["code"] !== 150 &&
    		    $rsp["code"] !== 125 
    	    ) {
    			die ("unable to raw list files - throw exception");
    		}
    		
    		$list = '';
    		while (!feof($con)) {
    			$list .= fread($con, 1024);
    		}
    		
    		$rsp = $this->getResponse ();
		    $this->history[] = array (
		        "</LIST>" => $rsp,
	        );
    		
    		$list = trim($list);
    		$list = preg_split("/[\n\r]+/", $list);
    		
    		if ($parse) {
        		
        		$rows = array ();
        		foreach ($list as $row) {
            		preg_match(implode("", array (
                        "/^([\\-dbclps])", // Directory flag [1]
                        "([\\-rwxs]{9})\\s+", // Permissions [2]
                        "(\\d+)\\s+", // Number of items [3]
                        "(\\w+)\\s+", // File owner [4]
                        "(\\w+)\\s+", // File group [5]
                        "(\\d+)\\s+", // File size in bytes [6]
                        "(\\w{3}\\s+\\d{1,2}\\s+", // 3-char month and 1/2-char day of the month [7]
                        "(?:\\d{1,2}:\\d{1,2}|\\d{4}))", // Time or year (need to check conditions) [+= 7]
                        "\\s+(.+)$/" // File/directory name [8] 
    		            )), $row, $matches
    	            );
    	            
    	            $rows[$matches[8]] = array (
    	                "type" => $matches[1] === "d" ? "dir" : "file",
    	                "permissions" => $matches[2],
    	                "items" => $matches[3],
    	                "owner" => $matches[4],
    	                "group" => $matches[5],
    	                "size" => $matches[6],
    	                "date" => $matches[7],
    	                "name" => $matches[8],
    	                
	                );
        		}
        		
        		$list = $rows;
    		}
    		
    		return $list;
        }
        
        die ("active connection " . __FILE__ . __LINE__);
    }
    
    public function getFileSize (
        $filename = ""
    ) {
        // might not support size
        
        if ($this->supports("SIZE") === false){
            die ("does not support size - throw exception");
		}
		
		$rsp = $this->request(sprintf("SIZE %s", $filename));
		if ($rsp["code"] !== 213 ) {
		    die ("unable to get file size - throw exception");
			return false;
		}
		
		if (preg_match(
		    "/^[0-9]{3} (?P<size>[0-9]+)$/", 
		    trim($response['message']), $matches
	    ) === false){
			die ("unable to get file size - throw exception 2");
		}
		
		return intval($matches["size"]);
    }
    
    public function getModifiedDateTime (
        $filename = ""
    ) {
        if ($this->supports("MDTM") === false) {
			die ("does not support modified date - throw exception");
		}
		
		$rsp = $this->request(sprintf("MDTM %s", $filename));
		if ($rsp["code"] !== 213) {
			die ("unable to get modified date - throw exception");
		}
		if (preg_match(
            "/^[0-9]{3} (?P<datetime>[0-9]{14})$/", 
            trim($rsp["message"]), 
            $matches
        ) === false) {
            var_dump($rsp);
			die ("unable to parse modified date - throw exception");
		}
		
		return strtotime("{$matches["datetime"]} UTC");
    }
    
    public function download (
        $remoteFilename = "",
        $localFilename = "",
        $mode = self::TRANSFER_TYPE_BINARY
    ) {
        $opt = $this->options;
        // will this work with temp files ??
        
		/*
		 * WHY USE 'wb' HERE?
		 * As fopen() function modifies line break character like LF, CR and CRLF depending on SAPI,
		 * we use 'b' here in order to receive data as plain.
		 * @see http://www.php.net/manual/en/function.fopen.php
		 */
		 
        $hnd = null;
	    if (is_string($localFilename)) {
		    $hnd = fopen($localFilename, "wb");
	    } 
	    
	    else if (is_resource($localFilename)) {
	        $hnd = $localFilename;
	    } 
	    
	    else if ($localFilename == false) {
	        $hnd = tmpfile();
	    }
	    
		if (!is_resource($hnd)) {
			throw new RuntimeException(sprintf(
	            "Failed to open local file \"%s\"", 
	            $localFilename
            ));
		}
		
		$rsp = $this->request(sprintf("TYPE %s", $mode));
		if ($rsp["code"] !== 200) {
		    var_dump($rsp);
			die ("unable to download file");
		}
		
		if ($opt["mode"] === self::TRANSFER_MODE_PASSIVE) {
		
    		$con = $this->getPassiveConnection();
    		$rsp = $this->request(sprintf("RETR %s", $remoteFilename));
    		if ($rsp["code"] !== 150 && 
    		    $rsp["code"] !== 125 
		    ) {
		        var_dump($rsp);
    			die ("unable to download the file - throw exception");
    		}
    		
    		while (!feof($con)) {
    			fwrite(
			        $hnd, 
			        fread($con, self::BUFFER_LARGE), 
			        self::BUFFER_LARGE
		        );
    		}
    		
    		$rsp = $this->getResponse ();
		    $this->history[] = array (
		        "</RETR>" => $rsp,
	        );
    		
    		return $hnd;
		}
		
		die ("TODO - active download of file");
    }
    
    public function downloadString (
        $remoteFilename = "",
        $mode = self::TRANSFER_TYPE_BINARY
    ) {
		
		$opt = $this->options;
		$rsp = $this->request(sprintf("TYPE %s", $mode));
		if ($rsp["code"] !== 200) {
			return false;
		}
		
		if ($opt["mode"] === self::TRANSFER_MODE_PASSIVE) {
		
		    $con = $this->getPassiveConnection ();
		    $rsp = $this->request(sprintf("RETR %s", $remoteFilename));
    		if ($rsp["code"] !== 150 && 
    		    $rsp["code"] !== 125
		    ) {
		        die ("throw exception - ftp download string");
    		}
    		
            $str = "";
    		while (!feof($con)) {
    			$str .= fread($con, self::BUFFER_LARGE);
    		}
    		
    		$rsp = $this->getResponse ();
		    $this->history[] = array (
		        "</RETR>" => $rsp,
	        );
    		
    		return $str;
		}
		
		die ("active connection download string");
    }
    
    public function upload (
        $localFilename = "",
        $remoteFilename = "",
        $mode = self::TRANSFER_TYPE_BINARY
    ) {
        
        $opt = $this->options;
        
		/*
		 * WHY USE 'rb' HERE?
		 * As fopen() function modifies line break character like LF, CR and CRLF depending on SAPI,
		 * we use 'b' here in order to receive data as plain.
		 * @see http://www.php.net/manual/en/function.fopen.php
		 */
		$hnd = fopen($localFilename, "rb");
		if (is_resource($hnd) === false) {
			throw new RuntimeException(sprintf(
			    "Failed to open local file \"%s\"",
			    $localFilename
		    ));
		}
		
		$rsp = $this->request(sprintf("TYPE %s", $modes[$mode]));
		if ($rsp["code"] !== 200){
		    var_dump($rsp);
		    die("unable to upload file. - throw exception: " . __LINE__);
		}
		
		if ($opt["mode"] === self::TRANSFER_MODE_PASSIVE) {
    		$con = $this->getPassiveConnection();
    		$rsp = $this->request(sprintf("STOR %s", $remoteFilename));
    		if ($rsp["code"] !== 150 && 
    		    $rsp["code"] !== 125 
		    ) {
    			var_dump($rsp);
    			die ("unable to upload file - throw exception: " . __LINE__);
    		}
    		
    		while (feof($hnd) === false) {
    			fwrite(
    			    $con, 
    			    fread($hnd, self::BUFFER_LARGE), 
    			    self::BUFFER_LARGE
			    );
    		}
    		
    		fclose($con);
    		
    		$rsp = $this->getResponse ();
		    $this->history[] = array (
		        "</STOR>" => $rsp,
	        );
    		
    		return $this;
		}
		
		die ("TODO - active upload connection");
		
		
    }
    
    public function uploadString (
        $content = "",
        $remoteFile = "",
        $mode = self::TRANSFER_TYPE_BINARY
    ) {
        $opt = $this->options;
        
		$rsp = $this->request(sprintf("TYPE %s", $mode));
		if ($rsp["code"] !== 200) {
		    var_dump ($rsp);
		    die ("unable to get type for upload string - throw exception");
		}
		
		if ($opt["mode"] === self::TRANSFER_MODE_PASSIVE) {
		
    		$con = $this->getPassiveConnection();
    		$rsp = $this->request(sprintf("STOR %s", $remoteFile));
    		if ($rsp["code"] !== 150 &&
    		    $rsp["code"] !== 125
    	    ) {
    	        var_dump($rsp);
    	        die("unable to upload string with stor command - throw exception");
    		}
    		
    		$done = fwrite($con, $content);
    		fclose($con);
    		
    		$rsp = $this->getResponse ();
		    $this->history[] = array (
		        "</STOR>" => $rsp,
	        );
	        
    		return $this;
		}
		
		die ("TODO - active upload string");
    }
    
    
    public function request (
        $command = ""
    ) {
        
        // connect if need be
        $this->connect();
        
        $command = $command . self::CRLF;
        fputs($this->connection, $command);
        
        // ...
        
        $response = $this->getResponse();
        
        $this->history[] = array (
            $command => $response,
        );
        
        return $response;
    }
    
    protected function getResponse ()
    {
        $message = "";
        $code = -1;
        $safety = 50;
        
        while (true) {
            $line = fgets($this->connection, 8192);
			$message .= $line;
			if (preg_match("/^[0-9]{3} /", $line)) {
				break;
			}
			
			// prevent infinite looping
			if (!$line && !($safety--)) {
			    break;
			}
        }
        
        if ($message) {
            $code = intval(substr(ltrim($message), 0, 3));
        }
        
        return array (
            "code" => $code,
            "message" => $message,
        );
    }
    
    protected function getPassiveConnection ()
    {
        
        $rsp = $this->request("PASV");
        $opt = $this->options;
        
        if (!preg_match(
            "/\((?P<host>[0-9,]+),(?P<port1>[0-9]+),(?P<port2>[0-9]+)\)/",
            $rsp["message"],
            $matches
        )) {
            die ("unable to get pasv connection - throw exception");
			return false;
		}
		
		$host = strtr($matches["host"], ",", ".");
		
		// low bit * 256 + high bit
		$port = ($matches["port1"] * 256) + $matches["port2"];
        
        $conn = fsockopen(
            $host, 
            $port, 
            $errorNumber, 
            $errorString, 
            $opt["timeout"]
        );
        
		if (!is_resource($conn)) {
			die("passive connection failed - throw exception");
		}
		
		stream_set_blocking($conn, true);
		stream_set_timeout($conn, $opt["timeout"]);
		
		return $conn;
    }
    
    public function exists (
        $file = ""
    ) {
        
    }
    
    public function supports ($command = "")
    {
        $features = $this->getFeatures();
		return array_key_exists($command, $features);
    }
    
    public function getHistory ()
    {
        return $this->history;
    }
}