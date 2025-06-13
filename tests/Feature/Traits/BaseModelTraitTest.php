<?php

namespace Tests\Feature\Traits;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Goal;
use App\Models\Client;
use App\Helpers\ApiResponse;

class BaseModelTraitTest extends TestCase
{
    use DatabaseTransactions;

    private function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'role' => User::ROLE_MANAGER,
        ]), $attributes);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetModelByCodedId()
    {
        // create
        $email = 'testuser@example.com';
        $user = $this->createUser(['email' => $email]);

        // check db
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        // get using getModelByCodedId
        $newUser = User::getModelByCodedId($user->coded_id);
        $this->assertNotNull($newUser);
        $this->assertEquals(User::class, get_class($newUser));
        $this->assertEquals($user->id, $newUser->id);
        $this->assertNotNull($newUser->coded_id);

        // check defaults
        $this->assertEquals('/images/no-user.jpg', $newUser->picture_url);
        $this->assertNull($newUser->password_reset_token);
        $this->assertEquals(1, $newUser->active);
        $this->assertEquals(0, $newUser->confirmation);
        $this->assertNull($newUser->google_login);
    }

    public function testGetModelByCodedIdReturnsNullForInvalidCodedId()
    {
        $this->assertNull(User::getModelByCodedId('invalid-coded-id'));
    }

    public function testFHasAccessReturnsFalseIfNoUser()
    {
        $user = $this->createUser();
        $this->assertFalse(User::fHasAccess($user));
    }

    public function testFHasAccessReturnsTrueForRootUser()
    {
        $user = $this->createUser();
        $loggedInUser = User::where('role', User::ROLE_ROOT)->first();
        $this->assertTrue(User::fHasAccess($user, $loggedInUser));
    }

    /**
     * When a new User model is passed to fHasAccess, it should return false.
     *
     * @return void
     */
    public function testFHasAccessReturnsFalseWhenNewUserModel()
    {
        $user = new User();
        $loggedInUser = User::where('role', User::ROLE_MANAGER)->first();
        $this->assertFalse(User::fHasAccess($user, $loggedInUser));
    }

    public function testFHasAccessReturnsTrueWhenNewOtherModel()
    {
        $Goal = new Goal();
        $loggedInUser = User::where('role', User::ROLE_MANAGER)->first();
        $this->assertTrue(User::fHasAccess($Goal, $loggedInUser));
    }

    public function testCreatingModelWithoutAccessThrowsException()
    {
        // Simulate no logged in user (fHasAccess will return false)
        $goal = new Goal();
        $this->expectException(\Exception::class);
        $goal->save();
    }

    public function testUpdatingModelWithoutAccessThrowsException()
    {
        // Create a client with access
        $user = User::where('role', User::ROLE_MANAGER)->first();
        $this->be($user);

        // new client
        $ret = Client::fSave([
            'user_id' => $user->id,
            'first_name' => 'Test',
            'last_name' => 'Client',
            'gender' => Client::GENDER_MALE,
            'birthdate' => '2000-01-01',
            'weight_kg' => 70,
            'height_cm' => 175,
        ]);
        $this->assertInstanceOf(ApiResponse::class, $ret);
        $this->assertFalse($ret->isError());

        // response
        $arrRet = $ret->getArrayResponse();
        $this->assertIsArray($arrRet);
        $this->assertArrayHasKey(ApiResponse::KEY_ERROR, $arrRet);
        $this->assertArrayHasKey(ApiResponse::KEY_MESSAGE, $arrRet);
        $this->assertArrayHasKey(ApiResponse::KEY_DATA, $arrRet);
        $Client = $ret->getValueFromResponse('Client');
        $this->assertInstanceOf(Client::class, $Client);
        $modelName = class_basename($Client);

        // Simulate no logged in user for update
        auth()->logout();
        $ret = Client::fSave(['first_name' => 'Test 123'], $Client->coded_id);
        $this->assertTrue($ret->isError());
        $this->assertEquals(__('messages.saveModelErrorSavingOther', [
            'modelName' => __("messages.models.{$modelName}.name"),
        ]), $ret->getMessage());
    }

    public function testFSaveReturnsErrorIfModelNotFound()
    {
        $loggedInUser = User::where('role', User::ROLE_ROOT)->first();
        $this->be($loggedInUser);

        $resp = User::fSave([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'role' => User::ROLE_MANAGER,
        ], 'badid');
        $this->assertTrue($resp->isError());
        $this->assertEquals(__('messages.saveModelNotFound', [
            'modelName' => __('messages.models.User.name'),
        ]), $resp->getMessage());
    }

    public function testFRemoveReturnsErrorIfModelNotFound()
    {
        $loggedInUser = User::where('role', User::ROLE_ROOT)->first();
        $this->be($loggedInUser);

        $User = $this->createUser();
        $resp = $User::fRemove('badid');
        $this->assertTrue($resp->isError());
    }

    public function testFRemoveReturnsSuccess()
    {
        $loggedInUser = User::where('role', User::ROLE_ROOT)->first();
        $this->be($loggedInUser);

        $User = $this->createUser();
        $resp = $User::fRemove($User->codedId);
        $this->assertFalse($resp->isError());
        $this->assertEquals(__('messages.saveModelSuccessRemoving', ['modelName' => __('messages.models.User.name')]), $resp->getMessage());
    }

    public function testFRemoveReturnsErrorIfNoAccess()
    {
        $loggedInUser = User::where('role', User::ROLE_MANAGER)->first();
        $this->be($loggedInUser);

        $Client = Client::where('user_id', '<>', $loggedInUser->id)->first();
        $this->assertNotNull($Client);
        $this->assertEquals(Client::class, get_class($Client));

        $resp = Client::fRemove($Client->codedId);
        $this->assertTrue($resp->isError());
        $this->assertEquals(__('messages.saveModelErrorSavingOther', [
            'modelName' => __('messages.models.Client.name'),
        ]), $resp->getMessage());
    }
}
