<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <!-- Add icons to the links using the .nav-icon class
           with font-awesome or any other icon font library -->
      <li class="nav-item {{ request()->routeIs('antrian.index') || request()->routeIs('customer.index') ? 'menu-open' : '' }}">
        <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->routeIs('antrian.index') || request()->routeIs('customer.index') ? 'active' : '' }}">
          <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
                Antrian
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('antrian.index') }}" class="nav-link {{ request()->routeIs('antrian.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Antrian Workshop</p>
                </a>
            </li>
            <li>
                <a href="{{ route('customer.index') }}" class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Data Pelanggan</p>
                </a>
            </li>
        </ul>
        </li>
        <li class="nav-item {{ request()->routeIs('daftarStok') || request()->routeIs('mutasiStok') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('daftarStok') || request()->routeIs('mutasiStok') ? 'active' : '' }}">
              <i class="nav-icon fas fa-box"></i>
                <p>
                    Kelola Stok
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('daftarStok') }}" class="nav-link {{ request()->routeIs('daftarStok') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Daftar Stok
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mutasiStok') }}" class="nav-link {{ request()->routeIs('mutasiStok') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Mutasi Stok
                        </p>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>