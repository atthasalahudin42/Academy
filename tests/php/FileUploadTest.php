<?php
use PHPUnit\Framework\TestCase;

// No need to require UploadedFileInterface; using a simple fake class

class FakeUpload
{
    public function getName()
    {
        return "test.jpg";
    }

    public function getSize()
    {
        return 1024;
    }

    public function getType()
    {
        return "image/jpeg";
    }
}

class FileUploadTest extends TestCase
{
    public function testFileUploadInterface()
    {
        $file = new FakeUpload();

        $this->assertEquals("test.jpg", $file->getName());
        $this->assertEquals(1024, $file->getSize());
        $this->assertEquals("image/jpeg", $file->getType());
    }
}