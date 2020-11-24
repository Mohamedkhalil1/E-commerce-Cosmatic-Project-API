
<?php $__env->startComponent('mail::message'); ?>
# Hello <?php echo e($user->name); ?>


Use the code below to reset your <?php echo e(config('app.name')); ?> password for your <?php echo e($user->email); ?> account.

Reset Code : <?php echo e($user->phone_code); ?>


If you didn’t ask to reset your password, you can ignore this email.

Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/html/Outlet/resources/views/emails/phone/code.blade.php ENDPATH**/ ?>