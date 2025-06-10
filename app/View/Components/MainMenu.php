<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\User;
use App\Helpers\SysUtils;
use App\Helpers\Permissions;
use App\Helpers\Icons;

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
                'icon' => $this->getClassesFromIcon(Icons::TACHOMETER),
                'label' => __('messages.menu.dashboard'),
            ],
            'client' => [
                'route' => route('app.client.index'),
                'routeName' => 'app.client.index',
                'icon' => $this->getClassesFromIcon(Icons::USERS),
                'label' => __('messages.menu.client'),
            ],
            'avaliation' => [
                'route' => route('app.avaliation.index'),
                'routeName' => 'app.avaliation.index',
                'icon' => $this->getClassesFromIcon(Icons::FILE_CHART),
                'label' => __('messages.menu.avaliation'),
            ],
            'calendar' => [
                'route' => route('app.calendar.index'),
                'routeName' => 'app.calendar.index',
                'icon' => $this->getClassesFromIcon(Icons::CALENDAR_ALT),
                'label' => __('messages.pages.calendar.title'),
            ],
            'report' => [
                'route' => route('app.report.index'),
                'routeName' => 'app.report.index',
                'icon' => $this->getClassesFromIcon(Icons::CHART_BAR),
                'label' => __('messages.pages.report.title'),
            ],
            'support' => [
                'route' => route('app.support.index'),
                'routeName' => 'app.support.index',
                'icon' => $this->getClassesFromIcon(Icons::HEADSET),
                'label' => __('messages.pages.support.menuTitle'),
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

    private function getClassesFromIcon(string $icon): string
    {
        // Extract classes from the icon string
        preg_match('/class="([^"]+)"/', $icon, $matches);
        return $matches[1] ?? '';
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
