

<?php $__env->startSection('title'); ?>
	Home
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('headerBreadcrumb'); ?>

	<?php $__env->startComponent('BASE.LayoutAdmin.breadcrumb'); ?>
		<?php $__env->slot('breadcrumb_title'); ?>
			<h4>Home</h4>
		<?php $__env->endSlot(); ?>
		<?php $__env->slot('pathHome'); ?>
			<?php echo e($base_url); ?>

		<?php $__env->endSlot(); ?>
	<?php echo $__env->renderComponent(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	
	
	<div class="container-fluid">
		<h1>WELCOME</h1>
	</div>
	
	
	<?php $__env->startPush('scripts'); ?>
	<script src="<?php echo e(asset('assets/js/chart/apex-chart/apex-chart.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/chart/apex-chart/stock-prices.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/chart/apex-chart/chart-custom.js')); ?>"></script>
	<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('BASE.LayoutAdmin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/Home/vMain.blade.php ENDPATH**/ ?>