<?php

namespace Tests\Feature\Helpers;

use Tests\TestCase;
use App\Helpers\Permissions;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class PermissionsTest extends TestCase
{
    public function testManagerCanAccessDashboardIndexAcl()
    {
        $user = new User(['role' => User::ROLE_MANAGER]);
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_DASHBOARD_INDEX, $user));
    }

    public function testClientCannotAccessDashboardIndexAcl()
    {
        $user = new User(['role' => User::ROLE_CLIENT]);
        $this->assertFalse(Permissions::checkPermission(Permissions::ACL_DASHBOARD_INDEX, $user));
    }

    public function testManagerCanEditClient()
    {
        $user = new User(['role' => User::ROLE_MANAGER]);
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_CLIENT_EDIT, $user));
    }

    public function testClientCannotEditClient()
    {
        $user = new User(['role' => User::ROLE_CLIENT]);
        $this->assertFalse(Permissions::checkPermission(Permissions::ACL_CLIENT_EDIT, $user));
    }

    public function testManagerCanViewGoal()
    {
        $user = new User(['role' => User::ROLE_MANAGER]);
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_GOAL_VIEW, $user));
    }

    public function testClientCanViewGoal()
    {
        $user = new User(['role' => User::ROLE_CLIENT]);
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_GOAL_VIEW, $user));
    }

    public function testClientCannotEditGoal()
    {
        $user = new User(['role' => User::ROLE_CLIENT]);
        $this->assertFalse(Permissions::checkPermission(Permissions::ACL_GOAL_EDIT, $user));
    }

    public function testRootUserAlwaysHasPermission()
    {
        $user = new User(['role' => User::ROLE_ROOT]);
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_CLIENT_EDIT, $user));
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_GOAL_VIEW, $user));
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_DASHBOARD_INDEX, $user));
    }

    public function testRouteNameIsResolvedToAcl()
    {
        $user = new User(['role' => User::ROLE_MANAGER]);
        $this->assertTrue(Permissions::checkPermission('app.client.index', $user)); // resolves to ACL_CLIENT_VIEW
        $this->assertTrue(Permissions::checkPermission('app.goal.htmlModalPastGoals', $user)); // resolves to ACL_GOAL_VIEW
    }

    public function testRouteNameDeniedForClient()
    {
        $user = new User(['role' => User::ROLE_CLIENT]);
        $this->assertFalse(Permissions::checkPermission('app.client.edit', $user)); // resolves to ACL_CLIENT_EDIT
    }

    public function testLoggedInUserHasAccessToDashboardIndex()
    {
        $user = User::where('role', User::ROLE_MANAGER)->first();
        $this->be($user, 'web');
        $this->assertTrue(Permissions::checkPermission(Permissions::ACL_DASHBOARD_INDEX));
    }

    public function testGetAllRouteAcls()
    {
        $routeAcls = Permissions::getAllRouteAcls();
        $this->assertIsArray($routeAcls);
        $this->assertNotEmpty($routeAcls);
        $this->assertArrayHasKey('app.dashboard.index', $routeAcls);
        $this->assertArrayHasKey('app.client.index', $routeAcls);
        $this->assertArrayHasKey('app.goal.htmlModalAdd', $routeAcls);
    }

    public function testIfAllRoutesUnderAppHaveDefinedPermissions()
    {
        $definedRoutes = array_keys(Permissions::getAllRouteAcls());
        $allAppRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return in_array('web', $route->middleware()) &&
                in_array('authWeb', $route->middleware());
        })
        ->map(function ($route) {
            return $route->getName();
        })->values()->all();

        foreach ($allAppRoutes as $route) {
            $this->assertContains($route, $definedRoutes, "Route '$route' does not have defined permissions.");
        }
    }

    public function testIfAllAclConstantsAreInUse()
    {
        $reflection = new \ReflectionClass(\App\Helpers\Permissions::class);
        $aclConstants = [];
        foreach ($reflection->getConstants() as $name => $value) {
            if (strpos($name, 'ACL_') === 0) {
                $aclConstants[] = $value;
            }
        }

        $usedAcls = array_values(Permissions::getAllRouteAcls());
        $uniqueAcls = array_unique($usedAcls);
        $this->assertTrue(
            (array_diff($uniqueAcls, $aclConstants) === [] && array_diff($aclConstants, $uniqueAcls) === [])
        );
    }
}
