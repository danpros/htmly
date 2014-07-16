<?php

class CacheOneFile
{
	protected $fileName = "";
	protected $holdTime = 43200;//12h
	
	public function __construct($fileName , $holdTime = 43200)
	{
		$this->fileName = $fileName;
		$this->holdTime = $holdTime;
	}

	public function is()
	{
		if(! file_exists($this->fileName))
			return false;
		if(filemtime($this->fileName) < ( time() - $this->holdTime ) )
		{
			unlink($this->fileName);
			return false;
		}
		return true;
	}
	public function get()
	{
		return file_get_contents($this->fileName);
	}
	public function set($content)
	{
		file_put_contents($this->fileName,$content);
	}
}

class Updater
{
	protected $cachedInfo = "cache/downloadInfo.json";
	protected $versionFile = "cache/installedVersion.json";
	protected $zipFile = "cache/tmpZipFile.zip";
	
	protected $infos = [];
	
	public function __construct()
	{
		if(! file_exists("cache/"))
		{
			mkdir("cache/");
		}
		$this->cachedInfo = new CacheOneFile($this->cachedInfo);
		$this->infos = $this->getInfos();
	}
	
	protected function getInfos()
	{
		$path = "https://api.github.com/repos/danpros/htmly/releases";
		if($this->cachedInfo->is())
		{
			$fileContent = $this->cachedInfo->get();
		}
		else
		{
			$fileContent = @file_get_contents($path,false, stream_context_create(['http'=>['header'=>"User-Agent: Awesome-Update-My-Self\r\n"]]));
			if($fileContent == false)
			{
				return [];
			}
			$json = json_decode($fileContent,true);
			$fileContent = json_encode($json, JSON_PRETTY_PRINT);
			$this->cachedInfo->set($fileContent);
			return $json;
		}
		return json_decode($fileContent,true);
	}

	public function updateAble()
	{
		if(empty($this->infos))
			return false;

		if(file_exists($this->versionFile))
		{
			$fileContent = file_get_contents($this->versionFile);
			$current = json_decode($fileContent,true);
		
			if(isset($current['id']) && $current['id'] == $this->infos[0]['id'])
				return false;
			if(isset($current['tag_name']) && $current['tag_name'] == $this->infos[0]['tag_name'])
				return false;
		}
		return true;
	}
	
	public function update()
	{
		if($this->updateAble())
		{
			if($this->download("https://github.com/danpros/htmly/archive/" . $this->infos[0]['tag_name'] . ".zip"))
			{
				if($this->unZip())
				{
					unlink($this->zipFile);
					file_put_contents($this->versionFile, json_encode([
						"id" => $this->infos[0]['id'],
						"tag_name" => $this->infos[0]['tag_name']
					], JSON_PRETTY_PRINT));
					return true;
				}
			}
		}
		return false;
	}	
	protected function download($url)
	{
		$file = @fopen($url, 'r');
		if($file == false)
			return false;
		file_put_contents($this->zipFile, $file);
		return true;
	}
	protected function unZip()
	{
		$path = dirname($_SERVER['SCRIPT_FILENAME']) . "/" . $this->zipFile;
		
		$zip = new ZipArchive;
		if ($zip->open($path) === true) {
			$cutLength = strlen($zip->getNameIndex(0));
			for($i = 1; $i < $zip->numFiles; $i++) {//iterate throw the Zip
				$fileName = $zip->getNameIndex($i);
				if($zip->statIndex($i)["crc"] == 0)
				{
					$dirName = substr($fileName,$cutLength);
					if(! file_exists($dirName))
					{
						mkdir($dirName);
					}
				}
				else{
					copy("zip://".$path."#".$fileName, substr($fileName,$cutLength));
				}
			}                   
			$zip->close();
			return true;
		}
		else{
			return false;
		}
	}
	
	public function printOne()
	{
		$releases = $this->infos;
		$string = "<h3>Updated to<h3>";
		$string .= "<h2>[" . $releases[0]['tag_name'] . "] " . $releases[0]['name'] . "</h2>\n";
		$string .= "<p>" . $releases[0]['body'] . "</p>\n";
		return $string;
	}
	
	public function getName()
	{
		return $this->infos[0]['tag_name'];
	}
}