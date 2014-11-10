<?php

namespace Kanti;

class HubUpdater {
	
	protected $options = [
		"cacheFile" => "downloadInfo.json",
		"versionFile" => "installedVersion.json",
		"zipFile" => "tmpZipFile.zip",
		
		"name" => "",
		"branch" => "master",
		"cache" => "cache/",
		"save" => "",
		"prerelease" => false,
	];
	
	protected $allRelease = [];
    protected $newestRelease = [];
    protected $streamContext = null;

    public function __construct($option) {
		if(is_array($option))
		{
			if(! isset($option['name']))
			{
				throw new Exception('No Name in Option Set');
			}
			$this->options = $option + $this->options;
		}
		else if(is_string($option))
		{
			$this->options['name'] = $option;
		}
		else
		{
			throw new Exception('No Option Set');
		}
	
		$this->options['cache'] = rtrim($this->options['cache'],'/');
		if($this->options['cache'] !== ''){
			$this->options['cache'] .= '/';			
			if (!file_exists($this->options['cache'])) {
				mkdir($this->options['cache']);
			}
		}
		$this->options['save'] = rtrim($this->options['save'],'/');
		if($this->options['save'] !== ''){
			$this->options['save'] .= '/';			
			if (!file_exists($this->options['save'])) {
				mkdir($this->options['save']);
			}
		}
		
		$this->cachedInfo = new CacheOneFile($this->options['cache'] . $this->options['cacheFile']);

        $this->streamContext = stream_context_create(
			array(
				'http' => array(
					'header' => "User-Agent: Awesome-Update-My-Self-" . $this->options['name'] . "\r\n
								 Accept: application/vnd.github.v3+json",
				),
				'ssl' => array(
					'cafile' => dirname(__FILE__) . '/ca_bundle.crt',
					'verify_peer' => true,
				),
			)
        );
        $this->streamContext2 = stream_context_create(
			array(
				'http' => array(
					'header' => "User-Agent: Awesome-Update-My-Self-" . $this->options['name'] . "\r\n",
				),
				'ssl' => array(
					'cafile' => dirname(__FILE__) . '/ca_bundle.crt',
					'verify_peer' => true,
				),
			)
        );
        $this->allRelease = $this->getRemoteInfos();
    }

    protected function getRemoteInfos() {
        $path = "https://api.github.com/repos/" . $this->options['name'] ."/releases";
		if ($this->cachedInfo->is()) {
            $fileContent = $this->cachedInfo->get();
        } else {
            if (!in_array('https', stream_get_wrappers())) {
                return array();
            }
            $fileContent = @file_get_contents($path, false, $this->streamContext);

            if ($fileContent === false) {
                return array();
            }
            $json = json_decode($fileContent, true);
			if(isset($json['message']))
			{
				$json = [];
			}
            $fileContent = json_encode($json, JSON_PRETTY_PRINT);
            $this->cachedInfo->set($fileContent);
            return $json;
        }
        return json_decode($fileContent, true);
    }

    public function able() {
        if (!in_array('https', stream_get_wrappers()))
            return false;
        if (empty($this->allRelease))
            return false;
		
		foreach($this->allRelease as $release)
		{
			if(!$this->options['prerelease'] && $release['prerelease'])
				continue;
			if($this->options['branch'] !== $release['target_commitish'])
				continue;
			$this->newestRelease = $release;
			break;
		}
		
        if (file_exists($this->options['cache'] . $this->options['versionFile'])) {
            $fileContent = file_get_contents($this->options['cache'] . $this->options['versionFile']);
            $current = json_decode($fileContent, true);

            if (isset($current['id']) && $current['id'] == $this->newestRelease['id'])
                return false;
            if (isset($current['tag_name']) && $current['tag_name'] == $this->newestRelease['tag_name'])
                return false;
        }
        return true;
    }

    public function update() {
        if ($this->able()) {
            if ($this->download($this->newestRelease['zipball_url'] )) {
                if ($this->unZip()) {
                    unlink($this->options['cache'] . $this->options['zipFile']);
                    file_put_contents($this->options['cache'] . $this->options['versionFile'], json_encode(array(
                        "id" => $this->newestRelease['id'],
                        "tag_name" => $this->newestRelease['tag_name']
                                    ), JSON_PRETTY_PRINT));
                    return true;
                }
            }
        }
        return false;
    }

    protected function download($url) {
        $file = @fopen($url, 'r', false, $this->streamContext2);
        if ($file == false)
            return false;
        file_put_contents(dirname($_SERVER['SCRIPT_FILENAME']) . "/" . $this->options['cache'] . $this->options['zipFile'], $file);
		fclose($file);
        return true;
    }

    protected function unZip() {
        $path = dirname($_SERVER['SCRIPT_FILENAME']) . "/" . $this->options['cache'] . $this->options['zipFile'];
		
        $zip = new \ZipArchive;
        if ($zip->open($path) === true) {
            $cutLength = strlen($zip->getNameIndex(0));
            for ($i = 1; $i < $zip->numFiles; $i++) {//iterate throw the Zip
                $fileName = $zip->getNameIndex($i);
                $stat = $zip->statIndex($i);
                if ($stat["crc"] == 0) {
                    $dirName = $this->options['save'] . substr($fileName, $cutLength);
                    if (!file_exists($dirName)) {
                        mkdir($dirName);
                    }
                } else {
                    copy("zip://" . $path . "#" . $fileName, $this->options['save'] . substr($fileName, $cutLength));
                }
            }
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    public function printOne() {
        $string = "<h3>Updated to<h3>";
        $string .= "<h2>[" . $this->newestRelease['tag_name'] . "] " . $this->newestRelease['name'] . "</h2>\n";
        $string .= "<p>" . $this->newestRelease['body'] . "</p>\n";
        return $string;
    }

    public function getName() {
        return $this->newestRelease['tag_name'];
    }
}