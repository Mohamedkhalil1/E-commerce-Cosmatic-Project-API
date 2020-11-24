
<?php $__env->startComponent('mail::message'); ?>
# Hello <?php echo e($user->name); ?>


Use the code below to reset your Egypt Outlet Online password for your <?php echo e($user->email); ?> account.

<?php $__env->startComponent('mail::button', ['url' => "https://thefamilysale.com/#/reset-password/{$user->code}"]); ?>
    Reset Password
<?php echo $__env->renderComponent(); ?>

If you didn’t ask to reset your password, you can ignore this email.

Thanks,<br>
    Egypt Outlet Online 
<?php echo $__env->renderComponent(); ?>
<?php /**PATH E:\360 cloud solution\OUTLET_PROJECT\Outlet - Copy - Copy\resources\views/emails/code.blade.php ENDPATH**/ ?>