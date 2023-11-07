<div id="cp<?php echo e($_idb_); ?>dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-12">
            <?php if(isset($userScope['roleAction']['/b2/moveSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridMove()"><i class="fas fa-add fa-lg"></i> Move</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/create'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridAdd()"><i class="fas fa-add fa-lg"></i> Add New</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/update'])) { ?>
                <button class="mb-1 btn btn-outline-secondary" onclick="jf<?php echo e($_idb_); ?>dGridEdit()"><i class="fas fa-edit fa-lg"></i> Edit</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/delete'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/sendWAProcess'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridSendWA()"><i class="far fa-paper-plane fa-lg"></i> Send WA</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/presentSave'])) { ?>
                <button class="mb-1 btn btn-outline-warning" onclick="jf<?php echo e($_idb_); ?>dGridPresent()"><i class="far fa-calendar-check fa-lg"></i> Update Status</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/patientExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridExport()"><i class="far fa-file-excel fa-lg"></i> Export</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-8">
            <!-- <select type="text" id="cp<?php echo e($_idb_); ?>dGridItemLv0"></select> -->
            <?php if(isset($userScope['roleAction']['/b2/resultImportSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridUpdateResult()"><i class="fa-solid fa-clipboard-check fa-lg"></i> Update Result</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/resultExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridExportResult()"><i class="far fa-file-excel fa-lg"></i> Export Result</button>
            <?php } ?>
            <?php if(isset($userScope['roleAction']['/b2/resultImportSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridImportResult()"><i class="far fa-file-excel fa-lg"></i> Import Result</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-6">
            <select type="text" id="cp<?php echo e($_idb_); ?>dGridLabelConfCopy" style="width:100px; height:33px">
                <?php for ($i=1; $i < 101; $i++) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/printLabel'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridLabel(4, '110x52-2')"><i class="fas fa-print fa-lg"></i> Print Label</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/printCover'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridPrintCover('PT GAM')"><i class="fas fa-print fa-lg"></i> Print Cover (A)</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/printCover'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridPrintCover('PT MGE')"><i class="fas fa-print fa-lg"></i> Print Cover (B)</button>
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
            pageList: [10, 20, 25, 50, 100],
            pageSize: 100,
            method: "GET",
            idField: "id",
            // url: "<?php echo e($urlb); ?>read",
            frozenColumns: [[
                {field:'cb', checkbox:true},
            ]],
            columns: [[
                {field: "id",title: "<b>ID</b>",align: "center",width: 45},
                {field: "schedule_date_dmY_slash",title: "<b>Schedule</b>",align: "center",width: 100},
                {field: "presentnumFull",title: "<b>Presnt Number</b>",align: "center",width: 150},
                {field: "mcu_format_package_name",title: "<b>Package</b>",align: "center",width: 100},
                {field: "unique",title: "<b>UNIQUE CODE</b>",align: "center",width: 150},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 200},
                {field: "gender",title: "<b>Gender</b>",align: "center",width: 70},
                {field: "dob_dmY_slash",title: "<b>DOB</b>",align: "center",width: 100},
                {field: "ktp",title: "<b>ID (KTP / Passport)</b>",align: "center",width: 150},
                {field: "phone",title: "<b>Phone</b>",align: "center",width: 180},
                {field: "email",title: "<b>Email</b>",align: "center",width: 180},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
                {field: "emp_no",title: "<b>Emp. Number</b>",align: "center",width: 150},
                {field: "emp_area",title: "<b>Job Area</b>",align: "center",width: 150},
                {field: "emp_div",title: "<b>Job Division</b>",align: "center",width: 150},
                {field: "emp_pos",title: "<b>Job Position</b>",align: "center",width: 150},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
                {field: "status_by",title: "<b>Status By</b>",align: "center",width: 100},
                {field: "status_at",title: "<b>Status At</b>",align: "center",width: 100},
                {field: "status_note",title: "<b>Status Note</b>",align: "left",width: 300},
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

        <?php if(!1==1 || isset($userScope['roleAction']['/b2/--showPriceLab'])) { ?>
            $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('hideColumn','price_lab_mod');
        <?php } ?>
        <?php if(!1==1 || isset($userScope['roleAction']['/b2/--showPriceCorporate'])) { ?>
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
        var dGridId = 0;
        if(sel){
            dGridId = sel.id;
        }
        filterParams = filterParams+"&mcu_event_id="+dGridId;

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
    function jf<?php echo e($_idb_); ?>dGridAdd(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
            let cpModalUrl = "<?php echo e($urlb); ?>add/"+sel.id;
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
        }else{
            Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select project first',
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
    function jf<?php echo e($_idb_); ?>dGridLabel(confCopy=4, confSize="110x52-2"){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            if(sel2){
                confCopy = $("#cp<?php echo e($_idb_); ?>dGridLabelConfCopy").val();
                let cpModalUrl = "<?php echo e($urlb); ?>label/"+sel2.id;
                cpModalUrl = cpModalUrl+"?confCopy="+confCopy;
                cpModalUrl = cpModalUrl+"&confSize="+confSize;
                window.open(cpModalUrl)
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select one row to Print Label',
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
    function jf<?php echo e($_idb_); ?>dGridPrintCover(corpName = ""){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            if(sel2.length > 0){
                let idList = [];
                for (let i = 0; i < sel2.length; i++) {
                    idList.push(sel2[i].id);
                }
                preloader_block();
                $.ajax({
                    type: 'POST',
                    dataType: "JSON",
                    url:"<?php echo e($urlb); ?>printCover?corpName="+corpName,
                    data:{
                        "_token": "<?php echo e(csrf_token()); ?>",
                        idList:idList
                    },
                    success: function(r) {
                        preloader_none();
                        if (r.success) {
                            window.open(r.urlDownload);
                        }else{
                            Swal.fire({
                                width:'300px',
                                icon: 'error',
                                title: 'Oops...',
                                text: r.message,
                                showConfirmButton:false
                            })
                            setTimeout(() => { Swal.close(); }, 200);
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
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Checklist to Print Cover',
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
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridUpdateResult(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
                let cpModalUrl = "<?php echo e($urlb); ?>updateResult/"+sel2.id;
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
                text: 'Please select one MCU Project first',
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
</script><?php /**PATH /home/u1598413/public_sys/devapp-mcu/app/Modules/MCU2Onsite/v2Main.blade.php ENDPATH**/ ?>