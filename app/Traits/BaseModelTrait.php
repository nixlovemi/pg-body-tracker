<?php

namespace App\Traits;

use App\Helpers\SysUtils;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    final public static function fHasAccess(Model $model, ?User $user = null): bool
    {
        if (SysUtils::isRunningMigration()) {
            // for migrations, seeders, etc. we allow access
            return true;
        }

        $user = $user ?? SysUtils::getLoggedInUser();
        if (!$user) {
            return false;
        }

        if ($user->role === User::ROLE_ROOT) {
            return true;
        }

        // users should not have access to create a new User model
        if ($model instanceof User && !$model->wasRecentlyCreated) {
            return false;
        }

        // new other models, skip
        if ($model->wasRecentlyCreated) {
            return true;
        }

        // if exists a method for custom access control, use it
        if (method_exists(static::class, 'fHasAccessCustom')) {
            return static::fHasAccessCustom($model, $user);
        }

        // if nothing else has granted access, deny access
        return false;
    }

    abstract public static function fHasAccessCustom(Model $model, ?User $user = null): bool;

    public static function fSave(array $form, ?string $codedId = null): ApiResponse
    {
        $modelName = self::getModelName();
        $modelNameMsg = self::getModelNameMsg();

        // get model for insert or update
        $Model = !empty($codedId)
            ? self::getModelByCodedId($codedId)
            : new self();

        if ($codedId && !$Model) {
            return new ApiResponse(true, __('messages.saveModelNotFound', [
                'modelName' => $modelNameMsg,
            ]));
        }
        $isEdit = ($Model->exists);

        // check if user can save
        if (!self::fHasAccess($Model)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => $modelNameMsg,
            ]));
        }

        // fill model
        $Model->fill($form);

        // BEFORE VALIDATE HOOK
        if (method_exists(static::class, 'fSaveBeforeValidate')) {
            $beforeValidateResponse = static::fSaveBeforeValidate($Model, $form);
            if ($beforeValidateResponse instanceof ApiResponse) {
                return $beforeValidateResponse;
            }
        }

        // validate model
        $validation = $Model->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
            $Model->save();
            $Model->refresh();
        } catch (\Exception $e) {
            \App\Helpers\LocalLogger::log("{$modelName} save error", ['exception' => $e->getMessage()]);
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => $modelNameMsg,
            ]));
        }

        // all good, return success
        $msg = $isEdit ? __('messages.saveModelSuccessEditing', ['modelName' => $modelNameMsg]) : __('messages.saveModelSuccessAdding', ['modelName' => $modelNameMsg]);
        return new ApiResponse(false, $msg, [
            $modelName => $Model,
            'isEdit' => $isEdit,
        ]);
    }

    /**
     * Use this if you need to do something before the model is validated (model is already filled with data).
     */
    abstract public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse;

    public static function fRemove(string $codedId): ApiResponse
    {
        $Model = self::getModelByCodedId($codedId);
        $modelName = self::getModelName();
        $modelNameMsg = self::getModelNameMsg();

        if ($Model === null) {
            return new ApiResponse(true, __('messages.saveModelNotFound', [
                'modelName' => $modelNameMsg,
            ]));
        }

        // check if user can save
        if (!self::fHasAccess($Model)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => $modelNameMsg,
            ]));
        }

        // remove model
        try {
            $Model->delete();
        } catch (\Exception $e) {
            \App\Helpers\LocalLogger::log("{$modelName} delete error", ['exception' => $e->getMessage()]);
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => $modelNameMsg,
            ]));
        }

        // all good, return success
        return new ApiResponse(false, __('messages.saveModelSuccessRemoving', ['modelName' => $modelNameMsg]));
    }

    private static function getModelName(): string
    {
        return class_basename(static::class);
    }

    private static function getModelNameMsg(): string
    {
        $modelName = self::getModelName();
        return __("messages.models.{$modelName}.name");
    }

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
            if (!self::fHasAccess($model)) {
                throw new \LogicException(__('messages.saveModelErrorSavingOther', [
                    'modelName' => self::getModelNameMsg(),
                ]));
            }

            try {
                $model->created_at = SysUtils::timezoneDate(date('c'), 'c');
                $model->updated_at = null;
            } catch (\Throwable $th) { }
        });

        static::updating(function ($model) {
            if (!self::fHasAccess($model)) {
                throw new \LogicException(__('messages.saveModelErrorSavingOther', [
                    'modelName' => self::getModelNameMsg(),
                ]));
            }

            try {
                $model->updated_at = SysUtils::timezoneDate(date('c'), 'c');
            } catch (\Throwable $th) { }
        });

        static::deleting(function ($model) {
            if (!self::fHasAccess($model)) {
                throw new \LogicException(__('messages.saveModelErrorSavingOther', [
                    'modelName' => self::getModelNameMsg(),
                ]));
            }
        });
    }
}
