

<?php $__env->startSection('title'); ?>
	User Corporate
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('headerBreadcrumb'); ?>

	<?php $__env->startComponent('BASE.LayoutAdmin.breadcrumb'); ?>
		<?php $__env->slot('breadcrumb_title'); ?>
            <h4 class="d-none d-sm-block">User Corporate</h4>
			<h5 class="d-block d-sm-none">User Corporate</h5>
		<?php $__env->endSlot(); ?>
        <?php $__env->slot('pathHome'); ?>
			<?php echo e($base_url); ?>

		<?php $__env->endSlot(); ?>
		<li class="breadcrumb-item active">User Corporate</li>
	<?php echo $__env->renderComponent(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false" class="p-2">
            <div class="easyui-tabs" data-options="fit:true, border:true, tabPosition:'top'">
                <div data-options="title:'User Corporate', border:true" style="padding:10px">
                    <div class="easyui-layout" data-options="fit:true">
                        <div data-options="region:'center', title:'User List', split:true, hideCollapsedContent:false, border:false, href:'<?php echo e($url); ?>b1/index'" class="p-2"></div>
                        <div data-options="region:'south', title:'User Client Access', split:true, hideCollapsedContent:false, border:false, href:'<?php echo e($url); ?>b2/index'" class="p-2" style="height:50%"></div>
                    </div>
                </div>
                <div data-options="title:'Role Configuration', border:true" style="padding:10px">
                    <div class="easyui-layout" data-options="fit:true">
                        <div data-options="region:'center', title:'Role List', split:true, hideCollapsedContent:false, border:false, href:'<?php echo e($url); ?>b3/index'" class="p-2"></div>
                        <div data-options="region:'east', title:'Module Access', split:true, hideCollapsedContent:false, border:false, href:'<?php echo e($url); ?>b4/index'" class="p-2" style="width:50%"></div>
                    </div>
                </div>
            </div>
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
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('BASE.LayoutAdmin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/UserCorporate/vMain.blade.php ENDPATH**/ ?>