@php
    $layout=\WpStarter\Wordpress\Admin\Facades\Route::current()->layout();
@endphp
<div class="wrap">
    <h1 class="wp-heading-inline">{{$layout->getTitle()}}</h1>
    @if($action=$layout->getAction())
    <a href="{{$action->getLink()}}" title="{{$action->getDesc()}}" class="page-title-action">
        {!! $action->getText() !!}
    </a>
    @endif
    @if($subTitle=$layout->getSubTitle())
    <span class="subtitle">
        {!! $subTitle !!}
    </span>
    @endif
    <hr class="wp-header-end">
    @foreach($layout->getNotices() as $notice)
        {!! $notice->render() !!}
    @endforeach
    <div class="wps-admin-content">
        @yield('content','Main admin content.This should be replaced by child view!')
    </div>
</div>
@php
$layout->clearNotices();
@endphp