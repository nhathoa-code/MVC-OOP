<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<?php
    if(isset($message["success"])) :
?>
<script>
    notif({
        msg:"<?php echo $message['success']; ?>",
        type:"success",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 10000,
        animation:'slide'
    });
</script>
<?php endif; ?>
<div class="heading">Sửa cửa hàng</div>
<form class="col-6" method="POST" action="<?php echo url("admin/store/update/$store->id") ?>">
    <div class="mb-3">
        <label class="form-label">Tên cửa hàng</label>
        <input type="text" name="name" value="<?php echo $old["name"] ?? $store->name ?>" class="form-control">
         <?php if(isset($errors['name'])): ?>
            <?php foreach($errors['name'] as $message): ?>
                <p class="error mt-1"><?php echo $message; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Tỉnh/thành</label>
        <select name="province_id" class="form-control">
            <?php foreach($provinces as $item): ?>
                <option <?php echo $store->province_id == $item->id ? "selected" : "" ?> value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
            <?php endforeach; ?>
        </select>
        <?php if(isset($errors['province_id'])): ?>
            <?php foreach($errors['province_id'] as $message): ?>
                <p class="error mt-1"><?php echo $message; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Quận/huyện</label>
        <select name="district_id" class="form-control">
            <?php foreach($districts as $item): ?>
                <option <?php echo $store->district_id == $item->id ? "selected" : "" ?> value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
            <?php endforeach; ?>
        </select>
        <?php if(isset($errors['district_id'])): ?>
            <?php foreach($errors['district_id'] as $message): ?>
                <p class="error mt-1"><?php echo $message; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Địa chỉ</label>
        <input type="text" name="address" value="<?php echo $old["address"] ?? $store->address ?>" class="form-control">
        <?php if(isset($errors['address'])): ?>
            <?php foreach($errors['address'] as $message): ?>
                <p class="error mt-1"><?php echo $message; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Tọa độ</label>
        <textarea name="coordinates" cols="30" rows="5" class="form-control" spellcheck="false"><?php echo $old["coordinates"] ?? $store->coordinates ?></textarea>
        <?php if(isset($errors['coordinates'])): ?>
            <?php foreach($errors['coordinates'] as $message): ?>
                <p class="error mt-1"><?php echo $message; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="d-flex align-items-center">
        <button type="submit" class="btn btn-primary mr-2">Sữa cửa hàng</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                              
</form>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>