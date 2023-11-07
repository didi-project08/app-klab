

<?php $__env->startSection('title'); ?>
	Corporate Client
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('headerBreadcrumb'); ?>

	<?php $__env->startComponent('BASE.LayoutAdmin.breadcrumb'); ?>
		<?php $__env->slot('breadcrumb_title'); ?>
            <h4 class="d-none d-sm-block">Corporate Client</h4>
			<h5 class="d-block d-sm-none">Corporate Client</h5>
		<?php $__env->endSlot(); ?>
        <?php $__env->slot('pathHome'); ?>
			<?php echo e($base_url); ?>

		<?php $__env->endSlot(); ?>
		<li class="breadcrumb-item active">Corporate Client</li>
	<?php echo $__env->renderComponent(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid" id="content_container">
    <div class="easyui-layout" data-options="fit:true">
        <div data-options="region:'center', border:false, href:'<?php echo e($url); ?>b1/index'" class="p-3"></div>
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
<?php echo $__env->make('BASE.LayoutAdmin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u1598413/public_sys/devapp-mcu/app/Modules/CorporateClient/vMain.blade.php ENDPATH**/ ?>