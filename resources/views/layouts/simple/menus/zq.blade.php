<nav class="sidebar-main">
    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
    <div id="sidebar-menu">
        <ul class="sidebar-links" id="simple-bar">
            <li class="back-btn"><a href="#"><img class="img-fluid"
                        src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a>
                <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                        aria-hidden="true"></i></div>
            </li>
            {{-- <li class="pin-title sidebar-main-title">
                <div>
                    <h6>Pinned</h6>
                </div>
            </li> --}}
            <li class="sidebar-main-title">
                <div>
                    <h6 class="lan-0">Menu</h6>
                </div>
            </li>


            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('zq.dashboard')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                    </svg>
                    <span class="lan-0">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title"
                    href="javascript:;">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                    </svg>
                    <span class="lan-0">Admins</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{route('zq.admins.create')}}">Add Admin</a></li>
                    <li><a href="{{route('zq.admins.index')}}">Admin List</a></li>
                </ul>
            </li>

            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title"
                    href="javascript:;">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"></use>
                    </svg>
                    <span class="lan-0">Partners / Territories</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{route('zq.partners.create')}}">Add Partner</a></li>
                    <li><a href="{{route('zq.partners.index')}}">Partner List</a></li>
                    <li><a href="{{route('zq.partners.deactivated.list')}}">Deactivated Partners</a></li>
                </ul>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('zq.users.index')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"> </use>
                    </svg><span>Users</span>
                </a>
            </li>

            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title"
                    href="javascript:;">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-button') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-button') }}"></use>
                    </svg>
                    <span class="lan-0">Affiliates</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{route('zq.affiliates.create')}}">Add Affiliate</a></li>
                    <li><a href="{{route('zq.affiliates.index')}}">Affiliate List</a></li>
                    <li><a href="{{route('zq.affiliates.deactivated.list')}}">Deactivated Affiliates</a></li>
                </ul>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('default.movies.index')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-blog') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-blog') }}"></use>
                    </svg>
                    <span class="lan-0">Movies</span>
                </a>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('default.shows.index')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-sample-page') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-sample-page') }}"></use>
                    </svg>
                    <span class="lan-0">Shows</span>
                </a>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('default.events.index')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-task') }}"></use>
                    </svg>
                    <span class="lan-0">Events</span>
                </a>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('zq.payments.index')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-charts') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-charts') }}"> </use>
                    </svg><span>Transactions</span>
                </a>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title" href="javascript:;">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-others') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-others') }}"></use>
                    </svg>
                    <span class="lan-0">Master Data</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{route('zq.genres.index')}}">Genres</a></li>
                    <li><a href="{{route('zq.event-type.index')}}">Events</a></li>
                    <li><a href="{{route('zq.ott.index')}}">OTT Platforms</a></li>
                    <li><a href="{{route('zq.countries.index')}}">Countries</a></li>
                    <li><a href="{{route('zq.categorizes.index')}}">Categorize</a></li>
                </ul>
            </li>



            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('zq.profile.edit', ['zq' => 1])}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"> </use>
                    </svg><span>Profile</span>
                </a>
            </li>

            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('zq.settings.site')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-board') }}"> </use>
                    </svg><span>Settings</span>
                </a>
            </li>

            {{-- <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="#">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-bookmark') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-bookmark') }}"> </use>
                    </svg><span>Bookmarks</span>
                </a>
            </li>

            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title"
                    href="#">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                    </svg>
                    <span class="lan-3">Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="#">Online course</a></li>
                    <li><a href="#">Crypto</a></li>
                </ul>
            </li> --}}


        </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
</nav>
