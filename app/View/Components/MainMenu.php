<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\User;
use App\Helpers\SysUtils;
use App\Helpers\Permissions;

class MainMenu extends Component
{
    private ?User $User;
    public array $menuItems = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->User = SysUtils::getLoggedInUser();
        $this->menuItems = $this->getMenuItems();
    }

    private function getMenuItems(): array
    {
        $menuItems = [
            'dashboard' => [
                'route' => route('app.dashboard.index'),
                'routeName' => 'app.dashboard.index',
                'icon' => 'fas fa-fw fa-tachometer-alt',
                'label' => __('messages.menu.dashboard'),
            ],
            'client' => [
                'route' => route('app.client.index'),
                'routeName' => 'app.client.index',
                'icon' => 'fas fa-fw fa-users',
                'label' => __('messages.menu.client'),
            ],
            'avaliation' => [
                'route' => route('app.avaliation.index'),
                'routeName' => 'app.avaliation.index',
                'icon' => 'fas fa-fw fa-chart-line',
                'label' => __('messages.menu.avaliation'),
            ],
        ];

        $this->checkPermissions($menuItems);
        return $menuItems;
    }

    private function checkPermissions(&$menuItems): void
    {
        // filter menu items based on permissions
        foreach ($menuItems as $key => $menuItem) {
            $routeName = $menuItem['routeName'];
            $canAccess = Permissions::checkPermission($routeName, $this->User);
            if (false === $canAccess) {
                unset($menuItems[$key]);
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main-menu');
    }
}
