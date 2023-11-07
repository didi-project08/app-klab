<?php $__env->startSection('title'); ?>Maintenance
 <?php echo e($title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/sweetalert2.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
	<section class="maintenance-sec">
	    <div class="page-wrapper">
	        <div class="error-wrapper maintenance-bg">
	            <div class="container">
	                <ul class="maintenance-icons">
	                    <li><i class="fa fa-cog"></i></li>
	                    <li><i class="fa fa-cog"></i></li>
	                    <li><i class="fa fa-cog"></i></li>
	                </ul>
	                <div class="maintenance-heading">
	                    <h2 class="headline">MAINTENANCE</h2>
	                </div>
	                <h4 class="sub-content">
	                    Our Site is Currently under maintenance We will be back Shortly<br />
	                    Thank You For Patience
	                </h4>
	                <div><a class="btn btn-primary btn-lg text-light" href="<?php echo e(route('index')); ?>">BACK TO HOME PAGE</a></div>
	            </div>
	        </div>
	    </div>
	</section>


    <?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.authentication.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Documents\Envato\viho_all\Viho-Laravel-8\theme\resources\views/admin/authentication/maintenance.blade.php ENDPATH**/ ?>