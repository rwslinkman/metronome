<?php
namespace Metronome;

use Metronome\Injection\MockCreator;
use Metronome\Util\MetronomeTestFile;
use Metronome\Util\TestFileBrowser;
use Symfony\Component\Filesystem\Filesystem;

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

    /**
     * @return TestFileBrowser
     */
    public function buildFileBrowser() {
        //
        $files = array();
        /** @var MetronomeTestFile $f */
        foreach($this->filesInDir as $f) {
            array_push($files, $f->toBrowsable());
        }

        $dirs = array();
        foreach($this->dirNames as $d) {
            $f = new MetronomeTestFile($d);
            array_push($dirs, $f->toBrowsable());
        }

        $mockFB = new TestFileBrowser();
        $mockFB->setFilesInDir($files);
        $mockFB->setDirs($dirs);
        return $mockFB;
    }

    /**
     * @return \Mockery\MockInterface|Filesystem
     */
    public function buildFileSystem() {
        $mockFS = MockCreator::mock('\Symfony\Component\Filesystem\Filesystem', array(
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

    public function addBrowsableFile(MetronomeTestFile $testFile)
    {
        array_push($this->filesInDir, $testFile);
    }

    public function addBrowsableDirectory($dirName) {
        array_push($this->dirNames, $dirName);
    }
}