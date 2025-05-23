<?php

namespace App\Helpers;

use App\Models\User;
use App\Helpers\SysUtils;

final class Permissions {
    public const ACL_DASHBOARD_INDEX = 'dashboard/index';

    public const ACL_CLIENT_VIEW = 'client/view';
    public const ACL_CLIENT_EDIT = 'client/edit';

    public const ACL_GOAL_VIEW = 'goal/view';
    public const ACL_GOAL_EDIT = 'goal/edit';

    public const ACL_AVALIATION_VIEW = 'avaliation/view';
    public const ACL_AVALIATION_EDIT = 'avaliation/edit';

    private const ACL = [
        self::ACL_DASHBOARD_INDEX => [User::ROLE_MANAGER],

        self::ACL_CLIENT_VIEW => [User::ROLE_MANAGER],
        self::ACL_CLIENT_EDIT => [User::ROLE_MANAGER],

        self::ACL_GOAL_VIEW => [User::ROLE_MANAGER, User::ROLE_CLIENT],
        self::ACL_GOAL_EDIT => [User::ROLE_MANAGER],

        self::ACL_AVALIATION_VIEW => [User::ROLE_MANAGER, User::ROLE_CLIENT],
        self::ACL_AVALIATION_EDIT => [User::ROLE_MANAGER],
    ];

    private const ROUTE_ACL = [
        'app.dashboard.index' => self::ACL_DASHBOARD_INDEX,
        'app.user.profile' => self::ACL_DASHBOARD_INDEX,
        'app.user.doProfile' => self::ACL_DASHBOARD_INDEX,
        'app.user.changePsw' => self::ACL_DASHBOARD_INDEX,
        'app.user.doChangePsw' => self::ACL_DASHBOARD_INDEX,

        'app.client.index' => self::ACL_CLIENT_VIEW,
        'app.client.add' => self::ACL_CLIENT_EDIT,
        'app.client.doSave' => self::ACL_CLIENT_EDIT,
        'app.client.edit' => self::ACL_CLIENT_EDIT,
        'app.client.view' => self::ACL_CLIENT_VIEW,

        'app.goal.htmlModalAdd' => self::ACL_GOAL_EDIT,
        'app.goal.doModalAdd' => self::ACL_GOAL_EDIT,
        'app.goal.doModalRemove' => self::ACL_GOAL_EDIT,
        'app.goal.htmlModalPastGoals' => self::ACL_GOAL_VIEW,

        'app.avaliation.htmlModalAdd' => self::ACL_AVALIATION_EDIT,
        'app.avaliation.doModalAdd' => self::ACL_AVALIATION_EDIT,
        'app.avaliation.htmlModalView' => self::ACL_AVALIATION_VIEW,
        'app.avaliation.htmlModalEdit' => self::ACL_AVALIATION_EDIT,
        'app.avaliation.index' => self::ACL_AVALIATION_VIEW,
        'app.avaliation.htmlModalSelectClient' => self::ACL_AVALIATION_EDIT,
        'app.avaliation.showPhoto' => self::ACL_AVALIATION_VIEW,
        'app.avaliation.viewReport' => self::ACL_AVALIATION_VIEW,
        'app.avaliation.viewReportPDF' => self::ACL_AVALIATION_VIEW,
        'app.avaliation.htmlModalSendWhats' => self::ACL_AVALIATION_EDIT,
        'app.avaliation.doModalSendWhats' => self::ACL_AVALIATION_EDIT,
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
