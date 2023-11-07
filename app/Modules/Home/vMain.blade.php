@extends('BASE.LayoutAdmin.master')

@section('title')
	Home
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
			<h4>Home</h4>
		@endslot
		@slot('pathHome')
			{{ $base_url }}
		@endslot
	@endcomponent

@endsection

@section('content')
	
	
	<div class="container-fluid">
		<h1>WELCOME</h1>
	</div>
	
	
	@push('scripts')
	<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/chart-custom.js') }}"></script>
	@endpush

@endsection