<li class="nav-item {{ request()->routeIs('design.indexDesain') || request()->routeIs('order.edit') || request()->routeIs('antrian.index') || request()->routeIs('antrian.edit') || request()->routeIs('antrian.show') || request()->routeIs('report.sales') || request()->routeIs('buatAntrianWorkshop') ? 'menu-open' : '' }}">
    <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->routeIs('design.indexDesain') || request()->routeIs('order.edit') || request()->routeIs('antrian.index') || request()->routeIs('antrian.edit') || request()->routeIs('antrian.show') || request()->routeIs('report.sales') || request()->routeIs('buatAntrianWorkshop') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
            Antrian
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('antrian.index') }}" class="nav-link {{ request()->routeIs('antrian.index') || request()->routeIs('antrian.edit') || request()->routeIs('antrian.show') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>List Order</p>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item">
    <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->routeIs('pos.addOrder') || request()->routeIs('pos.manageProduct') ? 'active' : '' }}">
    <i class="nav-icon fas fa-cash-register"></i>
        <p>
            Laporan
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('estimator.laporanPenugasan') }}" class="nav-link {{ request()->routeIs('estimator.laporanPenugasan') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Laporan Workshop</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pos.laporanBahan') }}" class="nav-link {{ request()->routeIs('pos.laporanBahan') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Laporan Penjualan</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('sales.summaryReport') }}" class="nav-link {{ request()->routeIs('sales.summaryReport') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Laporan Aktivitas Sales</p>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item {{ request()->routeIs('documentation.gallery') ? 'menu-open' : '' }}">
    <a href="" class="nav-link {{ request()->routeIs('documentation.gallery') ? 'active' : '' }}">
        <i class="nav-icon fas fa-camera"></i>
        <p>
            Dokumentasi
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('documentation.gallery') }}" class="nav-link {{ request()->routeIs('documentation.gallery') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i>
            <p>Galeri Dokumentasi</p>
            </a>
        </li>
    </ul>
</li>
