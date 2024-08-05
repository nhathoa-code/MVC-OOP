<?php include(APP_PATH . "/application/Views/client/header.php") ?>
<?php
  if(isset($message)) :
?>
<script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
<link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>">    
<script>
    notif({
        msg:"<?php echo isset($message['error']) ? $message['error'] : $message['success'] ?>",
        type: "<?php echo isset($message['error']) ? "warning" : "success" ?>",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 5000,
        animation:'slide'
    });
</script>
<?php endif; ?>
<section id="content">
    <div class="container-fluid">
        <form style="margin:0 auto;width:50%" class="mt-3" id="login-form" method="post" action="<?php echo url("auth/login"); ?>">
            <div class="p-3">
                <h1>ĐĂNG NHẬP</h1>
                <div class="row mx-0 my-2">
                    <div class="col-12 mb-3 p-0">
                        <label class="form-label">Email<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                        <input type="text" class="form-control" placeholder="Nhập email" name="login_key">
                    </div>
                    <?php if(isset($errors['login_key'])): ?>
                        <?php foreach($errors['login_key'] as $message): ?>
                            <p class="error px-0"><?php echo $message; ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="col-12 mb-3 p-0">
                        <label class="form-label">Mật khẩu<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                        <div class="position-relative">
                            <input type="password" class="form-control" placeholder="Nhập mật khẩu" name="password">
                            <div style="<?php echo isMobileDevice() ? "width:15%;right: -5px;" : "right:5px" ?>" class="position-absolute eye-icon-wrap">
                                <svg width="20" height="20" fill="currentColor" class="bi bi-eye-fill eye-icon" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($errors) && isset($errors['password'])): ?>
                        <?php foreach($errors['password'] as $message): ?>
                            <p class="error px-0"><?php echo $message; ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if(isset($message) && isset($message['message'])): ?>
                    <p class="error"><?php echo $message['message']; ?></p>
                <?php endif; ?>
                <div class="row m-0">
                    <div style="font-size: 14px;" class="col px-0">
                        Chưa có tài khoản ? <a href="<?php echo url("auth/registry"); ?>">Đăng ký ngay</a>
                    </div>
                </div>
                <div style="font-size: 14px;" class="col px-0">
                    Bạn quên mật khẩu ? <a href="<?php echo url("auth/forgotpassword"); ?>">Lấy lại mật khẩu</a>
                </div>
                <div class="row mx-0 mt-3">
                    <button class="btn btn-secondary p-2" type="submit">ĐĂNG NHẬP</button>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        </form>
    </div>
</section>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>