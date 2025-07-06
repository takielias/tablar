@extends('tablar::master')

@inject('layoutHelper', 'TakiElias\Tablar\Helpers\LayoutHelper')

@section('tablar_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('layout')
    @include('layouts.marketing')
@show

@section('tablar_js')
    @stack('js')
    @yield('js')
@stop

