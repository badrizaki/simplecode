<?php namespace system\libraries;

/**
  *  Name         : Generator Unique Code
  *  Description  : This function for generate unique code.
  *  @copyright   : Badri Zaki
  *  @version     : 1.2, 2015
  *  @author      : Badri Zaki - badrizaki@gmail.com
  *

    How to use :
		$length			= "7"; // Length character
		$format 		= "";
		$prefix_name 	= ""; //  prefix name unique code
		$expired 		= ""; // expired date
		$folder 		= ""; // folder for save unique code
        $strGen 		= new stringGenerator();
        $strGen->acceptChars = "123456789WERTYUPASDFGHJKZXCVBNM"; // 0oiOlILqQ
        $strGen->generate($pin_length,$pin_format,$prefix_name,"",$expired,$folder);

**/

class generatorUniqueCode
{
	public $acceptChars;
	public $length;
	public $prefix;
	public $postfix;
	public $expiry;
	public $keywordData;
	public $stringsData;
	public $useExpired;

	private $acceptCharsArray;

	public function __construct($strLen = "", $strChars = "", $strPrefix = "", $strPostfix = "", $strExpiry = "", $strData = "")
	{
		// Initialize Public Variables
		$this->useExpired = FALSE; ## IF USE EXPIRED SET TRUE
		$this->acceptChars = ($this->acceptChars) ? $this->acceptChars : "1234567890QWERTYUIOPASDFGHJKLZXCVBNM" ;
		$this->prefix = ($this->prefix) ? $this->prefix : "" ;
		$this->postfix = ($this->postfix) ? $this->postfix : "" ;
		$this->length = ($this->length) ? $this->length : 20 ;
		$this->expiry = ($this->expiry) ? $this->expiry : 5 * (60*60*24*365.25) ;
		$this->keywordData = ($this->keywordData) ? $this->keywordData : getcwd()."/tmp/stringGenerator" ;
		$this->stringsData = array() ;
		
		// Initialize Private Variables
		$this->acceptCharsArray = array() ;		
		
		$strLen = trim($strLen) ;
		$strChars = trim($strChars) ;
		$strPrefix = trim($strPrefix) ;
		$strPostfix = trim($strPostfix) ;
		$strExpiry = trim($strExpiry) ;
		$strData = trim($strData) ;
		if ($strLen || $strChars || $strPrefix || $strPostfix || $strExpiry || $strData)
		{
			return $this->generate($strLen, $strChars, $strPrefix, $strPostfix, $strExpiry, $strData) ;
		}
	}
	
	public function __destruct()
	{
		// Unset Public Variables 
		unset($this->acceptChars, $this->length, $this->prefix, $this->postfix, $this->expiry) ;
		
		// Unset Private Variables 
		unset($this->keywordData, $this->acceptCharsArray) ;
	}
	
	public function generate($strLen = "", $strChars = "", $strPrefix = "", $strPostfix = "", $strExpiry = "", $strData = "")
	{
		$this->acceptChars = (trim($strChars))?trim($strChars):$this->acceptChars ;
		$this->length = (trim($strLen))?trim($strLen):$this->length ;
		$this->prefix = (trim($strPrefix))?trim($strPrefix):$this->prefix ;
		$this->postfix = (trim($strPostfix))?trim($strPostfix):$this->postfix ;
		$this->expiry = (trim($strExpiry))?trim($strExpiry):$this->expiry ;
		$this->keywordData = (trim($strData))?trim($strData):$this->keywordData ;
		$this->setAcceptedChars() ;
		
		$generated = "" ;
		
		if (!is_dir($this->keywordData)) {
			mkdir($this->keywordData,0777,true) ;
		}
		if (is_dir($this->keywordData)) {
			$fileToCheck = "" ;
			$success = false ;
			$counter = 0 ;
			while (true) {
				$counter++ ;
				$generated = $this->randomChars() ;
				$fileToCheck = sprintf("%s/%s",$this->keywordData,$generated) ;
				if ($counter >= 1000000) {
					break ;
				}					

				## CHECK EXPIRY
				if ($this->useExpired)
				{
					if(trim($this->expiry) != "false")
					{
						if (file_exists($fileToCheck)) {
							$fileTime = trim(file_get_contents($fileToCheck)) ;
							if (time() - $fileTime >= $this->expiry) {
								$success = true ;
								break ;
							}
						} else {
							$success = true ;
							break ;
						}
					}
				}
				if(file_exists($fileToCheck)) {
					$success = false ;
					//break ;
				}else{
					$success = true ;
					break ;
				}
			}
			
			if ($success)
			{
				if ($this->useExpired)
				{
					$FH = fopen($fileToCheck,"w") ;
					if ($FH)
					{
						$counter = 0 ;
						while (fwrite($FH,time()) === false)
						{
							$success = false ;
							usleep(100) ;
							$counter++ ;
							//if ($counter > 50) {
							if ($counter > 10000) {
								break ;
							}
						}
						fclose($FH) ;
					} else {
						$success = false ;
					}
				} else {
					file_put_contents($fileToCheck, "");
				}
			}
		}
		
		if (!$success) {
			$generated = "" ;
		}
		
		return $generated ;
	}
	
	public function restoreStrings($strData = "")
	{
		$this->stringsData = ($strData)?$strData:$this->stringsData ;
		if (!is_array($this->stringsData)) {
			$this->stringsData = array($this->stringsData) ; 
		}
		if (!is_dir($this->keywordData)) {
			mkdir($this->keywordData,0777,true) ;
		}
		if (is_dir($this->keywordData)) {
			foreach ($this->stringsData as $key => $filename) {
				$fileToCheck = sprintf("%s/%s",$this->keywordData,$filename) ;
				$FH = fopen($fileToCheck,"w") ;
				if ($FH) {
					$counter = 0 ;
					while (fwrite($FH,time()) === false) {
						usleep(100) ;
						$counter++ ;
						if ($counter > 100) {
							break ;
						}
					}
					fclose($FH) ;
				}
			}
			return true ;
		} else {
			return false ;
		}
	}
	
	private function setAcceptedChars()
	{
		$this->acceptCharsArray = str_split($this->acceptChars,1) ;
	}
	
	private function randomChars()
	{
		$newPassword = $this->prefix ;
		$acceptLength = count($this->acceptCharsArray) ;
		$newLength = $this->length - strlen($this->prefix) - strlen($this->postfix) ;
		for ($i = 0; $i < $newLength; $i++) {
			$rIndex = rand(0,$acceptLength-1) ;
			$newPassword .= $this->acceptCharsArray[$rIndex] ;
		}
		$newPassword .= $this->postfix ;
		return $newPassword ;
	}
}
?>