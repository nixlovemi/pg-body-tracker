<?php

namespace Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;

class HasPhotoFieldTest extends TestCase
{
    public function testfGetOsPhotosFolder()
    {
        $basePhotoFolder = DIRECTORY_SEPARATOR . 'avatars';
        $expectedPath = 'app' . DIRECTORY_SEPARATOR . 'avatars';
        $actualPath = \App\Traits\HasPhotoField::fGetOsPhotosFolder($basePhotoFolder);

        $this->assertEquals($expectedPath, $actualPath);
    }

    public function testfGetDbPhotosFolder()
    {
        $basePhotoFolder = DIRECTORY_SEPARATOR . 'avatars';
        $expectedPath = 'storage' . DIRECTORY_SEPARATOR . 'avatars';
        $actualPath = \App\Traits\HasPhotoField::fGetDbPhotosFolder($basePhotoFolder);

        $this->assertEquals($expectedPath, $actualPath);
    }
}
