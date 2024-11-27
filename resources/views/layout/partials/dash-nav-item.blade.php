@php
$arrMenu = [
    'dashboard' => [
        'route' => route('site.dashboard'),
        'icon' => 'fas fa-fw fa-tachometer-alt',
        'label' => 'Dashboard',
    ],
    'people' => [
        'route' => route('people.index'),
        'icon' => 'fas fa-users',
        'label' => 'Pessoas',
    ],
    'attendance' => [
        'route' => route('attendance.index'),
        'icon' => 'fas fa-tasks',
        'label' => 'Presença',
    ],
    'attendanceReport' => [
        'route' => route('attendance.report'),
        'icon' => 'fas fa-print',
        'label' => 'Relatório',
    ],
];
@endphp

@foreach ($arrMenu as $menu)
    @php
    $isActive = strpos(Request::url(), $menu['route']) !== false;
    @endphp

    <li class="nav-item {{ $isActive ? 'active' : '' }}">
        <a class="nav-link" href="{{ $menu['route'] }}">
            <i class="{{ $menu['icon'] }}"></i>
            <span>{{ $menu['label'] }}</span>
        </a>
    </li>
    <hr class="sidebar-divider mb-0" />
@endforeach

<span class="mb-4"></span>
