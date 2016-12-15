<?php namespace system\libraries;

class file
{
	public $fileFolder;
	public $fileName;
	public $fileContent;
	public $fileLimit;
	public $fileList;
	public $fileCounter;
	public $status;

	public function __construct()
	{
		$this->fileFolder 	= "";
		$this->fileName 	= "";
		$this->fileContent 	= "";
		$this->fileLimit 	= 50;
		$this->fileList 	= array();
		$this->fileCounter 	= 0;
		$this->status 	= "";
	}

	public function __destruct()
	{
		unset($this->fileFolder);
		unset($this->fileName);
		unset($this->fileContent);
		unset($this->fileLimit);
		unset($this->fileList);
		unset($this->fileCounter);
		unset($this->status);
	}

	public function createFile($fileName = "", $content = "", $folder = "", $overwrite = false)
	{
		$this->fileFolder 	= ($folder)?$folder:$this->fileFolder;
		$this->fileName 	= ($fileName)?$fileName:$this->fileName;
		$this->fileContent 	= ($content)?$content:$this->fileContent;
		if ($this->fileFolder && $this->fileName && $this->fileContent)
		{
			// $Folder = $this->fixFolder($this->fileFolder."/".date("\yY\/\mm\/\dd\/\hH\/\mi\/\ss"));
			$Folder = $this->fixFolder($this->fileFolder);
			$hasHandle = false;
			$handleCounter = 0;

			do {
				if (!is_dir($Folder)) mkdir($Folder, 0777, true);

				if ($overwrite) unlink($Folder."/".$this->fileName);
				
				$FH = fopen($Folder."/".$this->fileName,"a+");
				if ($FH)
				{
					$hasHandle = true;
					if (fwrite($FH,$this->fileContent) === false)
						$hasHandle = false;
				}
				else {
					usleep(500);
					$handleCounter++;
				}
			} while (!$hasHandle && $handleCounter < 10);	
			
			fclose($FH);
			unset($FH);

			if ($hasHandle == true)
				return $Folder."/".$this->fileName;
			else
				return false;

		}
		else {
			$this->status = "error";
			return false;
		}
	}

	public function getFiles($folder, $limit = "")
	{
		$this->fileFolder = ($folder)?$folder:$this->fileFolder;
		$this->fileLimit = ($limit)?$limit:$this->fileLimit;

		if ($this->fileFolder)
		{
			$this->fileList = array();
			$this->fileCounter = 0;
			if (!is_dir($this->fileFolder))
				mkdir($this->fileFolder,0777,true);

			$this->getFileList($this->fixFolder($this->fileFolder));
			return $this->fileList;
		}
		else {
			$this->status = "error";
			return false;
		}
	}

	private function getFileList($folder)
	{
		if (file_exists($folder) && is_dir($folder))
		{
			if ($DH = opendir($folder))
			{
				$folders = array();
				while (($file = readdir($DH)) !== false)
				{
					if ($file != "." && $file != "..")
						array_push($folders,$file);
				}

				sort($folders);
				$filesCounter = 0;
				foreach ($folders as $key => $file)
				{
					if ($file != "." && $file != "..")
					{
						$filesCounter++;
						$filename = $this->fixFolder($folder."/".$file);
						if (is_dir($filename))
							$this->getFileList($filename);
						else {
							if ((time() - filemtime($filename)) > 1)
							{
								array_push($this->fileList,$filename);
								$this->fileCounter++;
							}
						}
					}
					
					if ($filesCounter == 0)
					{
						$folderStr = preg_replace("/\D/","",preg_replace("/(.+\/)(y".date("Y")."\/)/","$2",$folder));
						$dateStr = substr(date("YmdHis"),0,strlen($folderStr));
						if ($folderStr != $dateStr)
							rmdir($folders);
					}

					if ($this->fileCounter >= $this->fileLimit)
						break;
				}
				closedir($DH);
				unset($DH, $file);
			}
		}
	}

	public function cleanFolder($folder, $force = false)
	{
		$this->fileFolder = ($folder)?$folder:$this->fileFolder;
		if ($this->fileFolder)
		{
			$this->fileCounter = 0;
			$errorReportingConst = ini_get('error_reporting');
			error_reporting(1);
			$this->cleanFolderList($this->fixFolder($this->fileFolder),$force);
			error_reporting($errorReportingConst);
			return $this->fileCounter;
		}
		else {
			$this->status = "error";
			return false;
		}
	}

	private function cleanFolderList($folder, $force = false)
	{
		$limitFolder = $this->fileLimit * 1;
		if (is_dir($folder) && ($this->fileCounter <= $limitFolder || $force === true))
		{
			if ($DH = opendir($folder))
			{
				$folders = array();
				while (($file = readdir($DH)) !== false)
				{
					if ($file != "." && $file != "..")
					{
						if (is_dir($folder."/".$file))
							array_push($folders,$file);
					}
				}

				sort($folders);
				foreach ($folders as $key => $file)
				{
					$filename = $this->fixFolder($folder."/".$file);
					$dateDiff = time() - filemtime($filename);
					if (is_dir($filename) && $dateDiff > 1)
					{
						if ($filename != $this->fileFolder)
						{
							if (rmdir($filename) === false)
							{
								if (!preg_match("/\/h\d\d\/m\d\d\/s\d\d$/",$filename))
									$this->cleanFolderList($filename,$force);
							}
							$this->fileCounter++;
						}
					}
				}
				closedir($DH);
				unset($DH, $file);
			}
		}
	}

	private function fixFolder($folder = '')
	{
		$folder = preg_replace("/\/+/i","/",$folder);
		$folder = preg_replace("/\/+$/i","",$folder);
		return $folder;
	}
}