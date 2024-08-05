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
        <form method="POST" action="<?php echo url("admin/permission-group/{$permission_group->id}/permission/add") ?>">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Tên quyền</label>
                <input type="text" name="name" value="<?php echo $old["name"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['name'])): ?>
                    <?php foreach($errors['name'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Thêm quyền</button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                              
        </form>
    </div>
    <div class="col-8">
        <table id="cat-table" class="table table-borderless position-relative">
            <thead>
                <tr>
                    <th style="width:40%" scope="col">Tên</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($permission_group->getPermissions() as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item->name; ?>
                        </td>
                        <td>
                            <span class="delete"><a style="color:#b32d2e" href="javascript:void(0)" onclick="deletePermission(<?php echo $item->id ?>)">Xóa</a><form id="delete-permission-<?php echo $item->id ?>" style="display:none" method="POST" action="<?php echo url("admin/permission-group/{$permission_group->id}/permission/delete/{$item->id}") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                            <span class="edit"><a href="<?php echo url("admin/permission-group/{$permission_group->id}/permission/edit/{$item->id}") ?>">Sửa</a></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>       
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>