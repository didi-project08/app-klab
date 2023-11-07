<div id="cp{{$_idb_}}dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-12">
            <?php if(isset($userScope['roleAction']['/b2/moveSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridMove()"><i class="fas fa-add fa-lg"></i> Move</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/update'])) { ?>
                <button class="mb-1 btn btn-outline-secondary" onclick="jf{{$_idb_}}dGridEdit()"><i class="fas fa-edit fa-lg"></i> Edit</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/delete'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf{{$_idb_}}dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/sendWAProcess'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridSendWA()"><i class="far fa-paper-plane fa-lg"></i> Send WA</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/presentSave'])) { ?>
                <button class="mb-1 btn btn-outline-warning" onclick="jf{{$_idb_}}dGridPresent()"><i class="far fa-calendar-check fa-lg"></i> Present / Cancel</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/patientExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridExport()"><i class="far fa-file-excel fa-lg"></i> Export Patient</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-8">
            <select type="text" id="cp{{$_idb_}}dGridItemLv0"></select>
            <?php if(isset($userScope['roleAction']['/b2/resultImportSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridUpdateResult()"><i class="fa-solid fa-clipboard-check fa-lg"></i> Update Result</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/resultExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridExportResult()"><i class="far fa-file-excel fa-lg"></i> Export Result</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/resultImportSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_idb_}}dGridImportResult()"><i class="far fa-file-excel fa-lg"></i> Import Result</button>
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
                {field:'cb', checkbox:true},
            ]],
            columns: [[
                {field: "id",title: "<b>ID</b>",align: "center",width: 50},
                {field: "schedule_date_dmY_slash",title: "<b>Schedule</b>",align: "center",width: 100},
                {field: "actual_date_dmY_slash",title: "<b>Actual / Present</b>",align: "center",width: 110},
                {field: "presentnumFull",title: "<b>Presnt Number</b>",align: "center",width: 150},
                // {field: "type_name",title: "<b>Patient Type</b>",align: "center",width: 100},
                // {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 200},
                {field: "mcu_format_package_name",title: "<b>Package</b>",align: "center",width: 120},
                {field: "emp_no",title: "<b>Emp. Number</b>",align: "center",width: 150},
                {field: "nik",title: "<b>ID (KTP / Passport)</b>",align: "center",width: 150},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 200},
                {field: "gender",title: "<b>Gender</b>",align: "center",width: 70},
                {field: "dob_dmY_slash",title: "<b>DOB</b>",align: "center",width: 100},
                {field: "phone",title: "<b>Phone</b>",align: "center",width: 150},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
                {field: "area",title: "<b>Job Area</b>",align: "left",width: 150},
                {field: "division",title: "<b>Job Division</b>",align: "left",width: 150},
                {field: "position",title: "<b>Job Position</b>",align: "left",width: 150},
                {field: "price_lab_mod",title: "<b>Price (Provider)</b>",align: "right",width: 120},
                {field: "price_corporate_mod",title: "<b>Price (Corporate)</b>",align: "right",width: 120},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
                {field: "status_by",title: "<b>Status By</b>",align: "center",width: 100},
                {field: "status_at",title: "<b>Status At</b>",align: "center",width: 100},
                {field: "status_note",title: "<b>Status Note</b>",align: "left",width: 200},
                {field: "fit_cate",title: "<b>Fit Category</b>",align: "left",width: 160},
                {field: "fit_note",title: "<b>Fit Note</b>",align: "left",width: 200},
                {field: "fit_by",title: "<b>Result By</b>",align: "center",width: 100},
                {field: "fit_at",title: "<b>Result At</b>",align: "center",width: 100},
                {field: "wa_by",title: "<b>WA By</b>",align: "center",width: 100},
                {field: "wa_at",title: "<b>WA At</b>",align: "center",width: 100},
                {field: "wa_status_message",title: "<b>WA Status</b>",align: "left",width: 250},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp{{$_idb_}}dGrid").datagrid('clearSelections');
                $eu("#cp{{$_idb_}}dGrid").datagrid('clearChecked');
            },
            onSelect:function(index,row){
                // jf{{$_idb_}}dGridFilter_r();
                // jf{{$_idb_}}dGridFilterFront_p();
            }
        })

        <?php if(!isset($userScope['roleAction']['/b2/--showPriceLab'])) { ?>
            $eu("#cp{{$_idb_}}dGrid").datagrid('hideColumn','price_lab_mod');
        <?php } ?>
        <?php if(!isset($userScope['roleAction']['/b2/--showPriceCorporate'])) { ?>
            $eu("#cp{{$_idb_}}dGrid").datagrid('hideColumn','price_corporate_mod');
        <?php } ?>

        $eu("#cp{{$_idb_}}dGridItemLv0").combobox({
            prompt:"Select MCU Result",
            width:236,
            panelHeight:'auto',
            valueField:"id",
            textField:"title",
            data:[
                {id:-1, title:"FIT Result", selected:true}
            ]
        });

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
        
        var sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        var dGridId = 0;
        if(sel){
            dGridId = sel.id;
        }
        filterParams = filterParams+"&mcu_event_id="+dGridId;

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

    function jf{{$_idb_}}dGridAdd(){
        let sel = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
        if(sel){
            // let cpModalId = "cpModal{{$_idb_}}DGridForm";
            // let cpModalUrl = "{{$urlb}}add/"+sel.id;
            // cpGlobalModal(cpModalId, cpModalUrl);
            // var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            // cpModal.show();
            $eu("#cp{{$_id_}}tabs").tabs('select',0);
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project to Add Patient',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }   
    function jf{{$_idb_}}dGridEdit(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal{{$_idb_}}DGridForm";
                let cpModalUrl = "{{$urlb}}edit/"+sel2.id;
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
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridDelete(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
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
                text: 'DELETE data '+sel.name+' ?',
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
    function jf{{$_idb_}}dGridPresent(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal{{$_idb_}}DGridForm";
                let cpModalUrl = "{{$urlb}}present/"+sel2.id;
                cpGlobalModal(cpModalId, cpModalUrl);
                var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                cpModal.show();
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select one row to PRESENT',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridUpdateResult(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp{{$_idb_}}dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal{{$_idb_}}DGridForm";
                let cpModalUrl = "{{$urlb}}updateResult/"+sel2.id;
                cpGlobalModal(cpModalId, cpModalUrl);
                var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                cpModal.show();
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select one row to UPDATE RESULT',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridExport(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            var filterParams = jf{{$_idb_}}dGridFilter_g();
            window.open("{{ $urlb }}patientExport/?"+filterParams);
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridExportResult(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            var resultCate = $eu("#cp{{$_idb_}}dGridItemLv0").combobox('getValue');
            var filterParams = jf{{$_idb_}}dGridFilter_g();
            filterParams = filterParams+"&resultCate="+resultCate;
            window.open("{{ $urlb }}resultExport/?"+filterParams);
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridImportResult(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal{{$_idb_}}DGridFormResultImport";
            let cpModalUrl = "{{$urlb}}resultImport/"+sel.id;
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_idb_}}dGridMove(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            let checked = $eu("#cp{{$_idb_}}dGrid").datagrid('getChecked');
            if(checked.length > 0){
                let cpModalId = "cpModal{{$_idb_}}dGridFormMove";
                let cpModalUrl = "{{$urlb}}move/"+sel.id;
                cpGlobalModal(cpModalId, cpModalUrl);
                var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                cpModal.show();
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please checklist some patient to move.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 2000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }
    function jf{{$_idb_}}dGridSendWA(){
        let sel = $eu("#cp{{$_id_}}b1dGrid").datagrid('getSelected');
        if(sel){
            let checked = $eu("#cp{{$_idb_}}dGrid").datagrid('getChecked');
            if(checked.length > 0){
                let cpModalId = "cpModal{{$_idb_}}dGridFormWA";
                let cpModalUrl = "{{$urlb}}sendWA/"+sel.id;
                cpGlobalModal(cpModalId, cpModalUrl);
                var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                cpModal.show();
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please checklist some patient to Send WA.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 2000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }
</script>