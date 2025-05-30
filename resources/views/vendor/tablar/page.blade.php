@extends('tablar::master')

@inject('layoutHelper', 'TakiElias\Tablar\Helpers\LayoutHelper')

@section('tablar_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('layout')
    @if(isset($layout))
        @includeIf('tablar::layouts.' . $layout)
    @else
        @includeIf('tablar::layouts.'. config('tablar.layout'))
    @endif
@show

@section('tablar_js')
    <script 
  src="https://widgets.leadconnectorhq.com/loader.js"  
  data-resources-url="https://widgets.leadconnectorhq.com/chat-widget/loader.js" 
 data-widget-id="67e1ded91464712e761cdb98"   > 
 </script>
    @stack('js')
    @yield('js')
@stop

