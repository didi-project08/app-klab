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
</div><?php /**PATH C:\Users\Lenovo\Documents\Envato\viho_all\Viho-Laravel-8\theme\app\Modules/BASE/LayoutAdmin/breadcrumb.blade.php ENDPATH**/ ?>