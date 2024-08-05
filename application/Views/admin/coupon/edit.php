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
<div class="heading">Sửa mã giảm giá</div>
<form id="update-coupon-form" method="POST" action="<?php echo url("admin/coupon/update/{$coupon->id}") ?>">
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Code</label>
        </div>
        <div class="col-5">
            <input type="text" name="code" value="<?php echo $old["code"] ?? $coupon->code ?>" class="form-control">
            <?php if(isset($errors['code'])): ?>
                <?php foreach($errors['code'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Mức giảm</label>
        </div>
        <div class="col-5">
            <input type="number" name="amount" value="<?php echo $old["amount"] ?? $coupon->amount ?>" class="form-control">
            <?php if(isset($errors['amount'])): ?>
                <?php foreach($errors['amount'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Đơn hàng tối thiểu</label>
        </div>
        <div class="col-5">
            <input type="number" name="minimum_spend" value="<?php echo $old["minimum_spend"] ?? $coupon->minimum_spend ?>" class="form-control">
            <?php if(isset($errors['minimum_spend'])): ?>
                <?php foreach($errors['minimum_spend'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Lượt dùng</label>
        </div>
        <div class="col-5">
            <input type="number" name="usage" value="<?php echo $old["usage"] ?? $coupon->coupon_usage ?>" class="form-control">
            <?php if(isset($errors['usage'])): ?>
                <?php foreach($errors['usage'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Lượt sử dụng/người mua</label>
        </div>
        <div class="col-5">
            <input type="number" name="per_user" value="<?php echo $old["per_user"] ?? $coupon->per_user ?>" class="form-control">
            <?php if(isset($errors['per_user'])): ?>
                <?php foreach($errors['per_user'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Đã dùng</label>
        </div>
        <div class="col-5">
            <input type="number" name="used" value="<?php echo $old["used"] ?? $coupon->coupon_used ?>" class="form-control">
            <?php if(isset($errors['used'])): ?>
                <?php foreach($errors['used'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Thời gian bắt đầu</label>
        </div>
        <div class="col-5">
            <input type="text" name="start_time" value="<?php echo DateTime::createFromFormat('Y-m-d H:i:s', $coupon->start_time)->format("d-m-Y H:i"); ?>" class="form-control coupon-time">
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Thời gian kết thúc</label>
        </div>
        <div class="col-5">
            <input type="text" name="end_time" value="<?php echo DateTime::createFromFormat('Y-m-d H:i:s', $coupon->end_time)->format("d-m-Y H:i"); ?>" class="form-control coupon-time">
        </div>
    </div> 
    <div class="row">
        <button class="btn btn-primary mr-2">Update</button>
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
</form>
<script>
    const update_coupon_url = "<?php echo url("admin/coupon/update/{$coupon->id}") ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>