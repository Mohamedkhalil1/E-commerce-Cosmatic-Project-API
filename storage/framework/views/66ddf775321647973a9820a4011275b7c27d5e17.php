<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Invoice NO.</th>
        <th><b>Name</b></th>
        <th><b>Amount</b></th>
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($index++); ?></td>
            <td><?php echo e($order->created_at); ?></td>
            <td><?php echo e($order->invoice_num); ?></td>
            <td><?php echo e($order->user ? $order->user->name  : ''); ?></td>
            <td><?php echo e(number_format((float)$order->amount, 2, '.', '')); ?> </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH /var/www/html/Outlet/resources/views/exports/orders.blade.php ENDPATH**/ ?>