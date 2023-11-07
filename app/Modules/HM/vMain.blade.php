@extends('BASE.LayoutAdmin.master')

@section('title')
	MCU Project
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
            <h4 class="d-none d-sm-block">Device List</h4>
			<h5 class="d-block d-sm-none">Device List</h5>
		@endslot
        @slot('pathHome')
			{{ $base_url }}
		@endslot
		<li class="breadcrumb-item active">Device List</li>
	@endcomponent

@endsection

@section('content')

<div class="container-fluid" id="">
    <div class="row">
        <?php foreach ($device as $k => $v) { ?>
            <div class="col-sm-12 col-md-6">
                <div class="card shadow-sm shadow-showcase">
                    <div class="card-header bg-light" style="padding: 15px 20px !important">
                        <h5><a href="{{ $url }}b1/sample/{{ $v->id }}">{{ $v->title; }}</a></h5>
                        <small class="font-dark">{{ $v->id; }}</small>
                    </div>
                    <div class="card-body" style="padding: 15px 30px !important">
                        <?php
                            $online_title = "Online";
                            $online_color = "ribbon-success";
                            $badge_color = "badge-success";
                            if($v->online == 0){
                                $online_title = "Offline";
                                $online_color = "ribbon-danger";
                                $badge_color = "badge-danger";
                            }
                        ?>
                        <div class="ribbon ribbon-clip-right ribbon-right {{ $online_color }}">{{ $online_title }}</div>
                        <div><b>{{ $v->province }}, {{ $v->city }}</b></div>
                        <div>{{ $v->address }}</div>
                        <br>
                        <div>{{ $v->desc }}</div>
                        <br>
                        <div class="figure text-end d-block">
                            <cite title="Source Title">
                            <span class="badge {{ $badge_color }}">LAST ONLINE : {{ $v->last_online }}</span>
                            </cite>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

  
    
</div>

@push('scripts')
<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/chart-custom.js') }}"></script>

<script>
    var docHeight = $(document).height();
    docHeight -= 120;
    $("#content_container").css("height",docHeight+"px");
</script>

<script>
    $eu(function(){
        $eu("#cp{{$_id_}}tabs").tabs('select',1);
    })
</script>
@endpush

@endsection