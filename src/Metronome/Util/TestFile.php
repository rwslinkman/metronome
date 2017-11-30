<?php
namespace Metronome\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class TestFile
 * @package Metronome\Util
 */
class TestFile extends UploadedFile
{
    private $pathName;
    private $mime;
    private $extension;
    private $baseName;
    private $modifiedDate;

    /**
     * TestFile constructor.
     * @param string $originalName
     * @param string $mimeType
     * @param int $size
     * @param string $error
     * @param string $extension
     * @param string $baseName
     * @param int $modifiedDate
     */
    public function __construct($originalName, $mimeType = null, $size = null, $error = null,
                                $extension = ".ext", $baseName = "testFile", $modifiedDate = 1512076615) {
        parent::__construct("/path/to/".$originalName, $originalName, $mimeType, $size, $error ?: "test-file", true);
        $this->pathName = "/path/to/".$originalName;
        $this->mime = $mimeType;
        $this->extension = $extension;
        $this->baseName = $baseName;
        $this->modifiedDate = $modifiedDate;
    }

    public function getPathname()
    {
        return $this->pathName;
    }

    public function getMimeType()
    {
        return $this->mime;
    }

    public function move($directory, $name = null)
    {
        return null;
    }

    public function toBrowsable() {
        $file = array();
        $file['relativePathName']   = $this->getClientOriginalName();
        $file['pathName']           = $this->pathName;
        $file['name']               = $this->getFilename();
        $file['extension']          = $this->extension;
        $file['baseName']           = $this->baseName;
        $file['modifiedDate']       = $this->modifiedDate;
        $file['size']               = $this->getSize();
        return $file;
    }
}