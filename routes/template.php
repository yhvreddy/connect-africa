<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

//Language Change
Route::get('lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'de', 'es', 'fr', 'pt', 'cn', 'ae'])) {
        abort(400);
    }
    Session()->put('locale', $locale);
    Session::get('locale');
    return redirect()->back();
})->name('lang');

Route::group(['prefix' => 'demo'], function (){ 
    Route::prefix('dashboard')->group(function () {
        Route::view('index', 'template.dashboard.index')->name('index');
        Route::view('dashboard-02', 'template.dashboard.dashboard-02')->name('dashboard-02');
        Route::view('dashboard-03', 'template.dashboard.dashboard-03')->name('dashboard-03');
        Route::view('dashboard-04', 'template.dashboard.dashboard-04')->name('dashboard-04');
        Route::view('dashboard-05', 'template.dashboard.dashboard-05')->name('dashboard-05');
    });

    Route::prefix('widgets')->group(function () {
        Route::view('general-widget', 'template.widgets.general-widget')->name('general-widget');
        Route::view('chart-widget', 'template.widgets.chart-widget')->name('chart-widget');
    });

    Route::prefix('page-layouts')->group(function () {
        Route::view('box-layout', 'template.page-layout.box-layout')->name('box-layout');
        Route::view('layout-rtl', 'template.page-layout.layout-rtl')->name('layout-rtl');
        Route::view('layout-dark', 'template.page-layout.layout-dark')->name('layout-dark');
        Route::view('hide-on-scroll', 'template.page-layout.hide-on-scroll')->name('hide-on-scroll');
        Route::view('footer-light', 'template.page-layout.footer-light')->name('footer-light');
        Route::view('footer-dark', 'template.page-layout.footer-dark')->name('footer-dark');
        Route::view('footer-fixed', 'template.page-layout.footer-fixed')->name('footer-fixed');
    });

    Route::prefix('project')->group(function () {
        Route::view('projects', 'template.project.projects')->name('projects');
        Route::view('projectcreate', 'template.project.projectcreate')->name('projectcreate');
    });

    Route::view('file-manager', '.template.file-manager')->name('file-manager');
    Route::view('kanban', 'template.kanban')->name('kanban');

    Route::prefix('ecommerce')->group(function () {
        Route::view('product', 'template.apps.product')->name('product');
        Route::view('page-product', 'template.apps.product-page')->name('product-page');
        Route::view('list-products', 'template.apps.list-products')->name('list-products');
        Route::view('payment-details', 'template.apps.payment-details')->name('payment-details');
        Route::view('order-history', 'template.apps.order-history')->name('order-history');
        Route::view('invoice-template', 'template.apps.invoice-template')->name('invoice-template');
        Route::view('cart', 'template.apps.cart')->name('cart');
        Route::view('list-wish', 'template.apps.list-wish')->name('list-wish');
        Route::view('checkout', 'template.apps.checkout')->name('checkout');
        Route::view('pricing', 'template.apps.pricing')->name('pricing');
    });

    Route::prefix('email')->group(function () {
        Route::view('email-application', 'template.apps.email-application')->name('email-application');
        Route::view('email-compose', 'template.apps.email-compose')->name('email-compose');
    });

    Route::prefix('chat')->group(function () {
        Route::view('chat', 'template.apps.chat')->name('chat');
        Route::view('video-chat', 'template.apps.video-chat')->name('chat-video');
    });

    Route::prefix('users')->group(function () {
        Route::view('user-profile', 'template.apps.user-profile')->name('user-profile');
        Route::view('edit-profile', 'template.apps.edit-profile')->name('edit-profile');
        Route::view('user-cards', 'template.apps.user-cards')->name('user-cards');
    });


    Route::view('bookmark', 'template.apps.bookmark')->name('bookmark');
    Route::view('contacts', 'template.apps.contacts')->name('contacts');
    Route::view('task', 'template.apps.task')->name('task');
    Route::view('calendar-basic', 'template.apps.calendar-basic')->name('calendar-basic');
    Route::view('social-app', 'template.apps.social-app')->name('social-app');
    Route::view('to-do', 'template.apps.to-do')->name('to-do');
    Route::view('search', 'template.apps.search')->name('search');

    Route::prefix('ui-kits')->group(function () {
        Route::view('state-color', 'template.ui-kits.state-color')->name('state-color');
        Route::view('typography', 'template.ui-kits.typography')->name('typography');
        Route::view('avatars', 'template.ui-kits.avatars')->name('avatars');
        Route::view('helper-classes', 'template.ui-kits.helper-classes')->name('helper-classes');
        Route::view('grid', 'template.ui-kits.grid')->name('grid');
        Route::view('tag-pills', 'template.ui-kits.tag-pills')->name('tag-pills');
        Route::view('progress-bar', 'template.ui-kits.progress-bar')->name('progress-bar');
        Route::view('modal', 'template.ui-kits.modal')->name('modal');
        Route::view('alert', 'template.ui-kits.alert')->name('alert');
        Route::view('popover', 'template.ui-kits.popover')->name('popover');
        Route::view('tooltip', 'template.ui-kits.tooltip')->name('tooltip');
        Route::view('loader', 'template.ui-kits.loader')->name('loader');
        Route::view('dropdown', 'template.ui-kits.dropdown')->name('dropdown');
        Route::view('accordion', 'template.ui-kits.accordion')->name('accordion');
        Route::view('tab-bootstrap', 'template.ui-kits.tab-bootstrap')->name('tab-bootstrap');
        Route::view('tab-material', 'template.ui-kits.tab-material')->name('tab-material');
        Route::view('box-shadow', 'template.ui-kits.box-shadow')->name('box-shadow');
        Route::view('list', 'template.ui-kits.list')->name('list');
    });

    Route::prefix('bonus-ui')->group(function () {
        Route::view('scrollable', 'template.bonus-ui.scrollable')->name('scrollable');
        Route::view('tree', 'template.bonus-ui.tree')->name('tree');
        Route::view('bootstrap-notify', 'template.bonus-ui.bootstrap-notify')->name('bootstrap-notify');
        Route::view('rating', 'template.bonus-ui.rating')->name('rating');
        Route::view('dropzone', 'template.bonus-ui.dropzone')->name('dropzone');
        Route::view('tour', 'template.bonus-ui.tour')->name('tour');
        Route::view('sweet-alert2', 'template.bonus-ui.sweet-alert2')->name('sweet-alert2');
        Route::view('modal-animated', 'template.bonus-ui.modal-animated')->name('modal-animated');
        Route::view('owl-carousel', 'template.bonus-ui.owl-carousel')->name('owl-carousel');
        Route::view('ribbons', 'template.bonus-ui.ribbons')->name('ribbons');
        Route::view('pagination', 'template.bonus-ui.pagination')->name('pagination');
        Route::view('breadcrumb', 'template.bonus-ui.breadcrumb')->name('breadcrumb');
        Route::view('range-slider', 'template.bonus-ui.range-slider')->name('range-slider');
        Route::view('image-cropper', 'template.bonus-ui.image-cropper')->name('image-cropper');
        Route::view('sticky', 'template.bonus-ui.sticky')->name('sticky');
        Route::view('basic-card', 'template.bonus-ui.basic-card')->name('basic-card');
        Route::view('creative-card', 'template.bonus-ui.creative-card')->name('creative-card');
        Route::view('tabbed-card', 'template.bonus-ui.tabbed-card')->name('tabbed-card');
        Route::view('dragable-card', 'template.bonus-ui.dragable-card')->name('dragable-card');
        Route::view('timeline-v-1', 'template.bonus-ui.timeline-v-1')->name('timeline-v-1');
        Route::view('timeline-v-2', 'template.bonus-ui.timeline-v-2')->name('timeline-v-2');
        Route::view('timeline-small', 'template.bonus-ui.timeline-small')->name('timeline-small');
    });

    Route::prefix('builders')->group(function () {
        Route::view('form-builder-1', 'template.builders.form-builder-1')->name('form-builder-1');
        Route::view('form-builder-2', 'template.builders.form-builder-2')->name('form-builder-2');
        Route::view('pagebuild', 'template.builders.pagebuild')->name('pagebuild');
        Route::view('button-builder', 'template.builders.button-builder')->name('button-builder');
    });

    Route::prefix('animation')->group(function () {
        Route::view('animate', 'template.animation.animate')->name('animate');
        Route::view('scroll-reval', 'template.animation.scroll-reval')->name('scroll-reval');
        Route::view('aos', 'template.animation.aos')->name('aos');
        Route::view('tilt', 'template.animation.tilt')->name('tilt');
        Route::view('wow', 'template.animation.wow')->name('wow');
    });


    Route::prefix('icons')->group(function () {
        Route::view('flag-icon', 'template.icons.flag-icon')->name('flag-icon');
        Route::view('font-awesome', 'template.icons.font-awesome')->name('font-awesome');
        Route::view('ico-icon', 'template.icons.ico-icon')->name('ico-icon');
        Route::view('themify-icon', 'template.icons.themify-icon')->name('themify-icon');
        Route::view('feather-icon', 'template.icons.feather-icon')->name('feather-icon');
        Route::view('whether-icon', 'template.icons.whether-icon')->name('whether-icon');
        Route::view('simple-line-icon', 'template.icons.simple-line-icon')->name('simple-line-icon');
        Route::view('material-design-icon', 'template.icons.material-design-icon')->name('material-design-icon');
        Route::view('pe7-icon', 'template.icons.pe7-icon')->name('pe7-icon');
        Route::view('typicons-icon', 'template.icons.typicons-icon')->name('typicons-icon');
        Route::view('ionic-icon', 'template.icons.ionic-icon')->name('ionic-icon');
    });

    Route::prefix('buttons')->group(function () {
        Route::view('buttons', 'template.buttons.buttons')->name('buttons');
        Route::view('flat-buttons', 'template.buttons.flat-buttons')->name('flat-buttons');
        Route::view('edge-buttons', 'template.buttons.buttons-edge')->name('buttons-edge');
        Route::view('raised-button', 'template.buttons.raised-button')->name('raised-button');
        Route::view('button-group', 'template.buttons.button-group')->name('button-group');
    });

    Route::prefix('forms')->group(function () {
        Route::view('form-validation', 'template.forms.form-validation')->name('form-validation');
        Route::view('base-input', 'template.forms.base-input')->name('base-input');
        Route::view('radio-checkbox-control', 'template.forms.radio-checkbox-control')->name('radio-checkbox-control');
        Route::view('input-group', 'template.forms.input-group')->name('input-group');
        Route::view('megaoptions', 'template.forms.megaoptions')->name('megaoptions');
        Route::view('datepicker', 'template.forms.datepicker')->name('datepicker');
        Route::view('time-picker', 'template.forms.time-picker')->name('time-picker');
        Route::view('datetimepicker', 'template.forms.datetimepicker')->name('datetimepicker');
        Route::view('daterangepicker', 'template.forms.daterangepicker')->name('daterangepicker');
        Route::view('touchspin', 'template.forms.touchspin')->name('touchspin');
        Route::view('select2', 'template.forms.select2')->name('select2');
        Route::view('switch', 'template.forms.switch')->name('switch');
        Route::view('typeahead', 'template.forms.typeahead')->name('typeahead');
        Route::view('clipboard', 'template.forms.clipboard')->name('clipboard');
        Route::view('default-form', 'template.forms.default-form')->name('default-form');
        Route::view('form-wizard', 'template.forms.form-wizard')->name('form-wizard');
        Route::view('form-two-wizard', 'template.forms.form-wizard-two')->name('form-wizard-two');
        Route::view('wizard-form-three', 'template.forms.form-wizard-three')->name('form-wizard-three');
        Route::post('form-wizard-three', function () {
            return redirect()->route('form-wizard-three');
        })->name('form-wizard-three-post');
    });

    Route::prefix('tables')->group(function () {
        Route::view('bootstrap-basic-table', 'template.tables.bootstrap-basic-table')->name('bootstrap-basic-table');
        Route::view('bootstrap-sizing-table', 'template.tables.bootstrap-sizing-table')->name('bootstrap-sizing-table');
        Route::view('bootstrap-border-table', 'template.tables.bootstrap-border-table')->name('bootstrap-border-table');
        Route::view('bootstrap-styling-table', 'template.tables.bootstrap-styling-table')->name('bootstrap-styling-table');
        Route::view('table-components', 'template.tables.table-components')->name('table-components');
        Route::view('datatable-basic-init', 'template.tables.datatable-basic-init')->name('datatable-basic-init');
        Route::view('datatable-advance', 'template.tables.datatable-advance')->name('datatable-advance');
        Route::view('datatable-styling', 'template.tables.datatable-styling')->name('datatable-styling');
        Route::view('datatable-ajax', 'template.tables.datatable-ajax')->name('datatable-ajax');
        Route::view('datatable-server-side', 'template.tables.datatable-server-side')->name('datatable-server-side');
        Route::view('datatable-plugin', 'template.tables.datatable-plugin')->name('datatable-plugin');
        Route::view('datatable-api', 'template.tables.datatable-api')->name('datatable-api');
        Route::view('datatable-data-source', 'template.tables.datatable-data-source')->name('datatable-data-source');
        Route::view('datatable-ext-autofill', 'template.tables.datatable-ext-autofill')->name('datatable-ext-autofill');
        Route::view('datatable-ext-basic-button', 'template.tables.datatable-ext-basic-button')->name('datatable-ext-basic-button');
        Route::view('datatable-ext-col-reorder', 'template.tables.datatable-ext-col-reorder')->name('datatable-ext-col-reorder');
        Route::view('datatable-ext-fixed-header', 'template.tables.datatable-ext-fixed-header')->name('datatable-ext-fixed-header');
        Route::view('datatable-ext-html-5-data-export', 'template.tables.datatable-ext-html-5-data-export')->name('datatable-ext-html-5-data-export');
        Route::view('datatable-ext-key-table', 'template.tables.datatable-ext-key-table')->name('datatable-ext-key-table');
        Route::view('datatable-ext-responsive', 'template.tables.datatable-ext-responsive')->name('datatable-ext-responsive');
        Route::view('datatable-ext-row-reorder', 'template.tables.datatable-ext-row-reorder')->name('datatable-ext-row-reorder');
        Route::view('datatable-ext-scroller', 'template.tables.datatable-ext-scroller')->name('datatable-ext-scroller');
        Route::view('jsgrid-table', 'template.tables.jsgrid-table')->name('jsgrid-table');
    });

    Route::prefix('charts')->group(function () {
        Route::view('echarts', 'template.charts.echarts')->name('echarts');
        Route::view('chart-apex', 'template.charts.chart-apex')->name('chart-apex');
        Route::view('chart-google', 'template.charts.chart-google')->name('chart-google');
        Route::view('chart-sparkline', 'template.charts.chart-sparkline')->name('chart-sparkline');
        Route::view('chart-flot', 'template.charts.chart-flot')->name('chart-flot');
        Route::view('chart-knob', 'template.charts.chart-knob')->name('chart-knob');
        Route::view('chart-morris', 'template.charts.chart-morris')->name('chart-morris');
        Route::view('chartjs', 'template.charts.chartjs')->name('chartjs');
        Route::view('chartist', 'template.charts.chartist')->name('chartist');
        Route::view('chart-peity', 'template.charts.chart-peity')->name('chart-peity');
    });

    Route::view('sample-page', 'template.pages.sample-page')->name('sample-page');
    Route::view('internationalization', 'template.pages.internationalization')->name('internationalization');

    // Route::prefix('starter-kit')->group(function () {
    // });

    Route::prefix('others')->group(function () {
        Route::view('400', 'template.errors.400')->name('error-400');
        Route::view('401', 'template.errors.401')->name('error-401');
        Route::view('403', 'template.errors.403')->name('error-403');
        Route::view('404', 'template.errors.404')->name('error-404');
        Route::view('500', 'template.errors.500')->name('error-500');
        Route::view('503', 'template.errors.503')->name('error-503');
    });

    Route::prefix('authentication')->group(function () {
        Route::view('login', 'template.authentication.login')->name('login');
        Route::view('login-one', 'template.authentication.login-one')->name('login-one');
        Route::view('login-two', 'template.authentication.login-two')->name('login-two');
        Route::view('login-bs-validation', 'template.authentication.login-bs-validation')->name('login-bs-validation');
        Route::view('login-bs-tt-validation', 'template.authentication.login-bs-tt-validation')->name('login-bs-tt-validation');
        Route::view('login-sa-validation', 'template.authentication.login-sa-validation')->name('login-sa-validation');
        Route::view('sign-up', 'template.authentication.sign-up')->name('sign-up');
        Route::view('sign-up-one', 'template.authentication.sign-up-one')->name('sign-up-one');
        Route::view('sign-up-two', 'template.authentication.sign-up-two')->name('sign-up-two');
        Route::view('sign-up-wizard', 'template.authentication.sign-up-wizard')->name('sign-up-wizard');
        Route::view('unlock', 'template.authentication.unlock')->name('unlock');
        Route::view('forget-password', 'template.authentication.forget-password')->name('forget-password');
        Route::view('reset-password', 'template.authentication.reset-password')->name('reset-password');
        Route::view('maintenance', 'template.authentication.maintenance')->name('maintenance');
    });

    Route::view('comingsoon', 'template.comingsoon.comingsoon')->name('comingsoon');
    Route::view('comingsoon-bg-video', 'template.comingsoon.comingsoon-bg-video')->name('comingsoon-bg-video');
    Route::view('comingsoon-bg-img', 'template.comingsoon.comingsoon-bg-img')->name('comingsoon-bg-img');

    Route::view('basic-template', 'template.email-templates.basic-template')->name('basic-template');
    Route::view('email-header', 'template.email-templates.email-header')->name('email-header');
    Route::view('template-email', 'template.email-templates.template-email')->name('template-email');
    Route::view('template-email-2', 'template.email-templates.template-email-2')->name('template-email-2');
    Route::view('ecommerce-templates', 'template.email-templates.ecommerce-templates')->name('ecommerce-templates');
    Route::view('email-order-success', 'template.email-templates.email-order-success')->name('email-order-success');


    Route::prefix('gallery')->group(function () {
        Route::view('index', 'template.apps.gallery')->name('gallery');
        Route::view('with-gallery-description', 'template.apps.gallery-with-description')->name('gallery-with-description');
        Route::view('gallery-masonry', 'template.apps.gallery-masonry')->name('gallery-masonry');
        Route::view('masonry-gallery-with-disc', 'template.apps.masonry-gallery-with-disc')->name('masonry-gallery-with-disc');
        Route::view('gallery-hover', 'template.apps.gallery-hover')->name('gallery-hover');
    });

    Route::prefix('blog')->group(function () {
        Route::view('index', 'template.apps.blog')->name('blog');
        Route::view('blog-single', 'template.apps.blog-single')->name('blog-single');
        Route::view('add-post', 'template.apps.add-post')->name('add-post');
    });


    Route::view('faq', 'template.apps.faq')->name('faq');

    Route::prefix('job-search')->group(function () {
        Route::view('job-cards-view', 'template.apps.job-cards-view')->name('job-cards-view');
        Route::view('job-list-view', 'template.apps.job-list-view')->name('job-list-view');
        Route::view('job-details', 'template.apps.job-details')->name('job-details');
        Route::view('job-apply', 'template.apps.job-apply')->name('job-apply');
    });

    Route::prefix('learning')->group(function () {
        Route::view('learning-list-view', 'template.apps.learning-list-view')->name('learning-list-view');
        Route::view('learning-detailed', 'template.apps.learning-detailed')->name('learning-detailed');
    });

    Route::prefix('maps')->group(function () {
        Route::view('map-js', 'template.apps.map-js')->name('map-js');
        Route::view('vector-map', 'template.apps.vector-map')->name('vector-map');
    });

    Route::prefix('editors')->group(function () {
        Route::view('summernote', 'template.apps.summernote')->name('summernote');
        Route::view('ckeditor', 'template.apps.ckeditor')->name('ckeditor');
        Route::view('simple-mde', 'template.apps.simple-mde')->name('simple-mde');
        Route::view('ace-code-editor', 'template.apps.ace-code-editor')->name('ace-code-editor');
    });

    Route::view('knowledgebase', 'template.apps.knowledgebase')->name('knowledgebase');
    Route::view('support-ticket', 'template.apps.support-ticket')->name('support-ticket');
    Route::view('landing-page', 'template.pages.landing-page')->name('landing-page');

    Route::prefix('layouts')->group(function () {
        Route::view('compact-sidebar', 'template.admin_unique_layouts.compact-sidebar'); //default //Dubai
        Route::view('box-layout', 'template.admin_unique_layouts.box-layout');    //default //New York //
        Route::view('dark-sidebar', 'template.admin_unique_layouts.dark-sidebar');

        Route::view('default-body', 'template.admin_unique_layouts.default-body');
        Route::view('compact-wrap', 'template.admin_unique_layouts.compact-wrap');
        Route::view('enterprice-type', 'template.admin_unique_layouts.enterprice-type');

        Route::view('compact-small', 'template.admin_unique_layouts.compact-small');
        Route::view('advance-type', 'template.admin_unique_layouts.advance-type');
        Route::view('material-layout', 'template.admin_unique_layouts.material-layout');

        Route::view('color-sidebar', 'template.admin_unique_layouts.color-sidebar');
        Route::view('material-icon', 'template.admin_unique_layouts.material-icon');
        Route::view('modern-layout', 'template.admin_unique_layouts.modern-layout');
    });

    Route::get('layout-{light}', function ($light) {
        session()->put('layout', $light);
        session()->get('layout');
        if ($light == 'vertical-layout') {
            return redirect()->route('pages-vertical-layout');
        }
        return redirect()->route('index');
        return 1;
    });
});
