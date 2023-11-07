<header class="main-nav">
    <div class="sidebar-user text-center" style="margin-bottom:0px">
        <a class="setting-primary" href="javascript:void(0)"><i data-feather="settings"></i></a><img class="img-90 rounded-circle" src="<?php echo e(asset('assets/images/dashboard/1.png')); ?>" alt="" />
        <div class="badge-bottom"><span class="badge badge-primary"><h6><?php echo e($userInfo->f_fullname); ?></h6></span></div>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar" style="height: calc(100vh - 250px)">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>

                    <li class="sidebar-main-title">
                        <div>
                            <h6>MODULES</h6>
                        </div>
                    </li>

                    <?php foreach ($userModule as $k => $v) { ?>
                        <li>
                            <a class="nav-link menu-title link-nav <?php echo e(routeActive($v['f_module'])); ?>" href="<?php echo e($base_url.$v['f_module']); ?>">
                                <table>
                                    <tr>
                                        <td><i class="<?php echo e($v['f_icon']); ?> fa-fw fa-lg" style="margin-right:10px"></i></td>
                                        <td><span><?php echo e($v['f_module_name']); ?></span></td>
                                    </tr>
                                </table>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>
<?php /**PATH /home/n1731643/public_sys/devapp-mcu/app/Modules/BASE/LayoutAdmin/partials/sidebar.blade.php ENDPATH**/ ?>