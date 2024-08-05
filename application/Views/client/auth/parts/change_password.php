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
<div class="user-title">Đổi mật khẩu</div> 
<div class="row">
    <div class="col-12 col-lg-6">
    <form id="update-profile-form" class="w-100" method="post" action="<?php echo url("user/password/update"); ?>">
      <div class="mb-3">
        <label class="form-label">Mật khẩu hiện tại<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
        <div class="position-relative">
            <input type="password" class="form-control" placeholder="Nhập mật khẩu hiện tại" name="oldpass">
            <div style="<?php echo isMobileDevice() ? "width:15%;right: -5px;" : "right:5px" ?>" class="position-absolute eye-icon-wrap">
                <svg width="20" height="20" fill="currentColor" class="bi bi-eye-fill eye-icon" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                </svg>
            </div>
        </div>
      </div>
      <?php if(isset($errors['oldpass'])): ?>
          <?php foreach($errors['oldpass'] as $message): ?>
              <p class="error px-0"><?php echo $message; ?></p>
          <?php endforeach; ?>
      <?php endif; ?>
      <div class="mb-3">
        <label class="form-label">Mật khẩu mới<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
        <div class="position-relative">
            <input type="password" class="form-control" placeholder="Nhập mật khẩu mới" name="newpass">
            <div style="<?php echo isMobileDevice() ? "width:15%;right: -5px;" : "right:5px" ?>" class="position-absolute eye-icon-wrap">
                <svg width="20" height="20" fill="currentColor" class="bi bi-eye-fill eye-icon" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                </svg>
            </div>
        </div>
      </div>
      <?php if(isset($errors['newpass'])): ?>
        <?php foreach($errors['newpass'] as $message): ?>
            <p class="error px-0"><?php echo $message; ?></p>
        <?php endforeach; ?>
      <?php endif; ?>
      <div class="mb-3">
        <label class="form-label">Nhập lại mật khẩu mới<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
        <div class="position-relative">
            <input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới" name="retype_newpass">
            <div style="<?php echo isMobileDevice() ? "width:15%;right: -5px;" : "right:5px" ?>" class="position-absolute eye-icon-wrap">
                <svg width="20" height="20" fill="currentColor" class="bi bi-eye-fill eye-icon" viewBox="0 0 16 16">
                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                </svg>
            </div>
        </div>
      </div>
      <?php if(isset($errors['retype_newpass'])): ?>
          <?php foreach($errors['retype_newpass'] as $message): ?>
              <p class="error px-0"><?php echo $message; ?></p>
          <?php endforeach; ?>
      <?php endif; ?>
      <button type="submit" class="btn btn-secondary w-100">Cập nhật</button>
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    </form>
    </div>
</div>

