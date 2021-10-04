@extends('front.layout')
@section('content')
<div id="main-wrapper" class="container">
    <div class="entry-content">
        <div class="container ">
            <div class="faq">
                <div class="row ">
                     {!! $terms !!}
                </div>
            </div>
        </div>
    </div>
</div><!-- Main Wrapper -->
@stop