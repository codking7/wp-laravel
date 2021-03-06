@extends('layouts/marketing')


@section('meta')

<meta name="description" content="Since most of our projects are bound by our non-disclosure agreement, we can only show a small fraction of the thousands of projects our expert team has actually worked on.  See some of our best work on our portfolio page." />

<link rel="canonical" href="{{  route('our-code') }}" />

@stop


@section('title')
<title>Your Designs Brought to Life - PSD to HTML by Code My Views</title>
@endsection

@section('body')
<body class="portfolio-page">
@endsection

@section('content')
{!! setBodyClassIfPjax(['portfolio-page']) !!}

<section class="visual3 repeatable">
    <div class="container">
        <img src="{{ asset('images/bg-call-area.jpg') }}" alt="image description" class="bg-img tiled" >
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <header class="text-center">
                    <h1>{{ $h1_title }}</h1>
                    <h2>Check it out.</h2>
                </header>
                <p>Since most of our projects are bound by our <a target="_blank" href="{{ route('legal') }}">non-disclosure agreement</a>, we can only show a small fraction of the thousands of projects our expert team has actually worked on. We've tried to snapshot some of the latest and greatest below. Code samples included for each.</p>
            </div>
        </div>
    </div>
</section><!-- /visual3 -->
<section class="service-overview">
    <div class="container">
        <div class="tab-content">
            <div class="tab-pane active" id="tabs-01-01">
                <div class="row">
                    <div class="col-lg-12">
                        <div data-controller="portfolio">
                            <div id="portfolio-items">
                                <div id="single-portfolio-item">

                                    <div class="container" v-if="!loaded">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <div class="well well-small text-center" id="portfolio-loader">
                                                <h2>Loading Portfolio...<br /><i class="fa fa-spin fa-spinner"></i></h2>
                                            </div>
                                        </div>
                                    </div><!--container-->
                                    
                                    
                                    <div v-for="item in portfolioItems" v-if="loaded" class="portfolio-item">
                                            <img :src="item.image"/>

                                            <div class="hover-section">
                                                <h3>@{{ item.name }}</h3>
                                                <p>@{{ item.type }}</p>

                                                <a v-on:click="openCodeModal(item.name)" class="btn btn-success">View Code</a>
                                            </div><!--hover-section-->
                                            <div class="trans-bg"></div>
                                    </div><!--portfolio-item-->
                                    
                                </div><!--portfolio-item-->
                                <div class="modal fade dark" id="modal-portfolio">
                                  <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                                        <h4 class="modal-title">@{{ modal.name + ' Project' }}</h4>
                                      </div>
                                      <div class="modal-body">

                                        <div class="text-center">

                                            <div class="row">
                                                <div class="col-sm-10 col-sm-offset-1">

                                                    <h4 v-html="modal.description"></h4>

                                                </div><!--col-->
                                            </div><!--row-->
                                            
                                            <br />

                                            <div class="btn-group btn-group-lg">
                                                <a v-for="tab in modal.tabs"
                                                   href="#@{{ tab.id }}"
                                                   data-toggle="tab"
                                                   class="btn btn-lg btn-primary"><span>@{{ tab.text }}</span> <i class="fa fa-@{{ tab.icon }}"></i>
                                                </a>
                                            
                                                <a :href="modal.preview_link" target="_blank" rel="nofollow" class="btn btn-lg btn-primary">View Site <i class="fa fa-search"></i></a>
                                            </div><!--btn-group-->
                                        </div><!--text-center-->

                                        <br />

                                        <div class="code-block">
                                            <div class="tab-content">
                                                <div v-for="tab in modal.tabs"
                                                    class="tab-pane"
                                                    id="@{{ tab.id }}">
                                                    <pre class="portfolio-pre"><code class="@{{ tab.code_type }}" v-html="tab.content"></code></pre>
                                                </div><!--tab-pane-->
                                            </div><!--tab-content-->
                                        </div><!--code-block-->
                                      </div>
                                    </div><!-- /.modal-content -->
                                  </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </div><!--portfolio-items-->
                        </div>
                   </div><!--col--> 
                </div>
            </div>
        </div>
    </div>
</section><!-- /service-overview -->



@include('partials/call-to-action')

@include('partials/contact-info')

@stop