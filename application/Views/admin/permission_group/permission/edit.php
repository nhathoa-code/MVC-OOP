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
        timeout: 5000,
        animation:'slide'
    });
</script>
<?php endif; ?>  
<div class="heading">Nhóm quyền: <?php echo $permission_group->name; ?></div>
<div class="row">
    <div class="col-4">
        <form method="POST" action="<?php echo url("admin/permission-group/{$permission_group->id}/permission/update/{$permission->id}") ?>">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Tên quyền</label>
                <input type="text" name="name" value="<?php echo $old["name"] ?? $permission->name ?>" class="form-control">
                <?php if(isset($errors['name'])): ?>
                    <?php foreach($errors['name'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Sửa quyền</button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                              
        </form>
    </div>
</div>       
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>