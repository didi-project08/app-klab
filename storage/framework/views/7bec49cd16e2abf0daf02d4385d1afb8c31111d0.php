<div id="cp<?php echo e($_idb_); ?>dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-12">
            <?php if(isset($userScope['roleAction']['/b2/userClientUpdate'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridUserClientUpdate()"><i class="fas fa-add fa-fw"></i> Save</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-6 d-none">
            <div style="float:right">
                <input type="text" id="cp<?php echo e($_idb_); ?>dGridFilterSrcEvt_front">
                <button class="btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridFilter_o()"><i class="fas fa-filter fa-fw"></i> Filter</button>
            </div>
        </div>
    </div>
</div>
<table id="cp<?php echo e($_idb_); ?>dGrid"></table>

<script>
    $eu(function(){
        $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid({
            toolbar: "#cp<?php echo e($_idb_); ?>dGridToolbar",
            fit:true,
            pagination:false,
            rownumbers: true,
            checkbox: true,
            method: "GET",
            idField: "id",
            // url: "<?php echo e($urlb); ?>read",
            columns: [[
                {field:'cb', checkbox:true},
                {field: "title",title: "<b>Client</b>",align: "left",width: 200},
                {field: "prov",title: "<b>Province</b>",align: "center",width: 130},
                {field: "city",title: "<b>City</b>",align: "center",width: 130},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
            ]],
            onLoadSuccess:function(data){
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid("uncheckAll");
                var rowsChecked = data.rowsChecked;
                for (let i = 0; i < rowsChecked.length; i++) {
                    var rowIndex = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid("getRowIndex", rowsChecked[i].id);
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid("checkRow", rowIndex);
                }
            },
            onSelect:function(row){
                // var root = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getRoot');
                // var roots = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getRoots');
                // var parent = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getParent', row.id);
                // console.log(root);
                // console.log(roots);
                // console.log(parent);
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
        var dGridId2 = 0;
        if(sel){
            dGridId = sel.corporate_id;
            dGridId2 = sel.f_user_id;
        }
        filterParams = filterParams+"&corporate_id="+dGridId+"&f_user_id="+dGridId2;

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

    function jf<?php echo e($_idb_); ?>dGridUserClientUpdate(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let checkedData = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            if(checkedData.length > 0){
                let corpClientIdList = [];
                for (let i = 0; i < checkedData.length; i++) {
                    corpClientIdList.push(checkedData[i].id);
                }

                preloader_block();
                $.ajax({
                    type: 'POST',
                    dataType: "JSON",
                    url:"<?php echo e($urlb); ?>userClientUpdate/"+sel.f_user_id+"/"+sel.corporate_id,
                    data:{
                        _token: "<?php echo e(csrf_token()); ?>",
                        corpClientIdList:corpClientIdList
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
                            setTimeout(() => { Swal.close(); }, 2000);
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
                        setTimeout(() => { Swal.close(); }, 2000);
                    }
                })
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please some client first.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 2000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select User first.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }   

    function jf<?php echo e($_idb_); ?>dGridEdit(){
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
            let cpModalUrl = "<?php echo e($urlb); ?>edit/"+sel.id;
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
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }

    function jf<?php echo e($_idb_); ?>dGridDelete(){
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
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
            setTimeout(() => { Swal.close(); }, 2000);
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
                    setTimeout(() => { Swal.close(); }, 2000);
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
                setTimeout(() => { Swal.close(); }, 2000);
            }
        })
    }
</script><?php /**PATH /home/u1598413/public_sys/dkdmcu/app/Modules/UserCorporate/v2Main.blade.php ENDPATH**/ ?>