<?php
namespace JappserBundle\Tests\TestEnvironment\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestFile extends UploadedFile
{
    private $pathName;
    private $mime;

    public function __construct($originalName, $mimeType = null, $size = null, $error = null) {
        parent::__construct("/path/to/".$originalName, $originalName, $mimeType, $size, $error ?: "test-file", true);
        $this->pathName = "/path/to/".$originalName;
        $this->mime = $mimeType;
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
        $file['relativePathName'] = $this->getClientOriginalName();
        $file['pathName'] = $this->pathName;
        $file['name'] = $this->getFilename();
        return $file;
    }
}