<div class="container-fluid-disable">
    <div class="page-header">
      <div class="row">
        <div class="col-lg-12">
          <?php echo e($breadcrumb_title ?? ''); ?>

          <ol class="breadcrumb d-none d-md-block">
            <li class="breadcrumb-item"><a href="<?php echo e($pathHome ?? ''); ?>">Home</a></li>
              <?php echo e($slot ?? ''); ?>

          </ol>
        </div>
      </div>
    </div>
</div><?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/BASE/LayoutAdmin/breadcrumb.blade.php ENDPATH**/ ?>