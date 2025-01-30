<?php

namespace App\Helpers;

use App\Models\User;
use App\Helpers\SysUtils;

final class Permissions {
    public const ACL_DASHBOARD_INDEX = 'dashboard/index';

    public const ACL_CLIENT_INDEX = 'client/index';
    public const ACL_CLIENT_EDIT = 'client/edit';

    public const ACL_GOAL_EDIT = 'goal/edit';

    private const ACL = [
        self::ACL_DASHBOARD_INDEX => [User::ROLE_MANAGER],

        self::ACL_CLIENT_INDEX => [User::ROLE_MANAGER],
        self::ACL_CLIENT_EDIT => [User::ROLE_MANAGER],

        self::ACL_GOAL_EDIT => [User::ROLE_MANAGER],
    ];

    private const ROUTE_ACL = [
        'app.dashboard.index' => self::ACL_DASHBOARD_INDEX,

        'app.client.index' => self::ACL_CLIENT_INDEX,
        'app.client.add' => self::ACL_CLIENT_EDIT,
        'app.client.doSave' => self::ACL_CLIENT_EDIT,
        'app.client.edit' => self::ACL_CLIENT_EDIT,

        'app.goal.htmlModalAdd' => self::ACL_GOAL_EDIT,
        'app.goal.doModalAdd' => self::ACL_GOAL_EDIT,
    ];

    public static function checkPermission(string $aclOrRoute, ?User $User = null): bool
    {
        $User = $User ?? SysUtils::getLoggedInUser();
        if ($User?->isRoot()) {
            return true;
        }

        // if it's a route string, try to get ACL
        if (array_key_exists($aclOrRoute, self::ROUTE_ACL)) {
            $aclOrRoute = self::ROUTE_ACL[$aclOrRoute];
        }

        // check for true = all blocked except when true
        $hasAcl = (array_search($User?->role, self::ACL[$aclOrRoute] ?? []) !== false);
        return (true === $hasAcl);
    }
}
