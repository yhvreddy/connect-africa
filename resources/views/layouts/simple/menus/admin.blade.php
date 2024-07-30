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
                <a class="sidebar-link sidebar-title link-nav" href="{{route('admin.dashboard')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                    </svg>
                    <span class="lan-0">Dashboard</span>
                </a>
            </li>
            <!--
            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title"
                    href="javascript:;">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-blog') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-blog') }}"></use>
                    </svg>
                    <span class="lan-0">Movies</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{route('default.movies.create')}}">Add Movie</a></li>
                    <li><a href="{{route('default.movies.index')}}">Movie List</a></li>
                    @php
                        $movieCategories = \App\Models\Categorize::select('slug', 'title', 'id')->where('is_in_menu', 1)->where('type', 'movies')->get();
                    @endphp
                    @foreach ($movieCategories as $movieCategory)
                    <li><a href="{{route('default.movies.categorize.list', ['categorize'=>$movieCategory->slug])}}">{{$movieCategory->title}}</a></li>
                    @endforeach
                </ul>
            </li>

            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title"
                    href="javascript:;">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-sample-page') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-sample-page') }}"></use>
                    </svg>
                    <span class="lan-0">Shows</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{route('default.shows.create')}}">Add Show</a></li>
                    <li><a href="{{route('default.shows.index')}}">Show List</a></li>
                    @php
                        $showCategories = \App\Models\Categorize::select('slug', 'title', 'id')->where('is_in_menu', 1)->where('type', 'shows')->get();
                    @endphp
                    @foreach ($showCategories as $showCategory)
                        <li><a href="{{route('default.shows.categorize.list', ['categorize'=>$showCategory->slug])}}">{{$showCategory->title}}</a></li>
                    @endforeach
                </ul>
            </li>

            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title"
                    href="javascript:;">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-task') }}"></use>
                    </svg>
                    <span class="lan-0">Events</span>
                </a>
                <ul class="sidebar-submenu">
                    <li><a href="{{route('default.events.create')}}">Add Event</a></li>
                    <li><a href="{{route('default.events.index')}}">Event List</a></li>
                </ul>
            </li>
            {{-- <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('admin.profile.edit', ['admin' => 2])}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"> </use>
                    </svg><span>Profile</span>
                </a>
            </li> --}}
            -->
            <li class="sidebar-list">
                <i class="fa fa-thumb-tack"></i>
                <a class="sidebar-link sidebar-title link-nav" href="{{route('admin.users.index')}}">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-user') }}"> </use>
                    </svg><span>Users</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
</nav>
