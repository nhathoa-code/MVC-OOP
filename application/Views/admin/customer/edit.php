<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<?php
    if(isset($message)) :
?>
<script>
    notif({
        msg:"<?php echo isset($message['success']) ? $message["success"] : $message["error"] ?>",
        type:"<?php echo isset($message['success']) ? "success" : "warning" ?>",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 10000,
        animation:'slide'
    });
</script>
<?php endif; ?>  
<div class="heading">Sửa Khách hàng</div>
<form id="update-cat-form" method="POST" action="<?php echo url("admin/customer/update/{$customer->id}") ?>">
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Tên khách hàng</label>
        </div>
        <div class="col-5">
            <input type="text" name="name" value="<?php echo $old["name"] ?? $customer->name ?>" class="form-control">
            <?php if(isset($errors['name'])): ?>
                <?php foreach($errors['name'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Số điện thoại</label>
        </div>
        <div class="col-5">
            <input type="text" name="phone" value="<?php echo $old["phone"] ?? $customer->phone ?>" class="form-control">
            <?php if(isset($errors['phone'])): ?>
                <?php foreach($errors['phone'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <button class="btn btn-primary mr-2">Cập nhật khách hàng</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
</form>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>