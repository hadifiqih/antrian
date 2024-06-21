<li class="nav-item menu-open">
    <a href="{{ url('/dashboard') }}" class="nav-link active">
        <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
                Antrian
                <i class="right fas fa-angle-left"></i>
            </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('design.indexDesain') }}" class="nav-link {{ request()->routeIs('design.indexDesain') || request()->routeIs('order.edit') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>List Desain</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('antrian.index') }}" class="nav-link {{ request()->routeIs('antrian.index') || request()->routeIs('antrian.edit') || request()->routeIs('antrian.show') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Antrian Workshop</p>
            </a>
        </li>
    </ul>
</li>