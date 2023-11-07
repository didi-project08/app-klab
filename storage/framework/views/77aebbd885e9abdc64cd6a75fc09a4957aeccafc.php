

<?php $__env->startSection('title'); ?>
	MCU Project
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('headerBreadcrumb'); ?>

	<?php $__env->startComponent('BASE.LayoutAdmin.breadcrumb'); ?>
		<?php $__env->slot('breadcrumb_title'); ?>
            <h4 class="d-none d-sm-block">Device Sample</h4>
			<h5 class="d-block d-sm-none">Device Sample</h5>
		<?php $__env->endSlot(); ?>
        <?php $__env->slot('pathHome'); ?>
			<?php echo e($base_url); ?>

		<?php $__env->endSlot(); ?>
		<li class="breadcrumb-item active">Device Sample</li>
	<?php echo $__env->renderComponent(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid" id="">
    <h4 class="font-primary f-w-700 mb-3">Selected Device</h4>
    <div class="row">
        <?php foreach ($device as $k => $v) { ?>
            <div class="col-sm-12 col-md-12">
                <div class="card shadow-sm shadow-showcase">
                    <div class="card-header bg-light" style="padding: 15px 20px !important">
                        <h5><?php echo e($v->title); ?></h5>
                        <small class="font-dark"><?php echo e($v->id); ?></small>
                    </div>
                    <div class="card-body" style="padding: 15px 30px !important">
                        <?php
                            $online_title = "Online";
                            $online_color = "ribbon-success";
                            $badge_color = "badge-success";
                            if($v->online == 0){
                                $online_title = "Offline";
                                $online_color = "ribbon-danger";
                                $badge_color = "badge-danger";
                            }
                        ?>
                        <div class="ribbon ribbon-clip-right ribbon-right <?php echo e($online_color); ?>"><?php echo e($online_title); ?></div>
                        <div><b><?php echo e($v->province); ?>, <?php echo e($v->city); ?></b></div>
                        <div><?php echo e($v->address); ?></div>
                        <br>
                        <div><?php echo e($v->desc); ?></div>
                        <br>
                        <div class="figure text-end d-block">
                            <cite title="Source Title">
                            <span class="badge <?php echo e($badge_color); ?>">LAST ONLINE : <?php echo e($v->last_online); ?></span>
                            </cite>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <h4 class="font-primary f-w-700 mb-3">Sample List</h4>
    <div style="height:400px">
        <div class="easyui-layout" data-options="fit:true">
            <div data-options="region:'center', border:false" class="">
                <div id="cp<?php echo e($_idb_); ?>dGridToolbar">
                    <div class="row p-3">
                        <div class="col-12 col-md-8">
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/create'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridAdd()"><i class="fas fa-add fa-lg"></i> Add New</button> -->
                            <?php } ?>
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/update'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-secondary" onclick="jf<?php echo e($_idb_); ?>dGridEdit()"><i class="fas fa-edit fa-lg"></i> Edit</button> -->
                            <?php } ?>
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/delete'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_idb_); ?>dGridDelete()"><i class="fas fa-trash fa-lg"></i> Delete</button> -->
                            <?php } ?>
                            <?php if(1==1 || isset($userScope['roleAction']['/b1/confirmSave'])) { ?>
                                <!-- <button class="mb-1 btn btn-outline-warning" onclick="jf<?php echo e($_idb_); ?>dGridConfirm()"><i class="fas fa-file-pen fa-lg"></i> Confirm</button> -->
                            <?php } ?>
                        </div>
                        <div class="col-12 col-md-4">
                            <div style="float:right">
                                <input type="text" id="cp<?php echo e($_idb_); ?>dGridFilterSrcEvt_front">
                                <button class="btn btn-outline-primary" onclick="jf<?php echo e($_idb_); ?>dGridFilter_o()"><i class="fas fa-filter fa-lg"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="cp<?php echo e($_idb_); ?>dGrid"></table>
            </div>
        </div>
    </div>
  
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/chart/apex-chart/apex-chart.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/chart/apex-chart/stock-prices.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/chart/apex-chart/chart-custom.js')); ?>"></script>

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
                {field: "auto_id",title: "<b>Auto ID</b>",align: "center",width: 100},
                {field: "sample_id",title: "<b>Sample ID</b>",align: "left",width: 200},
                {field: "patient_name",title: "<b>Patient Name</b>",align: "left",width: 250},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.status_color;
            },
            onLoadSuccess:function(){
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearSelections');
                $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('clearChecked');
                if(typeof jf<?php echo e($_id_); ?>b3dGridFilter_r === "function"){
                    jf<?php echo e($_id_); ?>b3dGridFilter_r();
                }
                if(typeof jf<?php echo e($_id_); ?>b3dGridFilterFront_p === "function"){
                    jf<?php echo e($_id_); ?>b3dGridFilterFront_p();
                }
                if(typeof jf<?php echo e($_id_); ?>b2dGridFilter_r === "function"){
                    jf<?php echo e($_id_); ?>b2dGridFilter_r();
                }
                if(typeof jf<?php echo e($_id_); ?>b2dGridFilterFront_p === "function"){
                    jf<?php echo e($_id_); ?>b2dGridFilterFront_p();
                }
            },
            onSelect:function(index,row){
                if(typeof jf<?php echo e($_id_); ?>b3dGridFilter_r === "function"){
                    jf<?php echo e($_id_); ?>b3dGridFilter_r();
                }
                if(typeof jf<?php echo e($_id_); ?>b3dGridFilterFront_p === "function"){
                    jf<?php echo e($_id_); ?>b3dGridFilterFront_p();
                }
                if(typeof jf<?php echo e($_id_); ?>b2dGridFilter_r === "function"){
                    jf<?php echo e($_id_); ?>b2dGridFilter_r();
                }
                if(typeof jf<?php echo e($_id_); ?>b2dGridFilterFront_p === "function"){
                    jf<?php echo e($_id_); ?>b2dGridFilterFront_p();
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
        return filterParams;
    }
    function jf<?php echo e($_idb_); ?>dGridFilter_p(){
        var filterParams = jf<?php echo e($_idb_); ?>dGridFilter_g();
        $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid("load", "<?php echo e($urlb); ?>read/<?php echo e($device_id); ?>?"+filterParams);
        
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
        let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridForm";
        let cpModalUrl = "<?php echo e($urlb); ?>add";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
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
            setTimeout(() => { Swal.close(); }, 3000);
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
    function jf<?php echo e($_idb_); ?>dGridConfirm(){
        let sel = $eu("#cp<?php echo e($_idb_); ?>dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal<?php echo e($_idb_); ?>DGridFormConfirm";
            let cpModalUrl = "<?php echo e($urlb); ?>confirm/"+sel.id;
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

<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('BASE.LayoutAdmin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u1598413/public_sys/devapp-mcu/app/Modules/HM/v1Main.blade.php ENDPATH**/ ?>