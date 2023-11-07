<div id="cp<?php echo e($_idb_); ?>dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-6">
            <?php if(isset($userScope['roleAction']['/b3/create'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridAdd()"><i class="fas fa-add fa-lg"></i> Regist MCU</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-6">
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
            pageList: [20],
            pageSize: 20,
            method: "GET",
            idField: "id",
            // url: "<?php echo e($urlb); ?>read",
            checkbox:true,
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "cb", checkbox:true},
                {field: "eventnum",title: "<b>Last Event</b>",align: "center",width: 85},
                {field: "status",title: "<b>Last Status</b>",align: "center",width: 100},
                {field: "status_at",title: "<b>Last Date</b>",align: "center",width: 100},
                {field: "next_mcu_date",title: "<b>Next MCU</b>",align: "center",width: 100},
                // {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 200},
                {field: "emp_no",title: "<b>Emp. Number</b>",align: "center",width: 120},
                {field: "nik",title: "<b>ID (KTP / Passport)</b>",align: "center",width: 140},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 180},
                {field: "gender",title: "<b>Gender</b>",align: "center",width: 65},
                {field: "dob_dmY_slash",title: "<b>DOB</b>",align: "center",width: 100},
                {field: "phone",title: "<b>Phone</b>",align: "center",width: 135},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
                {field: "area",title: "<b>Job Area</b>",align: "left",width: 150},
                {field: "division",title: "<b>Job Division</b>",align: "left",width: 150},
                {field: "position",title: "<b>Job Position</b>",align: "left",width: 150},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.allowRegistMCUColor;
            },
            onLoadSuccess:function(){
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
            },
            onSelect:function(index,row){
                // jf<?php echo e($_idb_); ?>dGridFilter_r();
                // jf<?php echo e($_idb_); ?>dGridFilterFront_p();
            },
            onCheck:function(index,row){
                if(row.allowRegistMCU == 0){
                    setTimeout(() => {
                        $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('uncheckRow',index);
                    }, 100);
                }
            }
        })

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
        var dGridId = 0;
        if(sel){
            dGridId = sel.corporate_id;
        }
        filterParams = filterParams+"&corporate_id="+dGridId;

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

    function jf<?php echo e($_idb_); ?>dGridPatientList(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            // let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
            // let cpModalUrl = "<?php echo e($urlb); ?>add/"+sel.id;
            // cpGlobalModal(cpModalId, cpModalUrl);
            // var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            // cpModal.show();
            $eu("#cp<?php echo e($_id_); ?>tabs").tabs('select',1);
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project to Show Patient List',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }

    function jf<?php echo e($_idb_); ?>dGridAdd(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            if(checked.length > 0){
                let cpModalId = "cpModal<?php echo e($_idb_); ?>dGridForm";
                let cpModalUrl = "<?php echo e($urlb); ?>add/"+sel.id;
                cpGlobalModal(cpModalId, cpModalUrl);
                var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                cpModal.show();
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Checklist Employee to proses.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 2000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Project to Add Patient',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
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
                text: 'DELETE data '+sel.code+', '+sel.title+' ?',
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
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
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
</script><?php /**PATH /home/u1598413/public_sys/dkdmcu_klab__dev/app/Modules/MCUEvent/v3Main.blade.php ENDPATH**/ ?>