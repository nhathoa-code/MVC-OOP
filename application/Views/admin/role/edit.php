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
<div class="heading">Vai trò</div>
<div class="row">
    <div class="col-4">
        <form method="POST" action="<?php echo url("admin/role/update/{$role->id}") ?>">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Tên vai trò</label>
                <input type="text" name="name" value="<?php echo $old["name"] ?? $role->name; ?>" class="form-control">
                <?php if(isset($errors['name'])): ?>
                    <?php foreach($errors['name'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3 mt-5">
                <?php foreach($permission_groups as $item): ?>
                    <div class="d-flex" style="gap:50px">
                        <div style="font-weight: 600;" class="mr-5 col-2">
                            <?php echo $item->name ?>
                        </div>
                        <?php foreach($item->permissions as $permis): ?>
                            <div class="mb-3 form-check">
                                <input name="permissions[]" <?php echo in_array($permis->id,$role->permissions) ? "checked" : "" ?> value="<?php echo $permis->id ?>" type="checkbox" class="form-check-input" id="permission-<?php echo $permis->id ?>">
                                <label style="white-space:nowrap;" class="form-check-label" for="permission-<?php echo $permis->id ?>"><?php echo $permis->name ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Cập nhật vai trò</button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                              
        </form>
    </div>
  
</div>       
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>