<?php

namespace App\Traits;

use App\Helpers\SysUtils;
use App\Helpers\ApiResponse;

trait BaseModelTrait {
    final static function getModelByCodedId(string $codedId): ?\Illuminate\Database\Eloquent\Model
    {
        $id = SysUtils::decodeStr($codedId);
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $Class = get_called_class();
            return (new $Class)::find($id);
        } catch (\Throwable $th) {
            return null;
        }
    }

    final public function getCodedIdAttribute(int $id=null): ?string
    {
        $idValue = $id ?? $this->id;
        if (is_null($idValue)) {
            return null;
        }
        return SysUtils::encodeStr($idValue);
    }

    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    abstract function validateModel(): ApiResponse;

    /**
     * The "booted" method of the model.
     * 'retrieved', 'creating', 'created', 'updating', 'updated', 'saving', 'saved', 'restoring', 'restored', 'replicating', 'deleting', 'deleted', 'forceDeleted', 'trashed'
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->created_at = SysUtils::timezoneDate(date('c'), 'c');
                $model->updated_at = null;
            } catch (\Throwable $th) { }
        });

        static::updating(function ($model) {
            try {
                $model->updated_at = SysUtils::timezoneDate(date('c'), 'c');
            } catch (\Throwable $th) { }
        });
    }
}