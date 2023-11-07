<?php $__env->startSection('title'); ?>Unlock
 <?php echo e($title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/sweetalert2.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <section>
	    <div class="container-fluid p-0">
	        <div class="row m-0">
	            <div class="col-12 p-0">
	                <div class="login-card">
	                    <div class="login-main">
	                        <form class="theme-form login-form">
	                            <h4>Unlock</h4>
	                            <div class="form-group">
	                                <label class="col-form-label">Enter your Password</label>
	                                <div class="input-group">
	                                    <span class="input-group-text"><i class="icon-email"></i></span>
	                                    <input class="form-control" type="password" name="login[password]" required="" placeholder="*********" />
	                                    <div class="show-hide"><span class="show"> </span></div>
	                                </div>
	                            </div>
	                            <div class="form-group">
	                                <div class="checkbox p-0">
	                                    <input id="checkbox1" type="checkbox" />
	                                    <label class="text-muted" for="checkbox1">Remember password</label>
	                                </div>
	                            </div>
	                            <div class="form-group">
	                                <button class="btn btn-primary btn-block" type="submit">Unlock</button>
	                            </div>
	                            <p>Already have an account?<a class="ms-2" href="<?php echo e(route('login')); ?>">Sign in </a></p>
	                        </form>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>


    <?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.authentication.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Lenovo\Documents\Envato\viho_all\Viho-Laravel-8\theme\resources\views/admin/authentication/unlock.blade.php ENDPATH**/ ?>