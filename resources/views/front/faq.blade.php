@extends('front.layout')
@section('content')
<div id="main-wrapper" class="container">
    <div class="entry-content">
        <div class="container ">
            <div class="faq">
                <h1>FAQ</h1>
                <div class="row ">

                    {!! $faq !!}

                </div>
            </div>
        </div>
    </div>
</div><!-- Main Wrapper -->
@stop
