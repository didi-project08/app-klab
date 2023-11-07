<div id="cp<?php echo e($_idb_); ?>dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-4">
            <?php if(isset($userScope['roleAction']['/b2/patientExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridExport()"><i class="far fa-file-excel fa-lg"></i> Export Patient</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-8">
            <div style="float:right">
                <input type="text" id="cp<?php echo e($_idb_); ?>dGridFilterSrcEvt_front">
                <button class="btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridFilter_o()"><i class="fas fa-filter fa-lg"></i> Filter</button>
            </div>
        </div>
    </div>
</div>
<table id="cp<?php echo e($_idb_); ?>dGrid"></table>

<script>
    $eu(function(){
        $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid({
            toolbar: "#cp<?php echo e($_idb_); ?>dGridToolbar",
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
            // url: "<?php echo e($urlb); ?>read",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field:'cb', checkbox:true},
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
                {field: "price_lab_mod",title: "<b>Price (Provider)</b>",align: "right",width: 100},
                {field: "price_corporate_mod",title: "<b>Price (Corporate)</b>",align: "right",width: 100},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
                {field: "status_by",title: "<b>Status By</b>",align: "center",width: 100},
                {field: "status_at",title: "<b>Status At</b>",align: "center",width: 100},
                {field: "status_note",title: "<b>Status Note</b>",align: "left",width: 200},
                {field: "fit_cate",title: "<b>Fit Status</b>",align: "left",width: 160},
                {field: "fit_note",title: "<b>Fit Note</b>",align: "left",width: 200},
                {field: "fit_by",title: "<b>Result By</b>",align: "center",width: 100},
                {field: "fit_at",title: "<b>Result At</b>",align: "center",width: 100},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
            },
            onSelect:function(index,row){
                // jf<?php echo e($_idb_); ?>dGridFilter_r();
                // jf<?php echo e($_idb_); ?>dGridFilterFront_p();
            }
        })

        <?php if(!isset($userScope['roleAction']['/b1/--showPriceLab'])) { ?>
            $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('hideColumn','price_lab_mod');
        <?php } ?>
        <?php if(!isset($userScope['roleAction']['/b1/--showPriceCorporate'])) { ?>
            $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('hideColumn','price_corporate_mod');
        <?php } ?>

        $eu("#cp<?php echo e($_idb_); ?>dGridItemLv0").combobox({
            prompt:"Select MCU Result",
            width:236,
            panelHeight:'auto',
            valueField:"id",
            textField:"title",
            data:[
                {id:-1, title:"FIT Result", selected:true}
            ]
        });

        $eu("#cp<?php echo e($_idb_); ?>dGridFilterSrcEvt_front").textbox({
            prompt:"Type to search.",
            onChange:function(){
                jf<?php echo e($_idb_); ?>dGridFilterFront_p();
            }
        })
        jf<?php echo e($_idb_); ?>dGridFilter_i();
    })

    function jf<?php echo e($_idb_); ?>dGridFilter_i(){
        let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridFilter";
        let cpModalUrl = "<?php echo e($urlb); ?>filter";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
    }
    function jf<?php echo e($_idb_); ?>dGridFilter_o(){
        let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridFilter";
        cpGlobalModalOpen(cpModalId);
    }
    function jf<?php echo e($_idb_); ?>dGridFilter_g(){
        var srcEvt = $eu("#cp<?php echo e($_idb_); ?>dGridFilterSrcEvt").textbox('getValue');
        $eu("#cp<?php echo e($_idb_); ?>dGridFilterSrcEvt_front").textbox('setValue',srcEvt);
        var filterParams = $("#cp<?php echo e($_idb_); ?>dGridFilterForm").serialize();
        
        var sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        var dGridId = -9;
        if(sel){
            dGridId = sel.id;
        }
        filterParams = filterParams+"&mcu_invoice_id="+dGridId;

        return filterParams;
    }
    function jf<?php echo e($_idb_); ?>dGridFilter_p(){
        var filterParams = jf<?php echo e($_idb_); ?>dGridFilter_g();
        $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid("load", "<?php echo e($urlb); ?>read/?"+filterParams);
        
        let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridFilter";
        cpGlobalModalClose(cpModalId);
    }
    function jf<?php echo e($_idb_); ?>dGridFilter_r(){
        $eu("#cp<?php echo e($_idb_); ?>dGridFilterForm").form('clear');
        // jf<?php echo e($_idb_); ?>dGridFilter_p();
    }
    function jf<?php echo e($_idb_); ?>dGridFilterFront_p(){
        var srcEvt = $eu("#cp<?php echo e($_idb_); ?>dGridFilterSrcEvt_front").textbox('getValue');
        $eu("#cp<?php echo e($_idb_); ?>dGridFilterSrcEvt").textbox('setValue',srcEvt);
        jf<?php echo e($_idb_); ?>dGridFilter_p();
    }

    function jf<?php echo e($_idb_); ?>dGridAdd(){
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
        if(sel){
            // let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
            // let cpModalUrl = "<?php echo e($urlb); ?>add/"+sel.id;
            // cpGlobalModal(cpModalId, cpModalUrl);
            // var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            // cpModal.show();
            $eu("#cp<?php echo e($_id_); ?>tabs").tabs('select',0);
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
    function jf<?php echo e($_idb_); ?>dGridEdit(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
                let cpModalUrl = "<?php echo e($urlb); ?>edit/"+sel2.id;
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
    function jf<?php echo e($_idb_); ?>dGridDelete(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
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
                    jf<?php echo e($_idb_); ?>dGridDeleteGo();
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
    function jf<?php echo e($_idb_); ?>dGridDeleteGo(){
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
        preloader_block();
        $.ajax({
            type: 'DELETE',
            dataType: "JSON",
            url:"<?php echo e($urlb); ?>delete/"+sel.id,
            data:{
                "_token": "<?php echo e(csrf_token()); ?>"
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

                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('reload');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
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
    function jf<?php echo e($_idb_); ?>dGridPresent(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
                let cpModalUrl = "<?php echo e($urlb); ?>present/"+sel2.id;
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
    function jf<?php echo e($_idb_); ?>dGridExport(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            var filterParams = jf<?php echo e($_idb_); ?>dGridFilter_g();
            window.open("<?php echo e($urlb); ?>patientExport/?"+filterParams);
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select Invoice first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridExportResult(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            var resultCate = $eu("#cp<?php echo e($_idb_); ?>dGridItemLv0").combobox('getValue');
            var filterParams = jf<?php echo e($_idb_); ?>dGridFilter_g();
            filterParams = filterParams+"&resultCate="+resultCate;
            window.open("<?php echo e($urlb); ?>resultExport/?"+filterParams);
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
    function jf<?php echo e($_idb_); ?>dGridImportResult(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridFormResultImport";
            let cpModalUrl = "<?php echo e($urlb); ?>resultImport/"+sel.id;
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
    function jf<?php echo e($_idb_); ?>dGridMove(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            if(checked.length > 0){
                let cpModalId = "cpModal<?php echo e($_idb_); ?>dGridFormMove";
                let cpModalUrl = "<?php echo e($urlb); ?>move/"+sel.id;
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
    function jf<?php echo e($_idb_); ?>dGridSendWA(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            if(checked.length > 0){
                let cpModalId = "cpModal<?php echo e($_idb_); ?>dGridFormWA";
                let cpModalUrl = "<?php echo e($urlb); ?>sendWA/"+sel.id;
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
</script><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/MCUInvoice/v2Main.blade.php ENDPATH**/ ?>