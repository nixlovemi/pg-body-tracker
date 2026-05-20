<?php

namespace Tests\Feature\Traits;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class HasPhotoFieldTest extends TestCase
{
    use DatabaseTransactions;

    public function testSetAndRemovePictureUrl()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');
        $fileName = 'user_' . $user->id . '_' . time();

        $user->setPhotoUrl('picture_url', $file, User::BASE_PHOTOS_FOLDER, 400, $fileName);
        $this->assertNotNull($user->picture_url);
        $this->assertStringContainsString('storage/users/photos/' . $fileName, $user->picture_url);

        $imageBase64 = $user->getPhotoBase64('picture_url');
        $this->assertNotNull($imageBase64);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $imageBase64);

        $user->removePhotoUrl('picture_url', User::BASE_PHOTOS_FOLDER);
        $this->assertNull($user->picture_url);
        $imageBase64 = $user->getPictureBase64();
        $this->assertNotNull($imageBase64); // default image should be returned
        $this->assertStringStartsWith('data:image/jpeg;base64,', $imageBase64);
    }
}
