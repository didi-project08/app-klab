

<?php $__env->startSection('title'); ?>
	MCU Project
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('headerBreadcrumb'); ?>

	<?php $__env->startComponent('BASE.LayoutAdmin.breadcrumb'); ?>
		<?php $__env->slot('breadcrumb_title'); ?>
            <h4 class="d-none d-sm-block">Device List</h4>
			<h5 class="d-block d-sm-none">Device List</h5>
		<?php $__env->endSlot(); ?>
        <?php $__env->slot('pathHome'); ?>
			<?php echo e($base_url); ?>

		<?php $__env->endSlot(); ?>
		<li class="breadcrumb-item active">Device List</li>
	<?php echo $__env->renderComponent(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid" id="">
    <div class="row">
        <?php foreach ($device as $k => $v) { ?>
            <div class="col-sm-12 col-md-6">
                <div class="card shadow-sm shadow-showcase">
                    <div class="card-header bg-light" style="padding: 15px 20px !important">
                        <h5><a href="<?php echo e($url); ?>b1/sample/<?php echo e($v->id); ?>"><?php echo e($v->title); ?></a></h5>
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
        $eu("#cp<?php echo e($_id_); ?>tabs").tabs('select',1);
    })
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('BASE.LayoutAdmin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u1598413/public_sys/devapp-mcu/app/Modules/HM/vMain.blade.php ENDPATH**/ ?>