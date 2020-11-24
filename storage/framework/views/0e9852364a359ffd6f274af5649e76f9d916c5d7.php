<table>
    <thead>
    <tr>
        <th><b>barcode</b></th>
        <th><b>title</b></th>
        <th><b>quantity</b></th>
        <th><b>Price</b></th>
        <th><b>Price Discount</b></th>
   
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e((string)$product['barcode']); ?></td>
            <td><?php echo e($product['title']); ?></td>
            <td><?php echo e($product['quantity']); ?></td>
            <td><?php echo e($product['price']); ?></td>
            <td><?php echo e($product['price_discount']); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH E:\360 cloud solution\OUTLET_PROJECT\Outlet - Copy - Copy\resources\views/exports/products.blade.php ENDPATH**/ ?>