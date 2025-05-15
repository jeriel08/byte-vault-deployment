<nav class="navbar pos-navbar navbar-expand-lg">
    <div class="container-fluid">
        <!-- Left side (70%) -->
        <div class="d-flex align-items-center" style="width: 70%;">
            <a class="navbar-brand" href="{{ route('pos.products') }}">
                <img src="{{ asset('images/logo-cropped.png') }}" alt="POS System">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-lg-0 d-flex justify-content-center flex-grow-1">
                    <!-- Search Bar -->
                    <li class="nav-item me-3 d-flex align-items-center">
                        <div class="search-bar-container">
                            <span class="material-icons-outlined me-2">search</span>
                            <input type="text" class="search-input" placeholder="Search">
                        </div>
                    </li>
                    <!-- POS and Sales Links -->
                    <li class="nav-item me-2">
                        <a class="nav-link {{ request()->routeIs('pos.products') ? 'active' : '' }}"
                            href="{{ route('pos.products') }}">POS</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link {{ request()->routeIs('pos.sales') ? 'active' : '' }}"
                            href="{{ route('pos.sales') }}">Sales</a>
                    </li>
                    <!-- Switch to Inventory for Admin and Manager -->
                    @auth
                        @if (in_array(auth()->user()->role, ['Admin', 'Manager']))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    Switch to Inventory
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>

        <!-- Right side (30%) -->
        <div class="d-flex justify-content-end" style="width: 30%;">
            <!-- Right-side account section -->
            <div class="d-flex align-items-center account-section">
                <div class="d-flex align-items-center me-3">
                    <span class="material-icons-outlined me-2 fs-1">account_circle</span>
                    <div>
                        <p class="fw-bold mb-0">{{ Auth::user()->firstName }} {{ Auth::user()->lastName }}</p>
                        <small class="mt-0">{{ Auth::user()->role }}</small>
                    </div>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="btn border-0 bg-transparent p-0 ms-2">
                            <span class="material-icons-outlined">arrow_drop_down</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                            <span class="material-icons-outlined">settings</span>
                            Account Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <span class="material-icons-outlined">logout</span>
                                Logout
                            </a>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>