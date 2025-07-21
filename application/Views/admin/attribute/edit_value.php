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
<?php if($value): ?>
<div class="heading">Sửa giá trị - Thuộc tính: <?php echo $attribute->name; ?></div>
<form id="update-cat-form" method="POST" action="<?php echo url("admin/attribute/{$attribute->id}/value/{$value->id}/update") ?>">
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Giá trị</label>
        </div>
        <div class="col-5">
            <input type="text" name="value" value="<?php echo $old["value"] ?? $value->value ?>" class="form-control">
            <?php if(isset($errors['value'])): ?>
                <?php foreach($errors['value'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <button class="btn btn-primary mr-2">Cập nhật giá trị</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
</form>
<?php endif; ?>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>