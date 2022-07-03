<?php
namespace Metronome\File;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MetronomeTestFile
 * @package Metronome\Util
 */
class MetronomeTestFile extends UploadedFile
{
    private $pathName;
    private $mime;
    private $extension;
    private $baseName;
    private $modifiedDate;
    private $fileSize;
    private $movable;

    /**
     * MetronomeTestFile constructor.
     * @param string $originalName
     * @param string $mimeType
     * @param int $size
     * @param string $extension
     * @param string $baseName
     * @param int $modifiedDate
     * @param bool $movable
     */
    public function __construct($originalName, $mimeType = null, $size = 0, $extension = ".ext", $baseName = "testFile", $modifiedDate = 1512076615, $movable = true)
    {
        parent::__construct("/path/to/" . $originalName, $originalName, $mimeType, UPLOAD_ERR_NO_FILE, true);
        $this->pathName = "/path/to/" . $originalName;
        $this->mime = $mimeType;
        $this->extension = $extension;
        $this->baseName = $baseName;
        $this->modifiedDate = $modifiedDate;
        $this->fileSize = $size;
        $this->movable = $movable;
    }

    public function getPathname(): string
    {
        return $this->pathName;
    }

    public function getMimeType(): null|string
    {
        return $this->mime;
    }

    /**
     * @throws Exception
     */
    public function move($directory, $name = null): self
    {
        if ($this->movable) {
            return $this;
        }
        throw new Exception("Not movable");
    }

    public function toBrowsable(): array
    {
        $file = array();
        $file['relativePathName'] = $this->getClientOriginalName();
        $file['pathName'] = $this->pathName;
        $file['name'] = $this->getFilename();
        $file['extension'] = $this->extension;
        $file['baseName'] = $this->baseName;
        $file['modifiedDate'] = $this->modifiedDate;
        $file['size'] = $this->fileSize;
        return $file;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @param string $baseName
     */
    public function setBaseName($baseName)
    {
        $this->baseName = $baseName;
    }

    /**
     * @return int
     */
    public function getModifiedDate(): int
    {
        return $this->modifiedDate;
    }

    /**
     * @param int $modifiedDate
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }
}