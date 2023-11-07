@extends('BASE.LayoutAdmin.master')

@section('title')
	Corporate
@endsection

@push('css')
@endpush

@section('headerBreadcrumb')

	@component('BASE.LayoutAdmin.breadcrumb')
		@slot('breadcrumb_title')
            <h4 class="d-none d-sm-block">Corporate</h4>
			<h5 class="d-block d-sm-none">Corporate</h5>
		@endslot
        @slot('pathHome')
			{{ $base_url }}
		@endslot
		<li class="breadcrumb-item active">Corporate</li>
	@endcomponent

@endsection

@section('content')

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false" class="p-3">
            <div id="cp{{$_id_}}dGridToolbar">
                <div class="row p-3">
                    <div class="col-12">
                        <?php if(isset($userScope['roleAction']['/create'])) { ?>
                            <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_id_}}dGridAdd()"><i class="fas fa-add fa-fw"></i> Add New</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/update'])) { ?>
                            <button class="mb-1 btn btn-outline-secondary" onclick="jf{{$_id_}}dGridEdit()"><i class="fas fa-edit fa-fw"></i> Edit</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/delete'])) { ?>
                            <button class="mb-1 btn btn-outline-danger" onclick="jf{{$_id_}}dGridDelete()"><i class="fas fa-trash fa-fw"></i> Delete</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/export'])) { ?>
                            <button class="mb-1 btn btn-outline-success" onclick="jf{{$_id_}}dGridExport()"><i class="fas fa-file-excel fa-fw"></i> Export</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/importSave'])) { ?>
                            <button class="mb-1 btn btn-outline-success" onclick="jf{{$_id_}}dGridImport()"><i class="fas fa-file-excel fa-fw"></i> Import</button>
                        <?php } ?>
                    </div>
                    <div class="col-12">
                        <div style="float:right">
                            <input type="text" id="cp{{$_id_}}dGridFilterSrcEvt_front">
                            <button class="btn btn-outline-primary" onclick="jf{{$_id_}}dGridFilter_o()"><i class="fas fa-filter fa-fw"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </div>
            <table id="cp{{$_id_}}dGrid"></table>
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
                {field: "code",title: "<b>Code</b>",align: "center",width: 100},
                {field: "title",title: "<b>Name</b>",align: "left",width: 200},
                {field: "prov",title: "<b>Province</b>",align: "center",width: 150},
                {field: "city",title: "<b>City</b>",align: "center",width: 150},
                {field: "address",title: "<b>Address</b>",align: "left",width: 300},
                {field: "pic_name",title: "<b>PIC Name</b>",align: "left",width: 150},
                {field: "pic_phone",title: "<b>PIC Phone (WA)</b>",align: "left",width: 150},
            ]],
            rowStyler: function(index, row){
                return 'color:black;';
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

    function jf{{$_id_}}dGridExport(){
        var filterParams = jf{{$_id_}}dGridFilter_g();
        window.open("{{ $url }}export/?"+filterParams);
    }

    function jf{{$_id_}}dGridImport(){
        let cpModalId = "cpModal{{$_id_}}DGridForm";
        let cpModalUrl = "{{$url}}import";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
    }
</script>
@endpush

@endsection