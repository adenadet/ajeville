<aside class="main-sidebar sidebar-primary bg-white">
    <a href="/home" class="brand-link bg-navy text-white">
        <img src="<?php echo e(asset(config('app.logo'))); ?>" alt="<?php echo e(config('app.name')); ?>" class="brand-image img-circle elevation-3 bg-white" style="opacity: 1">
        <span class="brand-text font-weight-light text-white"><?php echo e(config('app.name_short')); ?></span>
    </a>
    <div class="sidebar mb-5">
        <?php if($page_title == 'Admission'): ?> <?php echo $__env->make('partials.lte.asides.admission', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Anesthesist'): ?> <?php echo $__env->make('partials.lte.asides.anesthesia', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Approvals'): ?> <?php echo $__env->make('partials.lte.asides.approvals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Archives'): ?> <?php echo $__env->make('partials.lte.asides.archives', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Billings'): ?> <?php echo $__env->make('partials.lte.asides.billings', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Chats'): ?> <?php echo $__env->make('partials.lte.asides.chat', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Consultation'): ?> <?php echo $__env->make('partials.lte.asides.consultation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Cooperative'): ?> <?php echo $__env->make('partials.lte.asides.cooperative', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Cooperative Admin'): ?> <?php echo $__env->make('partials.lte.asides.cooperative_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Customer Relations'): ?> <?php echo $__env->make('partials.lte.asides.crm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Dashboard'): ?> <?php echo $__env->make('partials.lte.asides.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Equipments'): ?> <?php echo $__env->make('partials.lte.asides.equipments', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Escrow Admin'): ?> <?php echo $__env->make('partials.lte.asides.escrow_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Escrows'): ?> <?php echo $__env->make('partials.lte.asides.escrows', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Facility Management'): ?><?php echo $__env->make('partials.lte.asides.facility', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Finance'): ?><?php echo $__env->make('partials.lte.asides.finance', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Front Office'): ?><?php echo $__env->make('partials.lte.asides.front_office', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Human Resources Admin'): ?><?php echo $__env->make('partials.lte.asides.hr_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Human Resources Management'): ?><?php echo $__env->make('partials.lte.asides.hr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Managed Care'): ?><?php echo $__env->make('partials.lte.asides.insurance', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Inventory'): ?><?php echo $__env->make('partials.lte.asides.inventory', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Laboratory'): ?><?php echo $__env->make('partials.lte.asides.laboratory', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Learn Admin'): ?><?php echo $__env->make('partials.lte.asides.learn_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Learn Student' || $page_title == 'Learn Tutor'): ?><?php echo $__env->make('partials.lte.asides.learn', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Laboratory'): ?><?php echo $__env->make('partials.lte.asides.laboratory', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Loans'): ?><?php echo $__env->make('partials.lte.asides.loans', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Loans Admin'): ?><?php echo $__env->make('partials.lte.asides.loans_staff', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Nursing Care'): ?><?php echo $__env->make('partials.lte.asides.nursing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Operations'): ?><?php echo $__env->make('partials.lte.asides.operations', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Policies'): ?><?php echo $__env->make('partials.lte.asides.policies', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Policy | Reader'): ?><?php echo $__env->make('partials.lte.asides.policies', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Procurement'): ?><?php echo $__env->make('partials.lte.asides.procurement', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Radiology'): ?><?php echo $__env->make('partials.lte.asides.radiology', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php elseif($page_title == 'Sales Orders'): ?><?php echo $__env->make('partials.lte.asides.sales', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?> <?php echo $__env->make('partials.lte.asides.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </div>
</aside><?php /**PATH C:\wamp64\www\laravel10\ajeville\resources\views/partials/lte/left.blade.php ENDPATH**/ ?>