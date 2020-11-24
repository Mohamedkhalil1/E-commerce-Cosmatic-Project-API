<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Invoice NO.</th>
        <th><b>Name</b></th>
        <th><b>Staff Email</b></th>
        <th><b>Email</b></th>
        <th><b>Phone</b></th>
        <th><b>Amount</b></th>
        <th>Shipping</th>
        <th><b>City</b></th>
        <th><b>Region</b></th>
        <th><b>Address</b></th>
        <th><b>System Reference</b></th>
        <th><b>Date</b></th>
        <th><b>Invoice Url</b></th>

        <th><b>TOTAL AMOUNT OF DAY </b></th>
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       
        <tr>
            <td><?php echo e($index++); ?></td>
            <td><?php echo e($order->invoice_num); ?></td>
        
            <td><?php echo e($order->user ? $order->user->name  : ''); ?></td>
            <td><?php echo e($order->user ? $order->user->parent_id === null ? '' : $order->user->staff->email : ''); ?></td>
            <td><?php echo e($order->user ? $order->user->email  : ''); ?></td>
            <td><?php echo e($order->user ? $order->user->phone  : ''); ?></td>
            <td><?php echo e(number_format((float)$order->amount - $order->shipping_fees, 2, '.', '')); ?> </td>
            <td><?php echo e(number_format((float)$order->shipping_fees, 2, '.', '')); ?> </td>
            
            <td><?php echo e($order->city); ?></td>
            <td><?php echo e($order->region); ?></td>
            <td><?php echo e($order->address); ?></td>
            <td><?php echo e($order->system_reference); ?></td>

            <td><?php echo e($order->created_at->format('d-m-Y h:m')); ?></td>
            <td><?php echo e($order->invoice_url); ?></td>
            <td><?php echo e($amount); ?></td>
        </tr>
         
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH E:\360 cloud solution\OUTLET_PROJECT\Outlet - Copy - Copy\resources\views/exports/orders2.blade.php ENDPATH**/ ?>