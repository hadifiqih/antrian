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
                <a href="{{ route('buatAntrianWorkshop') }}" class="nav-link {{ request()->routeIs('buatAntrianWorkshop') || request()->routeIs('order.edit') ? 'active' : '' }}">
                <i class="fas fa-plus-circle pl-1"></i>
                <p class="pl-2">Input Order</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('design.indexDesain') }}" class="nav-link {{ request()->routeIs('design.indexDesain') || request()->routeIs('order.edit') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Submit Project</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('antrian.index') }}" class="nav-link {{ request()->routeIs('antrian.index') || request()->routeIs('antrian.edit') || request()->routeIs('antrian.show') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>List Order</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('report.sales') }}" class="nav-link {{ request()->routeIs('report.sales') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Ringkasan Penjualan</p>
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
        <li class="nav-item">
            <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->routeIs('pos.addOrder') || request()->routeIs('pos.manageProduct') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cash-register"></i>
                <p>
                    POS Bahan
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('pos.addOrder') }}" class="nav-link {{ request()->routeIs('pos.addOrder') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <p style="margin-left:7px">Tambah Transaksi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pos.manageProduct') }}" class="nav-link {{ request()->routeIs('pos.manageProduct') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <p style="margin-left:7px">Daftar Produk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pos.laporanBahan') }}" class="nav-link {{ request()->routeIs('pos.laporanBahan') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <p style="margin-left:7px">Laporan Penjualan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pos.laporanItem') }}" class="nav-link {{ request()->routeIs('pos.laporanItem') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <p style="margin-left:7px">Laporan Bahan</p>
                    </a>
                </li>
            </ul>
        </li>