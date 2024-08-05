<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<style>
    .error {
        margin-top: 10px;
        margin-bottom: 0; 
    }
</style>
<?php if($user): ?>
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
<h1 class="heading">Cập nhật người dùng</h1>
<form id="update-user-form" action="<?php echo url("admin/user/update/{$user->id}") ?>" method="post">
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Tên</label>
        </div>
        <div class="col-5">
            <input type="text" name="name" value="<?php echo $old['name'] ?? $user->name ?>" class="form-control">
            <?php if(isset($errors['name'])): ?>
                <?php foreach($errors['name'] as $message): ?>
                    <p class="error"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Mã đăng nhập</label>
        </div>
        <div class="col-5">
            <input type="text" name="login_key" value="<?php echo $old['login_key'] ?? $user->login_key ?>" class="form-control">
            <?php if(isset($errors['login_key'])): ?>
                <?php foreach($errors['login_key'] as $message): ?>
                    <p class="error"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Email</label>
        </div>
        <div class="col-5">
            <?php if($user->role === "administrator" && $user->email_verified_at === null): ?>
                <div><?php echo $user->email ?></div>
            <?php else: ?>
                <input type="text" name="email" value="<?php echo $old['email'] ?? $user->email ?>" class="form-control">
                <?php if(isset($errors['email'])): ?>
                    <?php foreach($errors['email'] as $message): ?>
                        <p class="error"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>    
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Mật khẩu</label>
        </div>
        <div class="col-5">
            <button type="button" id="newpass-input" class="btn btn-secondary">Tạo mật khẩu mới</button>
            <?php if(isset($errors['password'])): ?>
                <?php foreach($errors['password'] as $message): ?>
                    <button id="remove-newpass" class="btn btn-secondary ml-2">Bỏ</button>
                    <div class="position-relative mt-2">
                        <input type="password" name="password" class="form-control">
                        <svg style="right:10px" width="20" height="20" fill="currentColor" class="bi bi-eye-fill eye-icon position-absolute" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>
                    </div>
                    <p class="error"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-2">
            <label>Vai trò</label>
        </div>
        <div class="col-5">
            <?php if($user->role === "administrator" && $user->email_verified_at === null): ?>
                <div><?php echo $user->role ?></div>
            <?php else: ?>
                <select id="role" <?php echo $user->role ? "name='role'" : "" ?> class="form-select form-control" aria-label="Default select example">
                    <?php foreach($roles as $r): ?>
                        <option <?php echo $user->role_id == $r->id ? "selected" : "" ?> value="<?php echo $r->id; ?>"><?php echo $r->name; ?></option>
                    <?php endforeach; ?>
                    <option <?php echo $user->role == null ? "selected" : "" ?> value="user">user</option>
                </select>
            <?php endif; ?>    
        </div>
    </div>
    <div class="row">
        <button class="btn btn-primary mr-2">Cập nhật người dùng</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
</form>
<script>
    const add_user = true;
</script>
<?php endif; ?>    
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>