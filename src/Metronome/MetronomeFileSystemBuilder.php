<?php
namespace JappserBundle\Tests\TestEnvironment;

use JappserBundle\Tests\TestEnvironment\Util\TestFile;
use JappserBundle\Tests\TestEnvironment\Util\TestFileBrowser;

class MetronomeFileSystemBuilder
{
    private $platformDirExists;
    private $filesInDir;
    private $dirNames;
    private $rootDir;

    public function __construct() {
        $this->platformDirExists = true;
        $this->filesInDir = array();
        $this->dirNames = array();
        $this->rootDir = "";
    }

    public function buildFileBrowser() {
        //
        $files = array();
        /** @var TestFile $f */
        foreach($this->filesInDir as $f) {
            array_push($files, $f->toBrowsable());
        }

        $dirs = array();
        foreach($this->dirNames as $d) {
            $f = new TestFile($d);
            array_push($dirs, $f->toBrowsable());
        }

        $mockFB = new TestFileBrowser();
        $mockFB->setFilesInDir($files);
        $mockFB->setDirs($dirs);
        return $mockFB;
    }

    public function buildFileSystem() {
        //
        $mockFS = \Mockery::mock('\Symfony\Component\Filesystem\Filesystem', array(
            "exists" => $this->platformDirExists,
            "mkdir" => null,
            "remove" => null
        ));
        return $mockFS;
    }

    public function platformDirExists($result)
    {
        $this->platformDirExists = $result;
    }

    public function filesInDir($result)
    {
        $this->filesInDir = $result;
    }

    public function rootDir($result) {
        $this->rootDir = $result;
    }

    public function getRootDir() {
        return $this->rootDir;
    }

    public function addBrowsableFile(TestFile $testFile)
    {
        array_push($this->filesInDir, $testFile);
    }

    public function addBrowsableDirectory($dirName) {
        array_push($this->dirNames, $dirName);
    }
}