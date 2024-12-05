<nav class="sidebar-main">
    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
    <div id="sidebar-menu">
        <ul class="sidebar-links" id="simple-bar">
            <li class="back-btn"><a href="#"><img class="img-fluid"
                        src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a>
                <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                        aria-hidden="true"></i></div>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.dashboard') }}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                    </svg>
                    <span class="lan-0">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.users.index') }}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"> </use>
                    </svg><span>Users</span>
                </a>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.subscription.list') }}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"> </use>
                    </svg><span>Subscriptions</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
</nav>
