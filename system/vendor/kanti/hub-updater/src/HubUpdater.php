<?php

namespace Kanti;

use Composer\CaBundle\CaBundle;
#[\AllowDynamicProperties]
class HubUpdater
{
    const JSON_PRETTY_PRINT = 128;

    /**
     * @var array
     */
    protected $options = array(
        "cacheFile" => "downloadInfo.json",
        "holdTime" => 43200,

        "versionFile" => "installedVersion.json",
        "zipFile" => "tmpZipFile.zip",
        "updateignore" => ".updateignore",

        "name" => "",
        "branch" => "master",
        "cache" => "cache/",
        "save" => "",
        "prerelease" => false,
        "auth" => null,

        "exceptions" => false,
    );
    /**
     * @var array
     */
    protected $allRelease = array();
    /**
     * @var array
     */
    protected $newestInfo = null;
    /**
     * @var array
     */
    protected $currentInfo = null;
    /**
     * @var null|resource
     */
    protected $streamContext = null;
    /**
     * @var null|resource
     */
    protected $streamContext2 = null;


    /**
     * HubUpdater constructor.
     * @param array|string $option
     * @throws \Exception
     */
    public function __construct($option)
    {
        if (!in_array('https', stream_get_wrappers())) {
            throw new \Exception("No HTTPS Wrapper Exception");
        }
        $this->setOptions($option);

        $this->options['save'] = rtrim($this->options['save'], '/');
        if ($this->options['save'] !== '') {
            $this->options['save'] .= '/';
            if (!file_exists($this->options['save'])) {
                mkdir($this->options['save']);
            }
        }
        $this->options['cache'] = $this->options['save'] . rtrim($this->options['cache'], '/');
        if ($this->options['cache'] !== '') {
            $this->options['cache'] .= '/';
            if (!file_exists($this->options['cache'])) {
                mkdir($this->options['cache']);
            }
        }

        $this->cachedInfo = new CacheOneFile(
            $this->options['cache'] . $this->options['cacheFile'],
            $this->options['holdTime']
        );

        $additionalHeader = '';
        if ($this->options['auth']) {
            $additionalHeader .= "Authorization: Basic " . base64_encode($this->options['auth']) . "\r\n";
        }

        $caFilePath = CaBundle::getSystemCaRootBundlePath();
        $this->streamContext = stream_context_create(
            array(
                'http' => array(
                    'header' => "User-Agent: Awesome-Update-My-Self-" . $this->options['name'] . "\r\n"
                        . "Accept: application/vnd.github.v3+json\r\n"
                        . $additionalHeader,
                ),
                'ssl' => array(
                    'cafile' => $caFilePath,
                    'verify_peer' => true,
                ),
            )
        );
        $this->streamContext2 = stream_context_create(
            array(
                'http' => array(
                    'header' => "User-Agent: Awesome-Update-My-Self-" . $this->options['name'] . "\r\n"
                        . $additionalHeader,
                ),
                'ssl' => array(
                    'cafile' => $caFilePath,
                    'verify_peer' => true,
                ),
            )
        );
        $this->allRelease = $this->getRemoteInfo();
    }

    protected function getRemoteInfo()
    {
        if ($this->cachedInfo->has()) {
            return json_decode($this->cachedInfo->get(), true);
        }
        $path = "https://api.github.com/repos/" . $this->options['name'] . "/releases";
        $fileContent = @file_get_contents($path, false, $this->streamContext);

        if ($fileContent === false) {
            if ($this->options["exceptions"]) {
                throw new \Exception("HTTP Exception");
            }
            return array();
        }
        $json = json_decode($fileContent, true);
        if (isset($json['message'])) {
            if ($this->options["exceptions"]) {
                throw new \Exception("API Exception[" . $json['message'] . "]");
            }
            $json = array();
        }
        $fileContent = json_encode($json, static::JSON_PRETTY_PRINT);
        $this->cachedInfo->set($fileContent);

        return $json;
    }

    /**
     * @return bool
     */
    public function able()
    {
        if (empty($this->allRelease)) {
            return false;
        }
        $newestInfo = $this->getNewestInfo();

        if (file_exists($this->options['cache'] . $this->options['versionFile'])) {
            $fileContent = file_get_contents($this->options['cache'] . $this->options['versionFile']);
            $current = json_decode($fileContent, true);

            if (isset($current['id']) && $current['id'] == $newestInfo['id']) {
                return false;
            }
            if (isset($current['tag_name']) && $current['tag_name'] == $newestInfo['tag_name']) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $newestRelease = $this->getNewestInfo();
        if ($this->able()) {
            if ($this->download($newestRelease['zipball_url'])) {
                if ($this->unZip()) {
                    unlink($this->options['cache'] . $this->options['zipFile']);
                    file_put_contents(
                        $this->options['cache'] . $this->options['versionFile'],
                        json_encode(array(
                            "id" => $newestRelease['id'],
                            "tag_name" => $newestRelease['tag_name']
                        ), static::JSON_PRETTY_PRINT)
                    );
                    return true;
                }
            }
        }
        return false;
    }

    protected function download($url)
    {
        $file = @fopen($url, 'r', false, $this->streamContext2);
        if ($file == false) {
            if ($this->options["exceptions"]) {
                throw new \Exception("Download failed Exception");
            }
            return false;
        }
        file_put_contents($this->options['cache'] . $this->options['zipFile'], $file);
        fclose($file);
        return true;
    }

    protected function shouldBeCopied($file)
    {
        static $updateIgnore = array();
        if (empty($updateIgnore) && file_exists($this->options['updateignore'])) {
            $updateIgnore = file($this->options['updateignore']);
            foreach ($updateIgnore as &$ignore) {
                $ignore = $this->options['save'] . trim($ignore);
            }
        }
        foreach ($updateIgnore as $ignore) {
            if (substr($file, 0, strlen($ignore)) == $ignore) {
                return false;
            }
        }
        return true;
    }

    protected function unZip()
    {
        $path = getcwd() . "/" . $this->options['cache'] . $this->options['zipFile'];

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return false;
        }
        $cutLength = strlen($zip->getNameIndex(0));
        for ($i = 1; $i < $zip->numFiles; $i++) {
            $name = $this->options['save'] . substr($zip->getNameIndex($i), $cutLength);

            if ($this->shouldBeCopied($name)) {
                $stat = $zip->statIndex($i);
                if ($stat["crc"] == 0) { //is dir
                    if (!file_exists($name)) {
                        mkdir($name);
                    }
                    continue;
                }
                copy("zip://" . $path . "#" . $zip->getNameIndex($i), $name);
            }
        }
        return $zip->close();
    }

    /**
     * @return mixed|null
     */
    public function getCurrentInfo()
    {
        if (is_null($this->currentInfo) && file_exists($this->options['cache'] . $this->options['versionFile'])) {
            $fileContent = file_get_contents($this->options['cache'] . $this->options['versionFile']);
            $current = json_decode($fileContent, true);

            foreach ($this->allRelease as $release) {
                if (isset($current['id']) && $current['id'] == $release['id']) {
                    return $this->currentInfo = $release;
                }
                if (isset($current['tag_name']) && $current['tag_name'] == $release['tag_name']) {
                    return $this->currentInfo = $release;
                }
            }
        }
        return $this->currentInfo;
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function getNewestInfo()
    {
        if (is_null($this->newestInfo)) {
            foreach ($this->allRelease as $release) {
                if (isset($release['prerelease']) && $release['prerelease'] && !$this->options['prerelease']) {
                    continue;
                }
                if (isset($release['target_commitish']) && $this->options['branch'] !== $release['target_commitish']) {
                    continue;
                }
                return $this->newestInfo = $release;
            }
            if ($this->options["exceptions"]) {
                throw new \Exception("no suitable release found");
            }
            $this->newestInfo = array();
        }
        return $this->newestInfo;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getAllRelease()
    {
        return $this->allRelease;
    }

    /**
     * @internal
     * @param $option
     * @throws \Exception
     */
    protected function setOptions($option)
    {
        if (is_array($option)) {
            if (!isset($option['name']) || empty($option['name'])) {
                throw new \Exception('No Name in Option Set');
            }
            $this->options = $option + $this->options;
            return;
        } elseif (is_string($option)) {
            if (empty($option)) {
                throw new \Exception('No Name Set');
            }
            $this->options['name'] = $option;
            return;
        }
        throw new \Exception('No Option Set type ' . gettype($option) . ' not supported');
    }

    /**
     * @return null|resource
     */
    public function getStreamContext()
    {
        return $this->streamContext;
    }
}
