<?php

namespace hydra\net\ftp;

interface SimpleFtpClientInterface extends FtpConstants
{
    
    /**
     * 
     * /
    public function __construct (
        $host = "", 
        $port = self::PORT_DEFAULT,
        $mode = self::TRANSFER_MODE_PASSIVE
    );
    */
    
    /**
	 * Login to the server.
	 * @param string $username
	 * @param string $password
	 * @return bool If success return TRUE, fail return FALSE.
	 */
    public function login (
        $username = "", 
        $password = ""
    );
    
    /**
	 * Return the system name.
	 * @abstract
	 * @return string|bool If error returns FALSE
	 */
	public function getSystem ();
	
	/**
	 * Return the features.
	 * @abstract
	 * @return array|bool If error returns FALSE
	 */
	public function getFeatures ();
	
	/**
	 * Close the connection.
	 * @abstract
	 * @return void
	 */
	public function disconnect ();
	
	/**
	 * Return the current directory name.
	 * @abstract
	 * @return string|bool If error, returns FALSE.
	 */
	public function getCurrentDirectory ();
	
	/**
	 * Change the current directory on a FTP server.
	 * @abstract
	 * @param string $directory
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function changeDirectory (
        $directory = ""
    );
	
	/**
	 * Remove a directory.
	 * @abstract
	 * @param string $directory
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function removeDirectory (
	    $directory = ""
    );
    
	/**
	 * Create a directory.
	 * @abstract
	 * @param string $directory
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function createDirectory (
        $directory = ""
    );
    
	/**
	 * Rename a file or a directory on the FTP server.
	 * @abstract
	 * @param string $oldName
	 * @param string $newName
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function rename (
	    $oldName = "", 
	    $newName = ""
    );
	
	/**
	 * Delete a file on the FTP server.
	 * @abstract
	 * @param string $filename
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function removeFile (
	    $filename = ""
    );
	
	/**
	 * Set permissions on a file via FTP.
	 * @abstract
	 * @param string $filename
	 * @param int $mode The new permissions, given as an octal value.
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function setPermission (
	    $filename = "",
	    $mode = self::PERMISSION_ALL
    );
    
	/**
	 * Return a list of files in the given directory.
	 * @abstract
	 * @param string $directory
	 * @return array|bool If error, returns FALSE.
	 */
	public function getList (
	    $directory = "."
    );
	
	/**
	 * Return the size of the given file.
	 * @abstract
	 * @param string $filename
	 * @return int|bool If failed to get file size, returns FALSE
	 * @note Not all servers support this feature!
	 */
	public function getFileSize (
	    $filename = ""
    );
    
	/**
	 * Return the last modified time of the given file.
	 * @abstract
	 * @param string $filename
	 * @return int|bool Returns the last modified time as a Unix timestamp on success, or FALSE on error.
	 * @note Not all servers support this feature!
	 */
	public function getModifiedDateTime (
	    $filename = ""
    );
	
	/**
	 * Download a file from the FTP server.
	 * @abstract
	 * @param string $remoteFilename
	 * @param string $localFilename
	 * @param int $mode MODE_ASCII or MODE_BINARY
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function download(
	    $remoteFilename = "", 
	    $localFilename = "", 
	    $mode = self::MODE_BINARY
    );
	
	/**
	 * Upload a file to the FTP server.
	 * @abstract
	 * @param string $localFilename
	 * @param string $remoteFilename
	 * @param int $mode MODE_ASCII or MODE_BINARY
	 * @return bool If success return TRUE, fail return FALSE.
	 */
	public function upload (
	    $localFilename = "", 
	    $remoteFilename = "", 
	    $mode = self::MODE_BINARY
    );
    
    /**
     * 
     */
    public function exists (
    	$file = ""
	);
    
    
}