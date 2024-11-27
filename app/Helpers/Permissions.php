<?php

namespace App\Helpers;

use App\Models\User;
use App\Helpers\SysUtils;

final class Permissions {
    public const ACL_DASHBOARD_VIEW = 'dashboard/view';

    public const ACL_CLIENT_VIEW = 'client/view';
    public const ACL_CLIENT_EDIT = 'client/edit';

    public const ACL_USER_PROFILE = 'user/profile';
    public const ACL_USER_CHANGE_PWD = 'user/changePwd';
    public const ACL_USER_VIEW = 'user/view';
    public const ACL_USER_EDIT = 'user/edit';

    public const ACL_JOB_VIEW = 'job/view';
    public const ACL_JOB_EDIT = 'job/edit';

    public const ACL_QUOTE_MENU = 'quote/menu';
    public const ACL_QUOTE_VIEW = 'quote/view';
    public const ACL_QUOTE_EDIT = 'quote/edit';

    public const ACL_SERVICE_ITEM_VIEW = 'serviceItem/view';
    public const ACL_SERVICE_ITEM_EDIT = 'serviceItem/edit';

    private const ACL = [
        self::ACL_DASHBOARD_VIEW => [User::ROLE_MANAGER, User::ROLE_CREATIVE, User::ROLE_EDITOR, User::ROLE_CUSTOMER],

        self::ACL_CLIENT_VIEW => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],
        self::ACL_CLIENT_EDIT => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],

        self::ACL_USER_PROFILE => [User::ROLE_MANAGER, User::ROLE_CREATIVE, User::ROLE_EDITOR, User::ROLE_CUSTOMER],
        self::ACL_USER_CHANGE_PWD => [User::ROLE_MANAGER, User::ROLE_CREATIVE, User::ROLE_EDITOR, User::ROLE_CUSTOMER],
        self::ACL_USER_VIEW => [User::ROLE_MANAGER], //@ TODO: only admin can change other people's role
        self::ACL_USER_EDIT => [],

        self::ACL_JOB_VIEW => [User::ROLE_MANAGER, User::ROLE_CREATIVE, User::ROLE_EDITOR, User::ROLE_CUSTOMER],
        self::ACL_JOB_EDIT => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],

        self::ACL_QUOTE_MENU => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],
        self::ACL_QUOTE_VIEW => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],
        self::ACL_QUOTE_EDIT => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],

        self::ACL_SERVICE_ITEM_VIEW => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],
        self::ACL_SERVICE_ITEM_EDIT => [User::ROLE_MANAGER, User::ROLE_CUSTOMER],
    ];

    private const ROUTE_ACL = [
        'site.dashboard' => self::ACL_DASHBOARD_VIEW,
        'site.showJobs' => self::ACL_DASHBOARD_VIEW,

        'client.index' => self::ACL_CLIENT_VIEW,
        'client.view' => self::ACL_CLIENT_VIEW,
        'client.add' => self::ACL_CLIENT_EDIT,
        'client.add.save' => self::ACL_CLIENT_EDIT,
        'client.edit' => self::ACL_CLIENT_EDIT,
        'client.edit.save' => self::ACL_CLIENT_EDIT,

        'user.index' => self::ACL_USER_VIEW,
        'user.view' => self::ACL_USER_VIEW,
        'user.add' => self::ACL_USER_EDIT,
        'user.add.save' => self::ACL_USER_EDIT,
        'user.edit' => self::ACL_USER_EDIT,
        'user.edit.save' => self::ACL_USER_EDIT,
        'user.changePwd' => self::ACL_USER_CHANGE_PWD,
        'user.doChangePwd' => self::ACL_USER_CHANGE_PWD,
        'user.resetPwd' => self::ACL_USER_EDIT,
        'user.doResetPwd' => self::ACL_USER_EDIT,
        'user.profile' => self::ACL_USER_PROFILE,
        'user.saveProfile' => self::ACL_USER_PROFILE,

        'job.index' => self::ACL_JOB_VIEW,
        'job.view' => self::ACL_JOB_VIEW,
        'job.add' => self::ACL_JOB_EDIT,
        'job.doAdd' => self::ACL_JOB_EDIT,
        'job.edit' => self::ACL_JOB_EDIT,
        'job.doEdit' => self::ACL_JOB_EDIT,
        'job.briefingPdf' => self::ACL_JOB_VIEW,

        'jobFile.add' => self::ACL_JOB_VIEW,
        'jobFile.doAdd' => self::ACL_JOB_VIEW,

        'QUOTE_MENU' => self::ACL_QUOTE_MENU, # not a route

        'serviceItems.index' => self::ACL_SERVICE_ITEM_VIEW,
        'serviceItems.view' => self::ACL_SERVICE_ITEM_VIEW,
        'serviceItems.add' => self::ACL_SERVICE_ITEM_EDIT,
        'serviceItems.add.save' => self::ACL_SERVICE_ITEM_EDIT,
        'serviceItems.edit' => self::ACL_SERVICE_ITEM_EDIT,
        'serviceItems.edit.save' => self::ACL_SERVICE_ITEM_EDIT,

        'quote.index' => self::ACL_QUOTE_VIEW,
        'quote.add' => self::ACL_QUOTE_EDIT,
        'quote.doAdd' => self::ACL_QUOTE_EDIT,
        'quote.getLinkToJobHtml' => self::ACL_QUOTE_EDIT,
        'quote.saveLinkToJobHtml' => self::ACL_QUOTE_EDIT,
        'quote.quoteItemsHtml' => self::ACL_QUOTE_EDIT,
        'quote.addFromJob' => self::ACL_QUOTE_EDIT,
        'quote.removeFromJob' => self::ACL_QUOTE_EDIT,
        'quote.pdf' => self::ACL_QUOTE_VIEW,

        'quoteItem.add' => self::ACL_QUOTE_EDIT,
        'quoteItem.doAdd' => self::ACL_QUOTE_EDIT,
    ];

    public static function checkPermission(string $aclOrRoute, ?User $User = null): bool
    {
        $User = $User ?? SysUtils::getLoggedInUser();
        if ($User?->isAdmin()) {
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
