<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item {{ request()->routeIs('bot.index') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('bot.index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-lightbulb"></i>
                <p>
                    AI Antree
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('bot.index') }}" class="nav-link {{ request()->routeIs('bot.index') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Chatbot</p>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>