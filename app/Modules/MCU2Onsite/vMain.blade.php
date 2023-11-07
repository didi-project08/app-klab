@extends('BASE.LayoutAdmin.master')

@section('title')
	MCU Onsite
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
            <h4 class="d-none d-sm-block">MCU Onsite</h4>
			<h5 class="d-block d-sm-none">MCU Onsite</h5>
		@endslot
        @slot('pathHome')
			{{ $base_url }}
		@endslot
		<li class="breadcrumb-item active">MCU Onsite</li>
	@endcomponent

@endsection

@section('content')

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false, href:'{{$url}}b1/index'" class="p-3"></div>
        <div data-options="region:'south', border:false" class="p-3" style="height:60%">
            <div id="cp{{$_id_}}tabs" class="easyui-tabs" data-options="fit:true, border:true, tabPosition:'top'">
                <!-- <div data-options="title:'Corporate Employee', border:true, href:'{{$url}}b3/index'" style="padding:10px"></div> -->
                <div data-options="title:'MCU Patient', border:true, href:'{{$url}}b2/index'" style="padding:10px"></div>
            </div>
        </div>
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