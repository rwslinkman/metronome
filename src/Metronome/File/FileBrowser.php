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
        $file['relativePathName'] = $fileInfo->getRelativePathname();
        $file['pathName'] = $fileInfo->getPathname();
        $file['name'] = $fileInfo->getFilename();
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