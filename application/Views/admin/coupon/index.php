<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<?php
    if(isset($message["success"])) :
?>
<script>
    notif({
        msg:"<?php echo $message['success']; ?>",
        type:"success",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 10000,
        animation:'slide'
    });
</script>
<?php endif; ?>  
<h1 class="heading">Mã giảm giá</h1>
<div class="row">
    <div class="col-4">
        <form id="add-coupon-form" method="POST" action="<?php echo url("admin/coupon/add") ?>">
            <div class="mb-3">
                <label class="form-label">Mã code</label>
                <input type="text" name="code" value="<?php echo $old["code"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['code'])): ?>
                    <?php foreach($errors['code'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Mức giảm</label>
                <input type="number" name="amount" value="<?php echo $old["amount"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['amount'])): ?>
                    <?php foreach($errors['amount'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Giá trị đơn hàng tối thiểu</label>
                <input type="number" name="minimum_spend" value="<?php echo $old["minimum_spend"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['minimum_spend'])): ?>
                    <?php foreach($errors['minimum_spend'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Lượt sử dụng</label>
                <input type="number" name="usage" value="<?php echo $old["usage"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['usage'])): ?>
                    <?php foreach($errors['usage'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Lượt sử dụng/người mua</label>
                <input type="number" name="per_user" value="<?php echo $old["per_user"] ?? 1 ?>" class="form-control">
                <?php if(isset($errors['per_user'])): ?>
                    <?php foreach($errors['per_user'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Thời gian bắt đầu</label>
                <input type="text" name="start_time" value="<?php $old["start_time"] ?? "" ?>" class="form-control coupon-time" autocomplete="off">
                <?php if(isset($errors['start_time'])): ?>
                    <?php foreach($errors['start_time'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Thời gian kết thúc</label>
                <input type="text" name="end_time" value="<?php $old["end_time"] ?? "" ?>" class="form-control coupon-time" autocomplete="off">
                <?php if(isset($errors['end_time'])): ?>
                    <?php foreach($errors['end_time'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Add New Coupon</button>
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                                     
        </form>
    </div>
    <div class="col-8">
        <div class="table-responsive">
            <table id="coupon-table" class="table table-borderless table-striped-columns ">
            <thead>
                <tr>
                    <th scope="col">Mã code</th>
                    <th scope="col">Mức giảm</th>
                    <th scope="col">Tối thiểu</th>
                    <th scope="col">Lượt dùng</th>
                    <th scope="col">Lượt dùng/người mua</th>
                    <th scope="col">Đã dùng</th>
                    <th scope="col">Bắt đầu</th>
                    <th scope="col">Kết thúc</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($coupons as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item->code ?>
                            <div class="row-actions">
                                <span data-route="<?php echo url("admin/coupon/delete/{$item->id}") ?>" class="delete"><a href="javascript:void(0)" onclick="deleteCoupon(<?php echo $item->id ?>)">Xóa</a><form id="delete-coupon-<?php echo $item->id ?>" style="display:none" method="POST" action="<?php echo url("admin/coupon/delete/{$item->id}") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                <span class="edit"><a href="<?php echo url("admin/coupon/edit/{$item->id}") ?>">Sửa</a></span>
                            </div>
                        </td>
                        <td><?php echo number_format($item->amount,0,"",".") ?>đ</td>
                        <td><?php echo number_format($item->minimum_spend,0,"","."); ?>đ</td>
                        <td><?php echo $item->coupon_usage ?></td>
                        <td><?php echo $item->per_user ?></td>
                        <td><?php echo $item->coupon_used ?></td>
                        <td><?php echo $item->start_time ?></td>
                        <td><?php echo $item->end_time ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        
    </div>
</div>
<script>
    const add_coupon_url = "<?php echo url("admin/coupon/add"); ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>