<?php

namespace TNQSoft\CommonBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file, $fileName=null)
    {
        if(null === $fileName) {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
        }
        $file->move($this->targetDir, $fileName);

        return $fileName;
    }
}