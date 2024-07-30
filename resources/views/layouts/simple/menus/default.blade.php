<nav class="sidebar-main">
    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
    <div id="sidebar-menu">
        <ul class="sidebar-links" id="simple-bar">
            <li class="back-btn"><a href="#"><img class="img-fluid"
                        src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a>
                <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                        aria-hidden="true"></i></div>
            </li>
            <li class="pin-title sidebar-main-title">
                <div>
                    <h6>Pinned</h6>
                </div>
            </li>
            <li class="sidebar-main-title">
                <div>
                    <h6 class="lan-1">General</h6>
                </div>
            </li>
            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
                <label class="badge badge-light-primary">5</label><a class="sidebar-link sidebar-title"
                    href="#">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                    </svg><span class="lan-3">Dashboard</span></a>
                <ul class="sidebar-submenu">
                    <li><a href="#">Online course</a></li>
                    <li><a href="#">Crypto</a></li>
                </ul>
            </li>
            
            <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a
                    class="sidebar-link sidebar-title link-nav" href="#">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-bookmark') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-bookmark') }}"> </use>
                    </svg><span>Bookmarks</span></a></li>

            
        </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
</nav>