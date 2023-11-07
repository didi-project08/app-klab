@extends('BASE.LayoutAdmin.master')

@section('title')
	MCU Project
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
            <h4 class="d-none d-sm-block">Device Sample</h4>
			<h5 class="d-block d-sm-none">Device Sample</h5>
		@endslot
        @slot('pathHome')
			{{ $base_url }}
		@endslot
		<li class="breadcrumb-item active">Device Sample</li>
	@endcomponent

@endsection

@section('content')

<div class="container-fluid" id="">
    <h4 class="font-primary f-w-700 mb-3">Selected Device</h4>
    <div class="row">
        <?php foreach ($device as $k => $v) { ?>
            <div class="col-sm-12 col-md-12">
                <div class="card shadow-sm shadow-showcase">
                    <div class="card-header bg-light" style="padding: 15px 20px !important">
                        <h5>{{ $v->title; }}</h5>
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
    
    <h4 class="font-primary f-w-700 mb-3">Sample List</h4>
    <div style="height:400px">
        <div class="easyui-layout" data-options="fit:true">
            <div data-options="region:'center', border:false" class="">
                <div id="cp{{$_idb_}}dGridToolbar">
                    <div class="row p-3">
                        <div class="col-12 col-md-8">
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/create'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridAdd()"><i class="fas fa-add fa-lg"></i> Add New</button> -->
                            <?php } ?>
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/update'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-secondary" onclick="jf{{$_idb_}}dGridEdit()"><i class="fas fa-edit fa-lg"></i> Edit</button> -->
                            <?php } ?>
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/delete'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-danger" onclick="jf{{$_idb_}}dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button> -->
                            <?php } ?>
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/confirmSave'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-warning" onclick="jf{{$_idb_}}dGridConfirm()"><i class="fas fa-file-pen fa-lg"></i> Confirm</button> -->
                            <?php } ?>
                        </div>
                        <div class="col-12 col-md-4">
                            <div style="float:right">
                                <input type="text" id="cp{{$_idb_}}dGridFilterSrcEvt_front">
                                <button class="btn btn-outline-primary" onclick="jf{{$_idb_}}dGridFilter_o()"><i class="fas fa-filter fa-lg"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="cp{{$_idb_}}dGrid"></table>
            </div>
        </div>
    </div>
  
</div>

@push('scripts')
<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/chart-custom.js') }}"></script>

<script>
    $eu(function(){
        $eu("#cp{{$_idb_}}dGrid").datagrid({
            toolbar: "#cp{{$_idb_}}dGridToolbar",
            border:true,
            striped: true,
            pagination: true,
            fit: true,
            fitColumns: false,
            rownumbers: true,
            checkbox: true,
            singleSelect: true,
            selectOnCheck: false,
            checkOnSelect: false,
            nowrap: false,
            pageList: [10, 20, 25, 50, 100],
            pageSize: 100,
            method: "GET",
            idField: "id",
            // url: "{{ $urlb }}read",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "auto_id",title: "<b>Auto ID</b>",align: "center",width: 100},
                {field: "sample_id",title: "<b>Sample ID</b>",align: "left",width: 200},
                {field: "patient_name",title: "<b>Patient Name</b>",align: "left",width: 250},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
                if(typeof jf{{$_id_}}b3dGridFilter_r === "function"){
                    jf{{$_id_}}b3dGridFilter_r();
                }
                if(typeof jf{{$_id_}}b3dGridFilterFront_p === "function"){
                    jf{{$_id_}}b3dGridFilterFront_p();
                }
                if(typeof jf{{$_id_}}b2dGridFilter_r === "function"){
                    jf{{$_id_}}b2dGridFilter_r();
                }
                if(typeof jf{{$_id_}}b2dGridFilterFront_p === "function"){
                    jf{{$_id_}}b2dGridFilterFront_p();
                }
            },
            onSelect:function(index,row){
                if(typeof jf{{$_id_}}b3dGridFilter_r === "function"){
                    jf{{$_id_}}b3dGridFilter_r();
                }
                if(typeof jf{{$_id_}}b3dGridFilterFront_p === "function"){
                    jf{{$_id_}}b3dGridFilterFront_p();
                }
                if(typeof jf{{$_id_}}b2dGridFilter_r === "function"){
                    jf{{$_id_}}b2dGridFilter_r();
                }
                if(typeof jf{{$_id_}}b2dGridFilterFront_p === "function"){
                    jf{{$_id_}}b2dGridFilterFront_p();
                }
            }
        })

        $eu("#cp{{$_idb_}}dGridFilterSrcEvt_front").textbox({
            prompt:"Type to search.",
            onChange:function(){
                jf{{$_idb_}}dGridFilterFront_p();
            }
        })
        jf{{$_idb_}}dGridFilter_i();
    })

    function jf{{$_idb_}}dGridFilter_i(){
        let cpModalId = "cpModal{{$_idb_}}DGridFilter";
        let cpModalUrl = "{{$urlb}}filter";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
    }
    function jf{{$_idb_}}dGridFilter_o(){
        let cpModalId = "cpModal{{$_idb_}}DGridFilter";
        cpGlobalModalOpen(cpModalId);
    }
    function jf{{$_idb_}}dGridFilter_g(){
        var srcEvt = $eu("#cp{{$_idb_}}dGridFilterSrcEvt").textbox('getValue');
        $eu("#cp{{$_idb_}}dGridFilterSrcEvt_front").textbox('setValue',srcEvt);
        var filterParams = $("#cp{{$_idb_}}dGridFilterForm").serialize();
        return filterParams;
    }
    function jf{{$_idb_}}dGridFilter_p(){
        var filterParams = jf{{$_idb_}}dGridFilter_g();
        $eu("#cp{{$_idb_}}dGrid").datagrid("load", "{{ $urlb }}read/{{ $device_id }}?"+filterParams);
        
        let cpModalId = "cpModal{{$_idb_}}DGridFilter";
        cpGlobalModalClose(cpModalId);
    }
    function jf{{$_idb_}}dGridFilter_r(){
        $eu("#cp{{$_idb_}}dGridFilterForm").form('clear');
        // jf{{$_idb_}}dGridFilter_p();
    }
    function jf{{$_idb_}}dGridFilterFront_p(){
        var srcEvt = $eu("#cp{{$_idb_}}dGridFilterSrcEvt_front").textbox('getValue');
        $eu("#cp{{$_idb_}}dGridFilterSrcEvt").textbox('setValue',srcEvt);
        jf{{$_idb_}}dGridFilter_p();
    }

    function jf{{$_idb_}}dGridAdd(){
        let cpModalId = "cpModal{{$_idb_}}DGridForm";
        let cpModalUrl = "{{$urlb}}add";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
    }   
    function jf{{$_idb_}}dGridEdit(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal{{$_idb_}}DGridForm";
            let cpModalUrl = "{{$urlb}}edit/"+sel.id;
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to EDIT',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridDelete(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        if(sel){
            Swal.fire({
                // width:'300px',
                customClass: {
                    confirmButton: 'btn btn-outline-primary',
                    cancelButton: 'btn btn-outline-danger'
                },
                buttonsStyling: false,
                icon: 'warning',
                title: 'Are you sure ?',
                text: 'DELETE data '+sel.code+', '+sel.title+' ?',
                showCancelButton:true,
                showConfirmButton:true
            }).then((result) => {
                if(result.isConfirmed){
                    jf{{$_idb_}}dGridDeleteGo();
                }
            })
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to DELETE',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridDeleteGo(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        preloader_block();
        $.ajax({
            type: 'DELETE',
            dataType: "JSON",
            url:"{{ $urlb }}delete/"+sel.id,
            data:{
                "_token": "{{ csrf_token() }}"
            },
            success: function(r) {
                preloader_none();
                if (r.success) {
                    Swal.fire({
                        width:'300px',
                        icon: 'success',
                        title: 'Success',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 1000);

                    $eu("#cp{{$_idb_}}dGrid").datagrid('reload');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                    $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
                }else{
                    Swal.fire({
                        width:'300px',
                        icon: 'error',
                        title: 'Oops...',
                        text: r.message,
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 3000);
                }
            },
            error:function(){
                preloader_none();
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Request error, please try again.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        })
    }
    function jf{{$_idb_}}dGridConfirm(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal{{$_idb_}}DGridFormConfirm";
            let cpModalUrl = "{{$urlb}}confirm/"+sel.id;
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to CONFIRM',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
</script>

@endpush

@endsection