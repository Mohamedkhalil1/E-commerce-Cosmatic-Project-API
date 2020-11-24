<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Invoice NO.</th>
        <th><b>Name</b></th>
        <th><b>Email</b></th>
        <th><b>Phone</b></th>
        <th><b>Amount</b></th>
        <th><b>Product Title</b></th>
        <th><b>Price</b></th>
        <th><b>Quantity</b></th>
        <th><b>Barcode</b></th>
        <th><b>City</b></th>
        <th><b>Region</b></th>
        <th><b>Address</b></th>
        <th><b>Payment type</b></th>
        <th><b>System Reference(waybill)</b></th>
        <th><b>Date</b></th>
        <th><b>Invoice Url</b></th>
      
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo e($index++); ?>

       <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index2 => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php if( $details->where('order_id',$order->id)->where('product_id',$product->id)->first()->quantity !== 0): ?>
                <tr>
                    <td><?php echo e($index); ?></td>
                    <td><?php echo e($order->invoice_num); ?></td>
                
                    <td><?php echo e($order->user ? $order->user->name  : ''); ?></td>
                    <td><?php echo e($order->user ? $order->user->email  : ''); ?></td>
                    <td><?php echo e($order->user ? $order->user->phone  : ''); ?></td>
                    <?php if( $order->payment_type === 'card'): ?>
                    <td>0.00</td>

                    <?php else: ?>
                    <td><?php echo e(number_format((float)$order->amount, 2, '.', '')); ?> </td>

                    <?php endif; ?>
                    
                    <td><?php echo e($product->title); ?></td>
                    <td><?php echo e($product->family_price); ?></td>
                    <td><?php echo e($details->where('order_id',$order->id)->where('product_id',$product->id)->first()->quantity); ?></td>
                    <td><?php echo e($product->barcode); ?></td>

                    <td><?php echo e($order->city); ?></td>
                    <td><?php echo e($order->region); ?></td>
                    <td><?php echo e($order->address); ?></td>
                    <td><?php echo e($order->payment_type); ?></td>
                    <td><?php echo e($order->waybill); ?></td>

                    <td><?php echo e($order->created_at->format('d-m-Y h:m')); ?></td>
                    <td><?php echo e($order->invoice_url); ?></td>
                  
                </tr>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH E:\360 cloud solution\OUTLET_PROJECT\Outlet - Copy - Copy\resources\views/exports/orders.blade.php ENDPATH**/ ?>