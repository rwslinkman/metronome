<?php
namespace Metronome\File;

/**
 * Class FileReader
 * Wraps a class around the file() php function
 * @codeCoverageIgnore
 * @package JappserBundle\Service\Helper
 */
class FileReader
{
    public function readFile($filePath) {
        return file($filePath);
    }
}