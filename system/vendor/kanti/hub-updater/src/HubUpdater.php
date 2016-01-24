<?php

namespace Kanti;

class HubUpdater
{
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

        "exceptions" => false,
    );

    protected $allRelease = array();
    protected $streamContext = null;

    public function __construct($option)
    {
        //options
        if (is_array($option)) {
            if (!isset($option['name']) || empty($option['name'])) {
                throw new \Exception('No Name in Option Set');
            }
            $this->options = $option + $this->options;
        } elseif (is_string($option)) {
            if (empty($option)) {
                throw new \Exception('No Name Set');
            }
            $this->options['name'] = $option;
        } else {
            throw new \Exception('No Option Set');
        }

        $this->options['save'] = rtrim($this->options['save'], '/');
        if ($this->options['save'] !== '') {
            $this->options['save'] .= '/';
            if (!HelperClass::fileExists($this->options['save'])) {
                mkdir($this->options['save']);
            }
        }
        $this->options['cache'] = $this->options['save'] . rtrim($this->options['cache'], '/');
        if ($this->options['cache'] !== '') {
            $this->options['cache'] .= '/';
            if (!HelperClass::fileExists($this->options['cache'])) {
                mkdir(dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache']);
            }
        }
        $caBundleDir = dirname(__FILE__);
        if (HelperClass::isInPhar()) {
            $caBundleDir = dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache'];
            if (!HelperClass::fileExists($this->options['cache'] . "ca_bundle.crt")) {
                copy(dirname(__FILE__) . "/ca_bundle.crt", $caBundleDir . "ca_bundle.crt");
            }
        }

        $this->cachedInfo = new CacheOneFile(
            dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache'] . $this->options['cacheFile'],
            $this->options['holdTime']
        );

        $this->streamContext = stream_context_create(
            array(
                'http' => array(
                    'header' => "User-Agent: Awesome-Update-My-Self-" . $this->options['name'] . "\r\n"
                        . "Accept: application/vnd.github.v3+json\r\n",
                ),
                'ssl' => array(
                    'cafile' => $caBundleDir . '/ca_bundle.crt',
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
                    'cafile' => $caBundleDir . '/ca_bundle.crt',
                    'verify_peer' => true,
                ),
            )
        );
        $this->allRelease = $this->getRemoteInfos();
    }

    protected function getRemoteInfos()
    {
        $path = "https://api.github.com/repos/" . $this->options['name'] . "/releases";
        if ($this->cachedInfo->is()) {
            $fileContent = $this->cachedInfo->get();
        } else {
            if (!in_array('https', stream_get_wrappers())) {
                if ($this->options["exceptions"]) {
                    throw new \Exception("No HTTPS Wrapper Exception");
                } else {
                    return array();
                }
            }
            $fileContent = @file_get_contents($path, false, $this->streamContext);

            if ($fileContent === false) {
                if ($this->options["exceptions"]) {
                    throw new \Exception("No Internet Exception");
                } else {
                    return array();
                }
            }
            $json = json_decode($fileContent, true);
            if (isset($json['message'])) {
                if ($this->options["exceptions"]) {
                    throw new \Exception("API Exception[" . $json['message'] . "]");
                } else {
                    $json = array();
                }
            }
            if (defined("JSON_PRETTY_PRINT")) {
                $fileContent = json_encode($json, JSON_PRETTY_PRINT);
            } else {
                $fileContent = json_encode($json);
            }
            $this->cachedInfo->set($fileContent);

            return $json;
        }

        return json_decode($fileContent, true);
    }

    public function able()
    {
        if (!in_array('https', stream_get_wrappers())) {
            return false;
        }
        if (empty($this->allRelease)) {
            return false;
        }

        $this->getNewestInfo();

        if (HelperClass::fileExists($this->options['cache'] . $this->options['versionFile'])) {
            $fileContent = file_get_contents(dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache'] . $this->options['versionFile']);
            $current = json_decode($fileContent, true);

            if (isset($current['id']) && $current['id'] == $this->newestInfo['id']) {
                return false;
            }
            if (isset($current['tag_name']) && $current['tag_name'] == $this->newestInfo['tag_name']) {
                return false;
            }
        }

        return true;
    }

    public function update()
    {
        $newestRelease = $this->getNewestInfo();
        if ($this->able()) {
            if ($this->download($newestRelease['zipball_url'])) {
                if ($this->unZip()) {
                    unlink(dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache'] . $this->options['zipFile']);
                    if (defined("JSON_PRETTY_PRINT")) {
                        file_put_contents(
                            dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache'] . $this->options['versionFile'],
                            json_encode(array(
                                "id" => $newestRelease['id'],
                                "tag_name" => $newestRelease['tag_name']
                            ), JSON_PRETTY_PRINT)
                        );
                    } else {
                        file_put_contents(
                            dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache'] . $this->options['versionFile'],
                            json_encode(array(
                                "id" => $newestRelease['id'],
                                "tag_name" => $newestRelease['tag_name']
                            ))
                        );
                    }

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
                throw new \Exception("Download faild Exception");
            } else {
                return false;
            }
        }
        file_put_contents(
            dirname($_SERVER['SCRIPT_FILENAME']) . "/" . $this->options['cache'] . $this->options['zipFile'],
            $file
        );
        fclose($file);

        return true;
    }

    protected function unZip()
    {
        $path = dirname($_SERVER['SCRIPT_FILENAME']) . "/" . $this->options['cache'] . $this->options['zipFile'];
        $updateIgnore = array();
        if (HelperClass::fileExists($this->options['updateignore'])) {
            $updateIgnore = file($this->options['updateignore']);
            foreach ($updateIgnore as &$ignore) {
                $ignore = $this->options['save'] . trim($ignore);
            }
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            $cutLength = strlen($zip->getNameIndex(0));
            for ($i = 1; $i < $zip->numFiles; $i++) {//iterate throw the Zip
                $name = $this->options['save'] . substr($zip->getNameIndex($i), $cutLength);

                $do = true;

                foreach ($updateIgnore as $ignore) {
                    if (substr($name, 0, strlen($ignore)) == $ignore) {
                        $do = false;
                        break;
                    }
                }

                if ($do) {
                    $stat = $zip->statIndex($i);
                    if ($stat["crc"] == 0) {
                        if (!HelperClass::fileExists($name)) {
                            mkdir(dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $name);
                        }
                    } else {
                        copy(
                            "zip://" . $path . "#" . $zip->getNameIndex($i),
                            dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $name
                        );
                    }
                }
            }
            $zip->close();

            return true;
        } else {
            return false;
        }
    }

    public function getCurrentInfo()
    {
        if (isset($this->currentInfo)) {
            return $this->currentInfo;
        }

        $this->currentInfo = null;
        if (HelperClass::fileExists($this->options['cache'] . $this->options['versionFile'])) {
            $fileContent = file_get_contents(dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $this->options['cache'] . $this->options['versionFile']);
            $current = json_decode($fileContent, true);

            foreach ($this->allRelease as $release) {
                if (isset($current['id']) && $current['id'] == $release['id']) {
                    $this->currentInfo = $release;
                    break;
                }
                if (isset($current['tag_name']) && $current['tag_name'] == $release['tag_name']) {
                    $this->currentInfo = $release;
                    break;
                }
            }
        }
        return $this->currentInfo;
    }

    public function getNewestInfo()
    {
        if (isset($this->newestInfo)) {
            return $this->newestInfo;
        }

        foreach ($this->allRelease as $release) {
            if (!$this->options['prerelease'] && $release['prerelease']) {
                continue;
            }
            if ($this->options['branch'] !== $release['target_commitish']) {
                continue;
            }
            $this->newestInfo = $release;
            break;
        }
        if (!isset($this->newestInfo)) {
            if ($this->options["exceptions"]) {
                throw new \Exception("no suitable release found");
            } else {
                return array();
            }
        }
        return $this->newestInfo;
    }
}
