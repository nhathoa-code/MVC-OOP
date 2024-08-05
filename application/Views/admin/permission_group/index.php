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
<div class="heading">Nhóm quyền</div>
<div class="row">
    <div class="col-4">
        <form method="POST" action="<?php echo url("admin/permission-group/add") ?>">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Tên nhóm</label>
                <input type="text" name="name" value="<?php echo $old["name"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['name'])): ?>
                    <?php foreach($errors['name'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Thêm nhóm quyền</button>
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
                <?php foreach($permission_groups as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item->name; ?>
                            <div class="row-actions">
                                <span class="delete"><a href="javascript:void(0)" onclick="deletePermissionGroup(<?php echo $item->id ?>)">Xóa</a><form id="delete-permission-group-<?php echo $item->id ?>" style="display:none" method="POST" action="<?php echo url("admin/permission-group/delete/{$item->id}") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                <span class="edit"><a href="<?php echo url("admin/permission-group/edit/{$item->id}") ?>">Sửa</a></span>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo url("admin/permission-group/{$item->id}/permissions") ?>">quyền</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>       
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>