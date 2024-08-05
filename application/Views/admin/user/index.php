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
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="<?php echo url("admin/user") ?>">Tất cả<?php echo $number_map['all'] > 0 ? " ({$number_map['all']})" : "" ?></a> | <a href="<?php echo url("admin/user?role=administrator") ?>">Admin<?php echo $number_map['admin'] > 0 ? " ({$number_map['admin']})" : "" ?></a> | <a href="<?php echo url("admin/user?role=cashier") ?>">Cashier<?php echo $number_map['admin'] > 0 ? " ({$number_map['admin']})" : "" ?></a> | <a href="<?php echo url("admin/user?role=user") ?>">User<?php echo $number_map['user'] > 0 ? " ({$number_map['user']})" : "" ?></a> | <a href="<?php echo url("admin/user?unverified=true") ?>">Unverified users<?php echo $number_map['unverified'] > 0 ? " ({$number_map['unverified']})" : "" ?></a>
    </div>
    <div>
        <form>
            <?php if(get_query("role")): ?>
                <input type="hidden" name="role" value="<?php echo get_query("role"); ?>">
            <?php endif; ?>    
            <?php if(get_query("unverified")): ?>
                <input type="hidden" name="unverified" value="<?php echo get_query("unverified"); ?>">
            <?php endif; ?>    
            <input value="<?php echo get_query("keyword") ?? "" ?>" type="text" name="keyword" class="form-control" placeholder="Nhập tên, email">
        </form>
    </div>
</div>
<table id="product-table" class="table bg-white">
    <thead>
        <tr>
            <th style="width:25%" scope="col">Tên</th>
            <th scope="col">Email</th>
            <th scope="col">Mã đăng nhập</th>
            <th scope="col">Vai trò</th>
            <th scope="col">Email verified at</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $u): ?>
            <tr>
                <td>
                    <?php echo $u->name ?>
                    <div class="row-actions">
                        <span class="edit"><a href="<?php echo url("admin/user/edit/{$u->id}") ?>">Sửa</a></span> | <span class="edit"><a href="<?php echo url("admin/user/profile/{$u->id}") ?>">Xem</a></span><?php if($u->email_verified_at !== null || ($u->email_verified_at === null && $u->role_name !== "administrator")): ?> |<form class="delete-form" action="<?php echo url("admin/user/delete/{$u->id}") ?>" method="post" style="display: inline;" onsubmit="return window.confirm('Bạn có chắc muốn xóa user này không?')" href="<?php echo url("admin/user/delete/{$u->id}") ?>"><button type="submit">Xóa</button><input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>"></form><?php endif; ?>
                    </div>
                </td>
                <td><?php echo $u->email ?></td>
                <td><?php echo $u->login_key ?></td>
                <td><?php echo $u->role ?? "user" ?></td>
                <td><?php echo $u->email_verified_at ?></td>
            </tr>
        <?php endforeach; ?>    
    </tbody>
</table>
<?php echo pagination($totalPages,$currentPage); ?>          
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>