

<?php $__env->startSection('title'); ?>
	Corporate Employee
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('headerBreadcrumb'); ?>

	<?php $__env->startComponent('BASE.LayoutAdmin.breadcrumb'); ?>
		<?php $__env->slot('breadcrumb_title'); ?>
			<h4 class="d-none d-sm-block">Corporate Employee</h4>
			<h5 class="d-block d-sm-none">Corporate Employee</h5>
		<?php $__env->endSlot(); ?>
        <?php $__env->slot('pathHome'); ?>
			<?php echo e($base_url); ?>

		<?php $__env->endSlot(); ?>
		<li class="breadcrumb-item active">Corporate Employee</li>
	<?php echo $__env->renderComponent(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false" class="p-3">
            <div id="cp<?php echo e($_id_); ?>dGridToolbar">
                <div class="row p-3">
                    <div class="col-12">
                        <?php if(isset($userScope['roleAction']['/create'])) { ?>
                            <button class="mb-1 btn btn-outline-primary" onclick="jf<?php echo e($_id_); ?>dGridAdd()"><i class="fas fa-add fa-fw"></i> Add New</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/update'])) { ?>
                            <button class="mb-1 btn btn-outline-secondary" onclick="jf<?php echo e($_id_); ?>dGridEdit()"><i class="fas fa-edit fa-fw"></i> Edit</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/delete'])) { ?>
                            <button class="mb-1 btn btn-outline-danger" onclick="jf<?php echo e($_id_); ?>dGridDelete()"><i class="fas fa-trash fa-fw"></i> Delete</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/export'])) { ?>
                            <button class="mb-1 btn btn-outline-success" onclick="jf<?php echo e($_id_); ?>dGridExport()"><i class="fas fa-file-excel fa-fw"></i> Export</button>
                        <?php } ?>
                        <?php if(isset($userScope['roleAction']['/importSave'])) { ?>
                            <button class="mb-1 btn btn-outline-success" onclick="jf<?php echo e($_id_); ?>dGridImport()"><i class="fas fa-file-excel fa-fw"></i> Import</button>
                        <?php } ?>
                    </div>
                    <div class="col-12">
                        <div style="float:right">
                            <input type="text" id="cp<?php echo e($_id_); ?>dGridFilterSrcEvt_front">
                            <button class="btn btn-outline-primary" onclick="jf<?php echo e($_id_); ?>dGridFilter_o()"><i class="fas fa-filter fa-fw"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </div>
            <table id="cp<?php echo e($_id_); ?>dGrid"></table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/chart/apex-chart/apex-chart.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/chart/apex-chart/stock-prices.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/chart/apex-chart/chart-custom.js')); ?>"></script>

<script>
    var docHeight = $(document).height();
    docHeight -= 120;
    $("#content_container").css("height",docHeight+"px");
</script>

<script>

    $eu(function(){
        $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid({
            toolbar: "#cp<?php echo e($_id_); ?>dGridToolbar",
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
            // url: "<?php echo e($url); ?>read",
            frozenColumns: [[
                
            ]],
            columns: [[
                {field: "corporate_code",title: "<b>Corp. Code</b>",align: "center",width: 100},
                {field: "corporate_name",title: "<b>Corporate</b>",align: "left",width: 200},
                {field: "emp_no",title: "<b>Emp. NUmber</b>",align: "center",width: 150},
                {field: "nik",title: "<b>ID (KTP / Passport)</b>",align: "center",width: 150},
                {field: "name",title: "<b>Fullname</b>",align: "left",width: 200},
                {field: "gender",title: "<b>Gender</b>",align: "center",width: 70},
                {field: "dob_dmY_slash",title: "<b>DOB</b>",align: "center",width: 100},
                {field: "phone",title: "<b>Phone</b>",align: "center",width: 150},
                {field: "address",title: "<b>Address</b>",align: "left",width: 200},
                {field: "area",title: "<b>Job Area</b>",align: "left",width: 150},
                {field: "division",title: "<b>Job Division</b>",align: "left",width: 150},
                {field: "position",title: "<b>Job Position</b>",align: "left",width: 150},
                {field: "eventnum",title: "<b>Last Event</b>",align: "center",width: 85},
                {field: "status",title: "<b>Last Status</b>",align: "center",width: 100},
                {field: "status_at",title: "<b>Last Date</b>",align: "center",width: 100},
                {field: "next_mcu_date",title: "<b>Next MCU</b>",align: "center",width: 100},
            ]],
            rowStyler: function(index, row){
                return 'color:'+row.allowRegistMCUColor;
            }
        })

        $eu("#cp<?php echo e($_id_); ?>dGridFilterSrcEvt_front").textbox({
            prompt:"Type to search.",
            onChange:function(){
                jf<?php echo e($_id_); ?>dGridFilterFront_p();
            }
        })
        jf<?php echo e($_id_); ?>dGridFilter_i();
    })

    function jf<?php echo e($_id_); ?>dGridFilter_i(){
        let cpModalId = "cpModal<?php echo e($_id_); ?>DGridFilter";
        let cpModalUrl = "<?php echo e($url); ?>filter";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
    }
    function jf<?php echo e($_id_); ?>dGridFilter_o(){
        let cpModalId = "cpModal<?php echo e($_id_); ?>DGridFilter";
        cpGlobalModalOpen(cpModalId);
    }
    function jf<?php echo e($_id_); ?>dGridFilter_g(){
        var srcEvt = $eu("#cp<?php echo e($_id_); ?>dGridFilterSrcEvt").textbox('getValue');
        $eu("#cp<?php echo e($_id_); ?>dGridFilterSrcEvt_front").textbox('setValue',srcEvt);
        var filterParams = $("#cp<?php echo e($_id_); ?>dGridFilterForm").serialize();
        return filterParams;
    }
    function jf<?php echo e($_id_); ?>dGridFilter_p(){
        var filterParams = jf<?php echo e($_id_); ?>dGridFilter_g();
        $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid("load", "<?php echo e($url); ?>read/?"+filterParams);
        
        let cpModalId = "cpModal<?php echo e($_id_); ?>DGridFilter";
        cpGlobalModalClose(cpModalId);
    }
    function jf<?php echo e($_id_); ?>dGridFilter_r(){
        $eu("#cp<?php echo e($_id_); ?>dGridFilterForm").form('clear');
        // jf<?php echo e($_id_); ?>dGridFilter_p();
    }
    function jf<?php echo e($_id_); ?>dGridFilterFront_p(){
        var srcEvt = $eu("#cp<?php echo e($_id_); ?>dGridFilterSrcEvt_front").textbox('getValue');
        $eu("#cp<?php echo e($_id_); ?>dGridFilterSrcEvt").textbox('setValue',srcEvt);
        jf<?php echo e($_id_); ?>dGridFilter_p();
    }

    function jf<?php echo e($_id_); ?>dGridAdd(){
        let cpModalId = "cpModal<?php echo e($_id_); ?>DGridForm";
        let cpModalUrl = "<?php echo e($url); ?>add";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
    }   

    function jf<?php echo e($_id_); ?>dGridEdit(){
        let sel = $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('getSelected');
        if(sel){
            let cpModalId = "cpModal<?php echo e($_id_); ?>DGridForm";
            let cpModalUrl = "<?php echo e($url); ?>edit/"+sel.id;
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

    function jf<?php echo e($_id_); ?>dGridDelete(){
        let sel = $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('getSelected');
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
                    jf<?php echo e($_id_); ?>dGridDeleteGo();
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
    function jf<?php echo e($_id_); ?>dGridDeleteGo(){
        let sel = $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('getSelected');
        preloader_block();
        $.ajax({
            type: 'DELETE',
            dataType: "JSON",
            url:"<?php echo e($url); ?>delete/"+sel.id,
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

                    $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('reload');
                    $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('clearSelections');
                    $eu("#cp<?php echo e($_id_); ?>dGrid").datagrid('clearChecked');
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

    function jf<?php echo e($_id_); ?>dGridExport(){
        var filterParams = jf<?php echo e($_id_); ?>dGridFilter_g();
        window.open("<?php echo e($url); ?>export/?"+filterParams);
    }

    function jf<?php echo e($_id_); ?>dGridImport(){
        let cpModalId = "cpModal<?php echo e($_id_); ?>DGridForm";
        let cpModalUrl = "<?php echo e($url); ?>import";
        cpGlobalModal(cpModalId, cpModalUrl);
        var cpModal = new bootstrap.Modal(document.getElementById(cpModalId), {backdrop:'static'});
        cpModal.show();
    }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('BASE.LayoutAdmin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u1598413/public_sys/devapp-mcu/app/Modules/CorporateEmp/vMain.blade.php ENDPATH**/ ?>