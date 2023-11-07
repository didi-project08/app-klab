<div class="page-main-header">
  <div class="main-header-right row m-0">
    <div class="main-header-left">
      <!-- <div class="logo-wrapper"><a href="<?php echo e(route('index')); ?>"><img class="img-fluid" src="<?php echo e(asset('assets/images/logo/logo.png')); ?>" alt=""></a></div> -->
      <!-- <div class="dark-logo-wrapper"><a href="<?php echo e(route('index')); ?>"><img class="img-fluid" src="<?php echo e(asset('assets/images/logo/dark-logo.png')); ?>" alt=""></a></div> -->
      <a href="<?php echo e($base_url); ?>">
        <h4 class="d-none d-sm-block"><b><?php echo e(env('APP_NAME')); ?></b></h4>
        <h5 class="d-block d-sm-none"><b><?php echo e(env('APP_NAME')); ?></b></h5>
      </a>
      <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle">    </i></div>
    </div>
    <div class="left-menu-header col" style="padding-bottom:0px; padding-top:8px">
      <!-- <ul>
        <li>
          <form class="form-inline search-form">
            <div class="search-bg"><i class="fa fa-search"></i>
              <input class="form-control-plaintext" placeholder="Search here.....">
            </div>
          </form>
          <span class="d-sm-none mobile-search search-bg"><i class="fa fa-search"></i></span>
        </li>
      </ul> -->
      <?php echo $__env->yieldContent('headerBreadcrumb'); ?>
    </div>
    <div class="nav-right col pull-right right-menu p-0">
      <ul class="nav-menus">
        <li><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i data-feather="maximize"></i></a></li>
        <li class="onhover-dropdown p-0">
          <a class="btn btn-primary-light" type="button" href="<?php echo e($base_url); ?>logout"><i data-feather="log-out"></i>Log out</a>
        </li>
      </ul>
    </div>
    <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
  </div>
</div>
<?php /**PATH /home/u1598413/public_sys/dkdmcu/app/Modules/BASE/LayoutAdmin/partials/header.blade.php ENDPATH**/ ?>