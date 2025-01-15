@foreach ($menuItems as $menu)
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
