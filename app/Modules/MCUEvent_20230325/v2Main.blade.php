<div id="cp{{$_id_}}b2dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-12">
            <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_id_}}b2dGridAdd()"><i class="fas fa-add fa-lg"></i> Add New</button>
            <button class="mb-1 btn btn-outline-secondary" onclick="jf{{$_id_}}b2dGridEdit()"><i class="fas fa-edit fa-lg"></i> Edit</button>
            <button class="mb-1 btn btn-outline-danger" onclick="jf{{$_id_}}b2dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button>
            <button class="mb-1 btn btn-outline-warning" onclick="jf{{$_id_}}b2dGridPresent()"><i class="far fa-calendar-check fa-lg"></i> Present</button>
            <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_id_}}b2dGridImport()"><i class="far fa-file-excel fa-lg"></i> Import</button>
            <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_id_}}b2dGridExport()"><i class="far fa-file-excel fa-lg"></i> Export</button>
        </div>
        <div class="col-12 col-md-8">
            <select type="text" id="cp{{$_id_}}b2dGridItemLv0"></select>
            <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_id_}}b2dGridImportResult()"><i class="far fa-file-excel fa-lg"></i> Import Result</button>
            <button class="mb-1 btn btn-outline-primary" onclick="jf{{$_id_}}b2dGridExportResult()"><i class="far fa-file-excel fa-lg"></i> Export Result</button>
        </div>
        <div class="col-12 col-md-4">
            <div style="float:right">
                <input type="text" id="cp{{$_id_}}b2dGridFilterSrcEvt_front">
                <button class="btn btn-outline-primary" onclick="jf{{$_id_}}b2dGridFilter_o()"><i class="fas fa-filter fa-lg"></i> Filter</button>
            </div>
        </div>
    </div>
</div>
<table id="cp{{$_id_}}b2dGrid"></table>

<script>
    $eu(function(){
        $eu("#cp{{$_id_}}b2dGrid").datagrid({
            toolbar: "#cp{{$_id_}}b2dGridToolbar",
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
            // url: "{{ $url }}b2Read",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "id",title: "<b>ID</b>",align: "center",width: 50},
                {field: "schedule_date_dmY_slash",title: "<b>Schedule</b>",align: "center",width: 100},
                {field: "actual_date_dmY_slash",title: "<b>Actual / Present</b>",align: "center",width: 110},
                {field: "presentnumFull",title: "<b>Presnt Number</b>",align: "center",width: 150},
                {field: "type_name",title: "<b>Patient Type</b>",align: "center",width: 100},
                {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 200},
                {field: "emp_no",title: "<b>Emp. Number</b>",align: "center",width: 150},
                {field: "nik",title: "<b>ID (NIK / Passport)</b>",align: "center",width: 150},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 200},
                {field: "gender",title: "<b>Gender</b>",align: "center",width: 70},
                {field: "dob_dmY_slash",title: "<b>DOB</b>",align: "center",width: 100},
                {field: "phone",title: "<b>Phone</b>",align: "center",width: 150},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
                {field: "area",title: "<b>Job Area</b>",align: "left",width: 150},
                {field: "division",title: "<b>Job Division</b>",align: "left",width: 150},
                {field: "position",title: "<b>Job Position</b>",align: "left",width: 150},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
                {field: "status_by",title: "<b>Status By</b>",align: "center",width: 100},
                {field: "status_at",title: "<b>Status At</b>",align: "center",width: 100},
                {field: "status_note",title: "<b>Status Note</b>",align: "left",width: 200},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp{{$_id_}}b2dGrid").datagrid('clearSelections');
                $eu("#cp{{$_id_}}b2dGrid").datagrid('clearChecked');
            },
            onSelect:function(index,row){
                // jf{{$_id_}}b2dGridFilter_r();
                // jf{{$_id_}}b2dGridFilterFront_p();
            }
        })

        $eu("#cp{{$_id_}}b2dGridFilterSrcEvt_front").textbox({
            prompt:"Type to search.",
            onChange:function(){
                jf{{$_id_}}b2dGridFilterFront_p();
            }
        })
        jf{{$_id_}}b2dGridFilter_i();
    })

    function jf{{$_id_}}b2dGridFilter_i(){
        let cpModalId = "cpModal{{$_id_}}b2DGridFilter";
        let cpModalUrl = "{{$url}}b2Filter";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
    }
    function jf{{$_id_}}b2dGridFilter_o(){
        let cpModalId = "cpModal{{$_id_}}b2DGridFilter";
        cpGlobalModalOpen(cpModalId);
    }
    function jf{{$_id_}}b2dGridFilter_g(){
        var srcEvt = $eu("#cp{{$_id_}}b2dGridFilterSrcEvt").textbox('getValue');
        $eu("#cp{{$_id_}}b2dGridFilterSrcEvt_front").textbox('setValue',srcEvt);
        var filterParams = $("#cp{{$_id_}}b2dGridFilterForm").serialize();
        
        var sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
        var dGridId = 0;
        if(sel){
            dGridId = sel.id;
        }
        filterParams = filterParams+"&mcu_event_id="+dGridId;

        return filterParams;
    }
    function jf{{$_id_}}b2dGridFilter_p(){
        var filterParams = jf{{$_id_}}b2dGridFilter_g();
        $eu("#cp{{$_id_}}b2dGrid").datagrid("load", "{{ $url }}b2Read/?"+filterParams);
        
        let cpModalId = "cpModal{{$_id_}}b2DGridFilter";
        cpGlobalModalClose(cpModalId);
    }
    function jf{{$_id_}}b2dGridFilter_r(){
        $eu("#cp{{$_id_}}b2dGridFilterForm").form('clear');
        // jf{{$_id_}}b2dGridFilter_p();
    }
    function jf{{$_id_}}b2dGridFilterFront_p(){
        var srcEvt = $eu("#cp{{$_id_}}b2dGridFilterSrcEvt_front").textbox('getValue');
        $eu("#cp{{$_id_}}b2dGridFilterSrcEvt").textbox('setValue',srcEvt);
        jf{{$_id_}}b2dGridFilter_p();
    }

    function jf{{$_id_}}b2dGridAdd(){
        let sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal{{$_id_}}b2DGridForm";
            let cpModalUrl = "{{$url}}b2Add/"+sel.id;
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Event to Add Patient',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }   
    function jf{{$_id_}}b2dGridEdit(){
        let sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp{{$_id_}}b2dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal{{$_id_}}b2DGridForm";
                let cpModalUrl = "{{$url}}b2Edit/"+sel2.id;
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
                text: 'Please select one MCU Event first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf{{$_id_}}b2dGridDelete(){
        let sel = $eu("#cp{{$_id_}}b2dGrid").datagrid('getSelected');
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
                    jf{{$_id_}}b2dGridDeleteGo();
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
    function jf{{$_id_}}b2dGridDeleteGo(){
        let sel = $eu("#cp{{$_id_}}b2dGrid").datagrid('getSelected');
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

                    $eu("#cp{{$_id_}}b2dGrid").datagrid('reload');
                    $eu("#cp{{$_id_}}b2dGrid").datagrid('clearSelections');
                    $eu("#cp{{$_id_}}b2dGrid").datagrid('clearChecked');
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
    function jf{{$_id_}}b2dGridPresent(){
        let sel = $eu("#cp{{$_id_}}dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp{{$_id_}}b2dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal{{$_id_}}b2DGridForm";
                let cpModalUrl = "{{$url}}b2Present/"+sel2.id;
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
                text: 'Please select one MCU Event first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
</script>