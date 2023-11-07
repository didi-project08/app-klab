<div id="cp<?php echo e($_idb_); ?>dGridToolbar">
    <div class="row p-3">
        <div class="col-12 col-md-12">
            <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridAdd(1)"><i class="fas fa-add fa-fw"></i> Add (Parent)</button>
            <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridAdd(0)"><i class="fas fa-add fa-fw"></i> Add (Input)</button>
            <button class="mb-1 btn btn-outline-secondary" onclick="jf<?php echo e($_idb_); ?>dGridEdit()"><i class="fas fa-edit fa-fw"></i> Edit</button>
            <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridDelete()"><i class="fas fa-trash fa-fw"></i> Delete</button>
        </div>
        <div class="col-12 col-md-12">
            <button class="mb-1 btn btn-outline-secondary" onclick="jf<?php echo e($_idb_); ?>dGridMoveTo(1)"><i class="fas fa-arrow-up fa-fw"></i> Up</button>
            <button class="mb-1 btn btn-outline-secondary" onclick="jf<?php echo e($_idb_); ?>dGridMoveTo(0)"><i class="fas fa-arrow-down fa-fw"></i> Down</button>
            <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridDuplicateTo()"><i class="fas fa-clone fa-fw"></i> Duplicate To</button>
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
        $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid({
            toolbar: "#cp<?php echo e($_idb_); ?>dGridToolbar",
            fit:true,
            pagination:true,
            pageList: [1000],
            pageSize: 1000,
            rownumbers: true,
            checkbox: false,
            method: "GET",
            idField: "id",
            treeField: "name_custome",
            // url: "<?php echo e($urlb); ?>read",
            columns: [[
                {field: "name_custome",title: "<b>Name</b>",align: "left",width: 350},
                {field: "level",title: "<b>Level</b>",align: "center",width: 50},
                {field: "header_name",title: "<b>Type</b>",align: "center",width: 90},
                {field: "unit",title: "<b>Unit</b>",align: "center",width: 90},
                {field: "ref_m",title: "<b>Normal (Male)</b>",align: "center",width: 150},
                {field: "ref_f",title: "<b>Normal (Female)</b>",align: "center",width: 150},
                {field: "code",title: "<b>Code</b>",align: "center",width: 120},
            ]],
            onSelect:function(row){
                // var root = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getRoot');
                // var roots = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getRoots');
                // var parent = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getParent', row.id);
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

        var sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").treegrid('getSelected');
        var dGridId = 0;
        if(sel){
            dGridId = sel.id;
        }
        filterParams = filterParams+"&mcu_format_id="+dGridId;

        return filterParams;
    }
    function jf<?php echo e($_idb_); ?>dGridFilter_p(){
        var filterParams = jf<?php echo e($_idb_); ?>dGridFilter_g();
        // $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid("load", "<?php echo e($urlb); ?>read/?"+filterParams);
        $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid({
            url: "<?php echo e($urlb); ?>read/?"+filterParams
        });
        
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

    function jf<?php echo e($_idb_); ?>dGridAdd(header = 0){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").treegrid('getSelected');
        if(sel){
            let sel2 = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getSelected');
            if(sel2){
                // console.log(sel2);
                if(sel2.id == -1 && header == 0){
                    Swal.fire({
                        width:'300px',
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Only Allow "Add (Parent)"',
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 2000);
                    return true;
                }
                if(sel2.header == 1){
                    var sort = 1;
                    var children = sel2.children;
                    if(children.length > 0){
                        var lastIndex = children.length - 1;
                        sort = eval(children[lastIndex].sort * 1 + 1);
                    }
                    let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
                    let cpModalUrl = "<?php echo e($urlb); ?>add/"+sel.id+"/"+sel2.id+"?header="+header+"&sort="+sort;
                    cpGlobalModal(cpModalId, cpModalUrl);
                    var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
                    cpModal.show();
                }else{
                    Swal.fire({
                        width:'300px',
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select Parent.',
                        showConfirmButton:false
                    })
                    setTimeout(() => { Swal.close(); }, 2000);
                }
            }else{
                Swal.fire({
                    width:'300px',
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select Parent.',
                    showConfirmButton:false
                })
                setTimeout(() => { Swal.close(); }, 2000);
            }
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select Format first.',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }   

    function jf<?php echo e($_idb_); ?>dGridEdit(){
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getSelected');
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
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getSelected');
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
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getSelected');
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

                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('reload');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearSelections');
                    $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearChecked');
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

    function jf<?php echo e($_idb_); ?>dGridMoveTo(moveTo = 0){
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getSelected');
        if(sel){
            if(sel.id != -1){
                var id = sel.id;
                var parent = $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('getParent', id);
                var children = parent.children;
                // console.log(sel);
                // console.log(parent);
                // console.log(children);

                var beforeId = -1;
                var centerId = sel.id;
                var afterId = -1;
                var beforeSort = -1;
                var centerSort = sel.sort;
                var afterSort = -1;

                var loop = 0;
                var stop = 0;
                for (let i = 0; i < children.length; i++) {
                    if(loop == -1){
                        beforeId = afterId;
                        beforeSort = afterSort;
                        loop++;
                        loop++;
                    }
                    loop++;
                    if(loop == 1){
                        beforeId = children[i].id
                        beforeSort = children[i].sort
                    }else if(loop == 2){
                        centerId = children[i].id
                        centerSort = children[i].sort
                    }else if(loop == 3){
                        afterId = children[i].id
                        afterSort = children[i].sort
                        loop = -1;
                    }
                    if(stop == 1){
                        break;
                    }
                    if(children[i].id == sel.id){
                        if(loop == -1){
                            beforeId = centerId
                            beforeSort = centerSort
                        }
                        centerId = children[i].id
                        centerSort = children[i].sort
                        loop = 2;
                        stop = 1;
                    }
                }
                // console.log(beforeId);
                // console.log(centerId);
                // console.log(afterId);
                // console.log(beforeSort);
                // console.log(centerSort);
                // console.log(afterSort);

                var allowMoveTo = false;
                if(moveTo == 1){
                    if(centerSort > beforeSort){
                        allowMoveTo = true;
                    }
                }else if(moveTo == 0){
                    if(centerSort < afterSort){
                        allowMoveTo = true;
                    }
                }

                if(allowMoveTo){
                    preloader_block();
                    $.ajax({
                        type: 'PUT',
                        dataType: "JSON",
                        url:"<?php echo e($urlb); ?>moveTo",
                        data:{
                            "_token": "<?php echo e(csrf_token()); ?>",
                            moveTo : moveTo,
                            beforeId : beforeId,
                            centerId : centerId,
                            afterId : afterId,
                            beforeSort : beforeSort,
                            centerSort : centerSort,
                            afterSort : afterSort
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

                                $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('reload');
                                $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearSelections');
                                $eu("#cp<?php echo e($_idb_); ?>dGrid").treegrid('clearChecked');
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
            }
        }
    }

    function jf<?php echo e($_idb_); ?>dGridDuplicateTo(){
        let sel = $eu("#cp<?php echo e($_id_); ?>b1dGrid").treegrid('getSelected');
        if(sel){
            let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridFormDuplicateTo";
            let cpModalUrl = "<?php echo e($urlb); ?>duplicateTo/"+sel.id;
            cpGlobalModal(cpModalId, cpModalUrl);
            var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
            cpModal.show();
        }else{
            Swal.fire({
                width:'300px',
                icon: 'error',
                title: 'Oops...',
                text: 'Please select one row to Duplicate',
                showConfirmButton:false
            })
            setTimeout(() => { Swal.close(); }, 2000);
        }
    }
</script><?php /**PATH /home/u1598413/public_sys/dkdmcu_klab__dev/app/Modules/MCUFormat/v2Main.blade.php ENDPATH**/ ?>