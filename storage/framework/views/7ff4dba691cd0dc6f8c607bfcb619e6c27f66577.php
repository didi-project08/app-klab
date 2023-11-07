<div id="cp<?php echo e($_idb_); ?>dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-8 mb-1">
            <?php if(isset($userScope['roleAction']['/b2/moveSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridMove()"><i class="fas fa-add fa-lg"></i> Move</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/create'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridAdd()"><i class="fas fa-add fa-lg"></i> Add</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/update'])) { ?>
                <button class="mb-1 btn btn-outline-secondary" onclick="jf<?php echo e($_idb_); ?>dGridEdit()"><i class="fas fa-edit fa-lg"></i> Edit</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/delete'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/patientImport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridImport()"><i class="far fa-file-excel fa-lg"></i> Imp. Patient</button>
            <?php } ?>
            |
            <?php if(1==1 || isset($userScope['roleAction']['/b2/patientExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridExport()"><i class="far fa-file-excel fa-lg"></i> Exp. Patient</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/patientExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridImportUpdate()"><i class="far fa-file-excel fa-lg"></i> Imp. Update Patient</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-4 mb-1">
            <div style="float:right">
                <input type="text" id="cp<?php echo e($_idb_); ?>dGridFilterSrcEvt_front">
                <button class="btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridFilter_o()"><i class="fas fa-filter fa-lg"></i> Filter</button>
            </div>
        </div>
        <div class="col-12 col-md-12 mb-1">
            <?php if(isset($userScope['roleAction']['/b2/sendWAProcess'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridSendWA()"><i class="far fa-paper-plane fa-lg"></i> Send WA</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/presentSave'])) { ?>
                <button class="mb-1 btn btn-outline-warning" onclick="jf<?php echo e($_idb_); ?>dGridPresent()"><i class="far fa-calendar-check fa-lg"></i> Present</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/updateStatus'])) { ?>
                <button class="mb-1 btn btn-outline-warning" onclick="jf<?php echo e($_idb_); ?>dGridUpdateStatus()"><i class="far fa-calendar-check fa-lg"></i> Upd. Status</button>
            <?php } ?>
            |
            <?php if(1==1 || isset($userScope['roleAction']['/b2/Label'])) { ?>
                <select type="text" id="cp<?php echo e($_idb_); ?>dGridLabelConfCopy" style="width:100px; height:33px">
                    <?php for ($i=1; $i < 101; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                </select>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridLabel('present_numFull')"><i class="fas fa-table fa-lg"></i> Label</button>
            <?php } ?>
            <?php if(1==2 || isset($userScope['roleAction']['/b2/CardControl'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridCardControl()"><i class="fas fa-list-check fa-lg"></i> C. Control</button>
            <?php } ?>
            <?php if(1==2 || isset($userScope['roleAction']['/b2/conclusionGenerate'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridGenerateConclusion()"><i class="fas fa-refresh fa-lg"></i> Re-Generate Conclusion</button>
            <?php } ?>
            <?php if(1==2 || isset($userScope['roleAction']['/b2/verify'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridVerify()"><i class="fas fa-check-double fa-lg"></i> Verify</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-12">
            <select type="text" id="cp<?php echo e($_idb_); ?>dGridItemLv0"></select>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/resultImportSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridUpdateResult()"><i class="fa-solid fa-clipboard-check fa-lg"></i> Upd. Result</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/resultExport'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridExportResult()"><i class="far fa-file-excel fa-lg"></i> Exp. Result</button>
            <?php } ?>
            <?php if(1==1 || isset($userScope['roleAction']['/b2/resultImportSave'])) { ?>
                <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridImportResult()"><i class="far fa-file-excel fa-lg"></i> Imp. Result</button>
            <?php } ?>
            |
            <?php if(1==1 || isset($userScope['roleAction']['/b2/resultPrint'])) { ?>
                <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridPrintResult()"><i class="far fa-file-pdf fa-lg"></i> Print</button>
            <?php } ?>
        </div>
        <div class="col-12 col-md-12">
            <?php if(1 == 2) { ?>
                
                <?php if(1==1 || isset($userScope['roleAction']['/b2/printLabel'])) { ?>
                    <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridLabel(4, '110x52-2')"><i class="fas fa-print fa-lg"></i> Print Label</button>
                <?php } ?>
                <?php if(1==1 || isset($userScope['roleAction']['/b2/printCover'])) { ?>
                    <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridPrintCover('PT GAM')"><i class="fas fa-print fa-lg"></i> Print Cover (A)</button>
                <?php } ?>
                <?php if(1==1 || isset($userScope['roleAction']['/b2/printCover'])) { ?>
                    <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridPrintCover('PT MGE')"><i class="fas fa-print fa-lg"></i> Print Cover (B)</button>
                <?php } ?>
            <?php } ?>
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
                {field: "verify",title: "<b>VERIFIED</b>",align: "center",width: 80},
                {field: "schedule_date_dmY_slash",title: "<b>Schedule</b>",align: "center",width: 90},
                {field: "present_at",title: "<b>Present</b>",align: "center",width: 90},
                {field: "present_numFull",title: "<b>Present Number</b>",align: "center",width: 140},
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
                {field: "reg_numFull",title: "<b>Reg. Number</b>",align: "center",width: 140},
                {field: "status_name",title: "<b>Status</b>",align: "center",width: 100},
                {field: "status_by",title: "<b>Status By</b>",align: "center",width: 100},
                {field: "status_at",title: "<b>Status At</b>",align: "center",width: 100},
                {field: "status_note",title: "<b>Status Note</b>",align: "left",width: 300},
                {field: "verify_by",title: "<b>Verify By</b>",align: "center",width: 100},
                {field: "verify_at",title: "<b>Verify At</b>",align: "center",width: 100},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
            },
            onSelect:function(index,row){
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked')
            },
            onCheck:function(){
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections')
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
            textField:"name",
            method: "GET",
            data:[
                // {id:-1, title:"FIT Result", selected:true}
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

    function jf<?php echo e($_idb_); ?>dGridItemLv0(){
        var sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            $eu("#cp<?php echo e($_idb_); ?>dGridItemLv0").combobox({
                url:"<?php echo e($urlb); ?>mcuItemLv0Combo/"+sel.mcu2_format_id+"?selectFirst=1"
            })
        }
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
                text: 'Please select one MCU Event to Add Patient',
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
                text: 'Please select one MCU Event first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridDelete(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
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
                text: 'Please select one MCU Event first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridUpdateStatus(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            if(sel2){
                let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
                let cpModalUrl = "<?php echo e($urlb); ?>updateStatus/"+sel2.id;
                cpGlobalModal(cpModalId, cpModalUrl);
                var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                cpModal.show();
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select one row to UPDATE STATUS',
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
    function jf<?php echo e($_idb_); ?>dGridLabel(confField="id", confSize="110x52-2"){
        let selEvent = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(selEvent){
            let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            
            var patientIdList = [];
            if(sel){
                patientIdList.push(sel.id);
            }
            if(checked.length > 0){
                for (let i = 0; i < checked.length; i++) {
                    const sel = checked[i];
                    patientIdList.push(sel.id);
                }
            }
            if(patientIdList.length > 0){
                var confEventId = selEvent.id;
                var confCopy = $("#cp<?php echo e($_idb_); ?>dGridLabelConfCopy").val();
                jf<?php echo e($_idb_); ?>dGridLabelGo(confField, confSize, confCopy, confEventId, patientIdList);
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select some Patinet first',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Event.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridLabelGo(confField, confSize, confCopy, confEventId, patientIdList){
        if(patientIdList.length > 0){
            let cpModalUrl = "<?php echo e($urlb); ?>label";
            cpModalUrl = cpModalUrl+"?confField="+confField;
            cpModalUrl = cpModalUrl+"&confSize="+confSize;
            cpModalUrl = cpModalUrl+"&confCopy="+confCopy;
            cpModalUrl = cpModalUrl+"&confEventId="+confEventId;
            cpModalUrl = cpModalUrl+"&patientIdList="+patientIdList;
            window.open(cpModalUrl)
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select some Patinet first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }

        // let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        // if(sel){
        //     let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
        //     if(sel2){
        //         confCopy = $("#cp<?php echo e($_idb_); ?>dGridLabelConfCopy").val();
        //         let cpModalUrl = "<?php echo e($urlb); ?>label/"+sel2.id;
        //         cpModalUrl = cpModalUrl+"?confCopy="+confCopy;
        //         cpModalUrl = cpModalUrl+"&confSize="+confSize;
        //         window.open(cpModalUrl)
        //     }else{
        //         Swal.fire({
        //             width:'300px',
        //             icon: 'error',
        //             title: 'Oops...',
        //             text: 'Please select one row to Print Label',
        //             showConfirmButton:false
        //         })
        //         setTimeout(() => { Swal.close(); }, 3000);
        //     }
        // }else{
        //     Swal.fire({
        //         width:'300px',
        //         icon: 'error',
        //         title: 'Oops...',
        //         text: 'Please select one MCU Event first',
        //         showConfirmButton:false
        //     })
        //     setTimeout(() => { Swal.close(); }, 3000);
        // }
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
                text: 'Please select one MCU Event first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridPrintResult(){
        let selEvent = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(selEvent){
            let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            
            var patientIdList = [];
            if(sel){
                patientIdList.push(sel.id);
            }
            if(checked.length > 0){
                for (let i = 0; i < checked.length; i++) {
                    const sel = checked[i];
                    patientIdList.push(sel.id);
                }
            }

            if(patientIdList.length > 0){
                var resultCate = $eu("#cp<?php echo e($_idb_); ?>dGridItemLv0").combobox('getValue');
                var filterParams = "mcu_event_id="+selEvent.id+"&patientIdList="+patientIdList+"&resultCate="+resultCate;
                window.open("<?php echo e($urlb); ?>resultPrint?"+filterParams);
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select some Patinet first',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Event.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridVerify(){
        let selEvent = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(selEvent){
            let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            
            var patientIdList = [];
            if(sel){
                patientIdList.push(sel.id);
            }
            if(checked.length > 0){
                for (let i = 0; i < checked.length; i++) {
                    const sel = checked[i];
                    patientIdList.push(sel.id);
                }
            }
            if(patientIdList.length > 0){
                Swal.fire({
                    // width:'300px',
                    customClass: {
                        confirmButton: 'btn btn-outline-primary',
                        cancelButton: 'btn btn-outline-danger'
                    },
                    buttonsStyling: false,
                    icon: 'warning',
                    title: 'Are you sure ?',
                    text: 'VERIFY '+patientIdList.length+' Data ?',
                    showCancelButton:true,
                    showConfirmButton:true
                }).then((result) => {
                    if(result.isConfirmed){
                        jf<?php echo e($_idb_); ?>dGridVerifyGo();
                    }
                })
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select some Patinet first',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Event.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridVerifyGo(){
        let selEvent = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(selEvent){
            let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            
            var patientIdList = [];
            if(sel){
                patientIdList.push(sel.id);
            }
            if(checked.length > 0){
                for (let i = 0; i < checked.length; i++) {
                    const sel = checked[i];
                    patientIdList.push(sel.id);
                }
            }
            if(patientIdList.length > 0){
                preloader_block();
                $.ajax({
                    type: 'PUT',
                    dataType: "JSON",
                    url:"<?php echo e($urlb); ?>resultVerify",
                    data:{
                        "_token": "<?php echo e(csrf_token()); ?>",
                        "mcu_event_id": selEvent.id,
                        "patientIdList": JSON.stringify(patientIdList)
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
                            setTimeout(() => { Swal.close(); }, 3000);

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
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select some Patinet first',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Event.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }

    function jf<?php echo e($_idb_); ?>dGridGenerateConclusion(){
        let selEvent = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(selEvent){
            let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            let checked = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getChecked');
            
            var patientIdList = [];
            if(sel){
                patientIdList.push(sel.id);
            }
            if(checked.length > 0){
                for (let i = 0; i < checked.length; i++) {
                    const sel = checked[i];
                    patientIdList.push(sel.id);
                }
            }

            if(patientIdList.length > 0){
                var resultCate = $eu("#cp<?php echo e($_idb_); ?>dGridItemLv0").combobox('getValue');
                var filterParams = "mcu_event_id="+selEvent.id+"&patientIdList="+patientIdList+"&resultCate="+resultCate;
                window.open("<?php echo e($urlb); ?>conclusionGenerate?"+filterParams);
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select some Patinet first',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Event.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }

    function jf<?php echo e($_idb_); ?>dGridImport(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridFormImport";
            let cpModalUrl = "<?php echo e($urlb); ?>patientImport/"+sel.id;
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
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

    function jf<?php echo e($_idb_); ?>dGridUpdateResult(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
            if(sel2){
                var resultCate = $eu("#cp<?php echo e($_idb_); ?>dGridItemLv0").combobox('getValue');
                if(resultCate){
                    let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
                    let cpModalUrl = "<?php echo e($urlb); ?>updateResult/"+sel2.id+"?resultCate="+resultCate;
                    cpGlobalModal(cpModalId, cpModalUrl);
                    var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                    cpModal.show();
                }else{
                    Swal.fire({
                        width:'300px',
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select one MCU Category.',
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 3000);
                }
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
                text: 'Please select one MCU Event first',
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
                text: 'Please select one MCU Event first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 3000);
        }
    }
    function jf<?php echo e($_idb_); ?>dGridExportResult(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").datagrid('getSelected');
        if(sel){
            var resultCate = $eu("#cp<?php echo e($_idb_); ?>dGridItemLv0").combobox('getValue');
            if(resultCate){
                var filterParams = jf<?php echo e($_idb_); ?>dGridFilter_g();
                filterParams = filterParams+"&resultCate="+resultCate;
                window.open("<?php echo e($urlb); ?>resultExport?"+filterParams);
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select one MCU Category.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 3000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one MCU Event.',
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
                text: 'Please select one MCU Event first',
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
                text: 'Please select one MCU Event first',
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
                text: 'Please select one MCU Event first',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }
</script><?php /**PATH /home/u1598413/public_sys/dkdmcu_klab__dev/app/Modules/MCU2Onsite/v2Main.blade.php ENDPATH**/ ?>