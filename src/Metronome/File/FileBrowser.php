<?php
namespace Metronome\File;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileBrowser
 * @codeCoverageIgnore
 * @package Metronome\File
 */
class FileBrowser
{
    const FILE_PROP_RELATIVE_PATH_NAME = 'relativePathName';
    const FILE_PROP_PATH_NAME = 'pathName';
    const FILE_PROP_NAME = 'name';

    /**
     * @param string $dirName
     * @return array
     */
    public function getFilesInDirectory($dirName) {
        $finder = new Finder();
        $cursor = $finder->files()->in($dirName);
        return $this->cursorToArray($cursor);
    }

    public function getDirectories($parent) {
        $finder = new Finder();
        $cursor = $finder->directories()->in($parent);
        return $this->cursorToArray($cursor);
    }

    /**
     * @param SplFileInfo $fileInfo
     * @return array
     */
    private function splToArray(SplFileInfo $fileInfo) {
        $file = array();
        $file[self::FILE_PROP_RELATIVE_PATH_NAME]   = $fileInfo->getRelativePathname();
        $file[self::FILE_PROP_PATH_NAME]            = $fileInfo->getPathname();
        $file[self::FILE_PROP_NAME]                 = $fileInfo->getFilename();
        return $file;
    }

    /**
     * @param Finder $cursor
     * @return array
     */
    private function cursorToArray($cursor)
    {
        $result = array();
        /** @var SplFileInfo $file */
        foreach ($cursor->getIterator() as $file) {
            array_push($result, $this->splToArray($file));
        }
        return $result;
    }
}