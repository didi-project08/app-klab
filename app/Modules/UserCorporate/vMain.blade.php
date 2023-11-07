@extends('BASE.LayoutAdmin.master')

@section('title')
	User Corporate
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
            <h4 class="d-none d-sm-block">User Corporate</h4>
			<h5 class="d-block d-sm-none">User Corporate</h5>
		@endslot
        @slot('pathHome')
			{{ $base_url }}
		@endslot
		<li class="breadcrumb-item active">User Corporate</li>
	@endcomponent

@endsection

@section('content')

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false" class="p-2">
            <div class="easyui-tabs" data-options="fit:true, border:true, tabPosition:'top'">
                <div data-options="title:'User Corporate', border:true" style="padding:10px">
                    <div class="easyui-layout" data-options="fit:true">
                        <div data-options="region:'center', title:'User List', split:true, hideCollapsedContent:false, border:false, href:'{{$url}}b1/index'" class="p-2"></div>
                        <div data-options="region:'south', title:'User Client Access', split:true, hideCollapsedContent:false, border:false, href:'{{$url}}b2/index'" class="p-2" style="height:50%"></div>
                    </div>
                </div>
                <div data-options="title:'Role Configuration', border:true" style="padding:10px">
                    <div class="easyui-layout" data-options="fit:true">
                        <div data-options="region:'center', title:'Role List', split:true, hideCollapsedContent:false, border:false, href:'{{$url}}b3/index'" class="p-2"></div>
                        <div data-options="region:'east', title:'Module Access', split:true, hideCollapsedContent:false, border:false, href:'{{$url}}b4/index'" class="p-2" style="width:50%"></div>
                    </div>
                </div>
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
@endpush

@endsection