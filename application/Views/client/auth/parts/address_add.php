<div class="user-title">Thêm địa chỉ</div>
<div class="row">
  <div class="col-12 col-lg-6">
    <form id="add-address-form" class="w-100" method="post" action="<?php echo url("user/address/add") ?>">
      <div class="mb-3">
        <label class="form-label">Họ và tên</label>
        <input name="name" type="text" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Số điện thoại</label>
        <input name="phone" type="text" class="form-control">
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
        <label class="form-label">Địa chỉ</label>
        <input name="address" type="text" class="form-control">
      </div>
      <div class="form-check mb-3">
        <input style="border-color:rgb(153, 153, 153)" class="form-check-input" type="checkbox" name="default" id="defaultChecked">
        <label class="form-check-label" for="defaultChecked">
          Địa chỉ mặc định
        </label>
      </div>
      <button type="submit" class="btn btn-secondary w-100">Thêm địa chỉ</button>
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    </form>
  </div>
</div>
