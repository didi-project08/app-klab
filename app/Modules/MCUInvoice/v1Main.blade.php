<div id="cp{{$_idb_}}dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-8">
            <?php if(isset($userScope['roleAction']['/b1/generate'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridGenerate()"><i class="fas fa-add fa-lg"></i> Generate</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b1/delete'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf{{$_idb_}}dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b1/approve'])) { ?>
                <button class="mb-1 btn btn-outline-success" onclick="jf{{$_idb_}}dGridApprove()"><i class="fas fa-file-pen fa-lg"></i> Approve</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b1/export']) && 1==2) { ?>
                <button class="mb-1 btn btn-outline-success" onclick="jf{{$_idb_}}dGridExport()"><i class="fas fa-file-excel fa-lg"></i> Export</button>
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
                // {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 150},
                // {field: "corporate_client_name",title: "<b>Client</b>",align: "left",width: 150},
                {field: "id",title: "<b>ID</b>",align: "center",width: 70},
                {field: "corporateClientName",title: "<b>Corporate</b>",align: "left",width: 300},
                {field: "date_dmY_slash",title: "<b>Invoice</b>",align: "center",width: 100},
                {field: "date_from_dmY_slash",title: "<b>Trans. From</b>",align: "center",width: 100},
                {field: "date_to_dmY_slash",title: "<b>Trans. To</b>",align: "center",width: 100},
                {field: "patient_total_mod",title: "<b>Patient</b>",align: "center",width: 100},
                {field: "price_lab_total_mod",title: "<b>Price (Provider)</b>",align: "right",width: 120},
                {field: "price_corporate_total_mod",title: "<b>Price (Corporate)</b>",align: "right",width: 120},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
                if(typeof jf{{$_id_}}b2dGridFilter_r === "function"){
                    jf{{$_id_}}b2dGridFilter_r();
                }
                if(typeof jf{{$_id_}}b2dGridFilter_p === "function"){
                    jf{{$_id_}}b2dGridFilter_p();
                }
            },
            onSelect:function(index,row){
                if(typeof jf{{$_id_}}b2dGridFilter_r === "function"){
                    jf{{$_id_}}b2dGridFilter_r();
                }
                if(typeof jf{{$_id_}}b2dGridFilter_p === "function"){
                    jf{{$_id_}}b2dGridFilter_p();
                }
            }
        })

        <?php if(!isset($userScope['roleAction']['/b1/--showPriceLab'])) { ?>
            $eu("#cp{{$_idb_}}dGrid").datagrid('hideColumn','price_lab_total_mod');
        <?php } ?>
        <?php if(!isset($userScope['roleAction']['/b1/--showPriceCorporate'])) { ?>
            $eu("#cp{{$_idb_}}dGrid").datagrid('hideColumn','price_corporate_total_mod');
        <?php } ?>

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
        $eu("#cp{{$_idb_}}dGrid").datagrid("load", "{{ $urlb }}read/?"+filterParams);
        
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

    function jf{{$_idb_}}dGridGenerate(){
        preloader_block();
        $.ajax({
            type: 'POST',
            dataType: "JSON",
            url:"{{ $urlb }}generate",
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
                text: 'DELETE Invoice ID '+sel.id+' ?',
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

    function jf{{$_idb_}}dGridApprove(){
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
                text: 'APPROVE Invoice ID '+sel.id+' ?',
                showCancelButton:true,
                showConfirmButton:true
            }).then((result) => {
                if(result.isConfirmed){
                    jf{{$_idb_}}dGridApproveGo();
                }
            })
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to APPROVE',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridApproveGo(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        preloader_block();
        $.ajax({
            type: 'PUT',
            dataType: "JSON",
            url:"{{ $urlb }}approve/"+sel.id,
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