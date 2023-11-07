@extends('BASE.LayoutAdmin.master')

@section('title')
	Corporate Client
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
            <h4 class="d-none d-sm-block">Corporate Client</h4>
			<h5 class="d-block d-sm-none">Corporate Client</h5>
		@endslot
        @slot('pathHome')
			{{ $base_url }}
		@endslot
		<li class="breadcrumb-item active">Corporate Client</li>
	@endcomponent

@endsection

@section('content')

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false, href:'{{$url}}b1/index'" class="p-3"></div>
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
@endpush

@endsection