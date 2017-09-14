<?php
namespace Metronome\Util;

use Metronome\File\FileBrowser;

/**
 * Class TestFileBrowser
 * @package Metronome\Util
 */
class TestFileBrowser extends FileBrowser
{
    private $filesInDir = array();
    private $dirs = array();

    public function getFilesInDirectory($dirName)
    {
        return $this->filesInDir;
    }

    public function getDirectories($parent)
    {
        return $this->dirs;
    }


    public function setFilesInDir($files) {
        $this->filesInDir = $files;
    }

    public function setDirs($dirs) {
        $this->dirs = $dirs;
    }

    public static function browsableFile($fileName, $relativePathName = "somePathName", $pathName = "someName") {
        $file = array();
        $file['relativePathName'] = $relativePathName;
        $file['pathName'] = $pathName;
        $file['fileName'] = $fileName;
        return $file;
    }
}