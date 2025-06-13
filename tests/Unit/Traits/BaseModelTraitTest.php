<?php

namespace Tests\Unit\Traits;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use Mockery;

class DummyModel extends Model
{
    use \App\Traits\BaseModelTrait;

    public $id = 1;
    public $exists = true;

    public static $customAccess = null;
    public static $beforeValidateResponse = null;
    public static $validateModelResponse = null;

    public function fill(array $attributes)
    {
        foreach ($attributes as $k => $v) {
            $this->$k = $v;
        }
        return $this;
    }

    public function validateModel(): ApiResponse
    {
        return static::$validateModelResponse ?? new ApiResponse(false, 'ok');
    }

    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        return static::$customAccess ?? true;
    }

    public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse
    {
        return static::$beforeValidateResponse;
    }
}

class BaseModelTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DummyModel::$customAccess = null;
        DummyModel::$beforeValidateResponse = null;
        DummyModel::$validateModelResponse = null;
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetCodedIdAttributeReturnsEncodedId()
    {
        $arrCodedId = [
            1 => 'ZD',
            2 => 'Zt',
            35 => 'ZmH',
            100 => 'ZGNj',
            950 => 'BGHj',
        ];

        $model = new DummyModel();
        foreach ($arrCodedId as $id => $codedId) {
            $model->id = $id;
            $this->assertEquals($codedId, $model->getCodedIdAttribute());
        }

        // test with null
        $model->id = null;
        $this->assertNull($model->getCodedIdAttribute());

        // test parameter
        $this->assertNull($model->getCodedIdAttribute(null));
        $this->assertEquals($arrCodedId[100], $model->getCodedIdAttribute(100));
    }

    public function testGetModelByCodedIdReturnsNullIfNotNumeric()
    {
        $this->assertNull(DummyModel::getModelByCodedId('notnum'));
    }

    public function testValidateModelReturnsApiResponse()
    {
        $model = new DummyModel();
        $response = $model->validateModel();
        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertFalse($response->isError());
        $this->assertEquals('ok', $response->getMessage());
    }
}
