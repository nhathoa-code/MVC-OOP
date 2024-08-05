<?php
  if(isset($message["success"])) :
?>
    <script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
    <link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>">    
    <script>
    notif({
        msg:"<?php echo $message['success'] ?>",
        type: "success",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 5000,
        animation:'slide'
    });
    </script>
<?php endif; ?>
<div class="user-title">Thông tin cá nhân</div>
<div class="row">
  <div class="col-12 col-lg-6">
    <form id="update-profile-form" class="w-100" method="post" action="<?php echo url("user/profile/update"); ?>">
    <?php 
      $profile = unserialize($data['profile'] ?? "");
    ?>
    <div class="mb-3">
      <label class="form-label">Họ và tên</label>
      <input name="name" value="<?php echo $profile['name'] ?? "" ?>" type="text" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Số điện thoại</label>
      <input name="phone" value="<?php echo $profile['phone'] ?? "" ?>" type="text" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" readonly value="<?php echo getUser()->email ?>" type="text" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Ngày sinh</label>
      <input name="birth_day" value="<?php echo $profile['birth_day'] ?? "" ?>" type="text" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Tỉnh/Thành phố</label>
      <select name="province" id="province" class="form-select">
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Quận/Huyện</label>
      <select name="district" id="district" class="form-select">
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Phường/Xã</label>
      <select name="ward" id="ward" class="form-select">
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Giới tính</label>
      <div style="gap:10px" class="form-check d-flex align-items-center">
          <input style="width:13px;height:13px" class="form-check-input" id="boy" value="boy" type="radio" <?php echo isset($profile['gender']) && $profile['gender'] === "boy" ? "checked" : "" ?> name="gender">
          <label class="form-check-label" for="boy">
              Nam
          </label>
      </div>
      <div style="gap:10px" class="form-check d-flex align-items-center">
          <input style="width:13px;height:13px" class="form-check-input" id="girl" value="girl" type="radio" <?php echo isset($profile['gender']) && $profile['gender'] === "girl" ? "checked" : "" ?> name="gender">
          <label class="form-check-label" for="girl">
              Nữ
          </label>
      </div>
    </div>
    <button type="submit" class="btn btn-secondary w-100">Cập nhật</button>
    <input type="hidden" name="province_id" value="<?php echo $profile['province_id'] ?? 0 ?>">
    <input type="hidden" name="district_id" value="<?php echo $profile['district_id'] ?? 0?>">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <script>
      const load_provinces = true;
      var add = {province_id: <?php echo $profile['province_id'] ?>,district_id:<?php echo $profile['district_id'] ?>,ward_code:"<?php echo $profile['ward_code']; ?>"};
    </script>
  </form>

  </div>
</div>
