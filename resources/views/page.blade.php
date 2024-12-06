@extends('tablar::master')

@inject('layoutHelper', 'TakiElias\Tablar\Helpers\LayoutHelper')

@section('tablar_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('layout')
    @if(config('tablar','display_alert'))
        @include('tablar::partials.common.alerts-container')
    @endif
    @if(isset($layout))
        @includeIf('tablar::layouts.' . $layout)
    @else
        @includeIf('tablar::layouts.'. config('tablar.layout'))
    @endif
@show

@section('tablar_js')
    @stack('js')
    @yield('js')
@stop

