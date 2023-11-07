@extends('BASE.LayoutAdmin.master')

@section('title')
	MCU Event
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
            <h4 class="d-none d-sm-block">MCU Event</h4>
			<h5 class="d-block d-sm-none">MCU Event</h5>
		@endslot
        @slot('pathHome')
			{{ $base_url }}
		@endslot
		<li class="breadcrumb-item active">MCU Event</li>
	@endcomponent

@endsection

@section('content')

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false" class="p-3">
            <div id="cp{{$_id_}}dGridToolbar">
                <div class="row p-3">
                    <div class="col-12 col-md-8">
                        <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_id_}}dGridAdd()"><i class="fas fa-add fa-lg"></i> Add New</button>
                        <button class="mb-1 btn btn-outline-secondary" onclick="jf{{$_id_}}dGridEdit()"><i class="fas fa-edit fa-lg"></i> Edit</button>
                        <button class="mb-1 btn btn-outline-danger" onclick="jf{{$_id_}}dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button>
                        <button class="mb-1 btn btn-outline-warning" onclick="jf{{$_id_}}dGridConfirm()"><i class="fas fa-file-pen fa-lg"></i> Confirm</button>
                    </div>
                    <div class="col-12 col-md-4">
                        <div style="float:right">
                            <input type="text" id="cp{{$_id_}}dGridFilterSrcEvt_front">
                            <button class="btn btn-outline-primary" onclick="jf{{$_id_}}dGridFilter_o()"><i class="fas fa-filter fa-lg"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </div>
            <table id="cp{{$_id_}}dGrid"></table>
        </div>
        <div data-options="region:'south', border:false, href:'{{$url}}b2Index'" class="p-3" style="height:50%"></div>
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
        $eu("#cp{{$_id_}}dGrid").datagrid({
            toolbar: "#cp{{$_id_}}dGridToolbar",
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
            // url: "{{ $url }}read",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "eventnum",title: "<b>Number</b>",align: "center",width: 100},
                {field: "laboratory_name",title: "<b>Provider</b>",align: "left",width: 200},
                {field: "refer_name",title: "<b>Refer-To (Execute By)</b>",align: "left",width: 200},
                {field: "title",title: "<b>Title</b>",align: "left",width: 250},
                {field: "date_from_dmY_slash",title: "<b>From</b>",align: "center",width: 100},
                {field: "date_to_dmY_slash",title: "<b>To</b>",align: "center",width: 100},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
                {field: "confirm_by",title: "<b>Confirm By</b>",align: "center",width: 100},
                {field: "confirm_at",title: "<b>Confirm At</b>",align: "center",width: 100},
                {field: "confirm_note",title: "<b>Confirm Note</b>",align: "center",width: 200},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp{{$_id_}}dGrid").datagrid('clearSelections');
                $eu("#cp{{$_id_}}dGrid").datagrid('clearChecked');
                jf{{$_id_}}b2dGridFilter_r();
                jf{{$_id_}}b2dGridFilterFront_p();
            },
            onSelect:function(index,row){
                jf{{$_id_}}b2dGridFilter_r();
                jf{{$_id_}}b2dGridFilterFront_p();
            }
        })

        $eu("#cp{{$_id_}}dGridFilterSrcEvt_front").textbox({
            prompt:"Type to search.",
            onChange:function(){
                jf{{$_id_}}dGridFilterFront_p();
            }
        })
        jf{{$_id_}}dGridFilter_i();
    })

    function jf{{$_id_}}dGridFilter_i(){
        let cpModalId = "cpModal{{$_id_}}DGridFilter";
        let cpModalUrl = "{{$url}}filter";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
    }
    function jf{{$_id_}}dGridFilter_o(){
        let cpModalId = "cpModal{{$_id_}}DGridFilter";
        cpGlobalModalOpen(cpModalId);
    }
    function jf{{$_id_}}dGridFilter_g(){
        var srcEvt = $eu("#cp{{$_id_}}dGridFilterSrcEvt").textbox('getValue');
        $eu("#cp{{$_id_}}dGridFilterSrcEvt_front").textbox('setValue',srcEvt);
        var filterParams = $("#cp{{$_id_}}dGridFilterForm").serialize();
        return filterParams;
    }
    function jf{{$_id_}}dGridFilter_p(){
        var filterParams = jf{{$_id_}}dGridFilter_g();
        $eu("#cp{{$_id_}}dGrid").datagrid("load", "{{ $url }}read/?"+filterParams);
        
        let cpModalId = "cpModal{{$_id_}}DGridFilter";
        cpGlobalModalClose(cpModalId);
    }
    function jf{{$_id_}}dGridFilter_r(){
        $eu("#cp{{$_id_}}dGridFilterForm").form('clear');
        // jf{{$_id_}}dGridFilter_p();
    }
    function jf{{$_id_}}dGridFilterFront_p(){
        var srcEvt = $eu("#cp{{$_id_}}dGridFilterSrcEvt_front").textbox('getValue');
        $eu("#cp{{$_id_}}dGridFilterSrcEvt").textbox('setValue',srcEvt);
        jf{{$_id_}}dGridFilter_p();
    }

    function jf{{$_id_}}dGridAdd(){
        let cpModalId = "cpModal{{$_id_}}DGridForm";
        let cpModalUrl = "{{$url}}add";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
    }   
    function jf{{$_id_}}dGridEdit(){
        let sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal{{$_id_}}DGridForm";
            let cpModalUrl = "{{$url}}edit/"+sel.id;
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
    function jf{{$_id_}}dGridDelete(){
        let sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
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
                    jf{{$_id_}}dGridDeleteGo();
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
    function jf{{$_id_}}dGridDeleteGo(){
        let sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
        preloader_block();
        $.ajax({
            type: 'DELETE',
            dataType: "JSON",
            url:"{{ $url }}delete/"+sel.id,
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

                    $eu("#cp{{$_id_}}dGrid").datagrid('reload');
                    $eu("#cp{{$_id_}}dGrid").datagrid('clearSelections');
                    $eu("#cp{{$_id_}}dGrid").datagrid('clearChecked');
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
    function jf{{$_id_}}dGridConfirm(){
        let sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal{{$_id_}}DGridFormConfirm";
            let cpModalUrl = "{{$url}}confirm/"+sel.id;
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