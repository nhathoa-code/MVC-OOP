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
    <div class="col-12">
        <table id="cat-table" class="table table-borderless position-relative">
            <thead>
                <tr>
                    <th style="width:40%" scope="col">Tên vai trò</th>
                    <th scope="col">Quyền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($roles as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item->name; ?>
                            <div class="row-actions">
                                <span class="delete"><a href="javascript:void(0)" onclick="deleteRole(<?php echo $item->id ?>)">Xóa</a><form id="delete-role-<?php echo $item->id ?>" style="display:none" method="POST" action="<?php echo url("admin/role/delete/{$item->id}") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                <span class="edit"><a href="<?php echo url("admin/role/edit/{$item->id}") ?>">Sửa</a></span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <?php
                                    echo join(", ",array_map(function($item){
                                        return $item->name;
                                    },$item->permissions));
                                ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex align-items-center">
            <a href="<?php echo url("admin/role/add") ?>" class="btn btn-primary mr-2">Thêm vai trò</a>
        </div>
    </div>
</div>       
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>