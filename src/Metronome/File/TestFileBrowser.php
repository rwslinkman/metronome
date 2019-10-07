<?php
namespace Metronome\File;

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
        $file[parent::FILE_PROP_RELATIVE_PATH_NAME] = $relativePathName;
        $file[parent::FILE_PROP_PATH_NAME]          = $pathName;
        $file[parent::FILE_PROP_NAME]               = $fileName;
        return $file;
    }
}