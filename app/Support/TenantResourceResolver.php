<?php

namespace App\Support;

use App\Models\Avaliation;
use App\Contracts\TenantVisible;
use App\Models\Client;
use App\Models\Goal;
use App\Models\CheckinConfig;
use App\Models\UserPlans;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class TenantResourceResolver
{
    private const MODELS = [
        'client' => Client::class,
        'avaliation' => Avaliation::class,
        'goal' => Goal::class,
        'checkin-config' => CheckinConfig::class,
        'user-plan' => UserPlans::class,
    ];

    /**
     * @return array<string, class-string<TenantVisible>>
     */
    public static function supportedResources(): array
    {
        return self::MODELS;
    }

    public function resolve(string $resource, string $codedId, ?User $user, string $ability = 'view'): Model
    {
        if (!$user || !isset(self::MODELS[$resource])) {
            throw (new ModelNotFoundException())->setModel(self::MODELS[$resource] ?? Model::class);
        }

        $id = CodedId::decode($codedId);
        if (!$id) {
            throw (new ModelNotFoundException())->setModel(self::MODELS[$resource]);
        }

        $modelClass = self::MODELS[$resource];
        if (!is_a($modelClass, TenantVisible::class, true)) {
            throw new \LogicException(sprintf(
                'The tenant resource "%s" must implement %s.',
                $resource,
                TenantVisible::class
            ));
        }

        $model = $modelClass::visibleTo($user)->findOrFail($id);
        if (!Gate::forUser($user)->allows($ability, $model)) {
            throw (new ModelNotFoundException())->setModel($modelClass, [$id]);
        }

        return $model;
    }

    public function resolvePhoto(string $fileName, ?User $user): Avaliation
    {
        if (!$user || $fileName !== basename($fileName)) {
            throw (new ModelNotFoundException())->setModel(Avaliation::class);
        }

        $photoUrl = Avaliation::fGetDbPhotosFolder() . $fileName;
        $avaliation = Avaliation::visibleTo($user)
            ->where(function ($query) use ($photoUrl) {
                foreach (['photo_front_url', 'photo_right_url', 'photo_left_url', 'photo_rear_url'] as $field) {
                    $query->orWhere($field, $photoUrl);
                }
            })
            ->firstOrFail();

        if (!Gate::forUser($user)->allows('view', $avaliation)) {
            throw (new ModelNotFoundException())->setModel(Avaliation::class, [$avaliation->id]);
        }

        return $avaliation;
    }
}
