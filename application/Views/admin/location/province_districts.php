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
        timeout: 5000,
        animation:'slide'
    });
</script>
<?php endif; ?>  
<h1 class="heading">Tỉnh/thành: <?php echo $province->name; ?></h1>
<div class="row">
    <div class="col-4">
        <form method="POST" action="<?php echo url("admin/location/province/{$province->id}/district/add") ?>">
            <div class="mb-3">
                <label class="form-label">Tên quận/huyện</label>
                <input type="text" name="name" value="<?php echo $old["name"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['name'])): ?>
                    <?php foreach($errors['name'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Thêm quận/huyện</button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                                     
        </form>
    </div>
    <div class="col-8">
        <div class="table-responsive">
            <table id="coupon-table" class="table table-borderless table-striped-columns ">
            <thead>
                <tr>
                    <th style="width:30%" scope="col">Mã ID</th>
                    <th scope="col">Tên quận/huyện</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($districts as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item->id ?>
                            <div class="row-actions">
                                <span class="delete"><a href="javascript:void(0)" onclick="deleteDistrict(<?php echo $item->id ?>)">Xóa</a><form id="delete-district-<?php echo $item->id ?>" style="display:none" method="POST" action="<?php echo url("admin/location/province/{$province->id}/district/{$item->id}/delete") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                <span class="edit"><a href="<?php echo url("admin/location/province/{$province->id}/district/{$item->id}/edit") ?>">Sửa</a></span>
                            </div>
                        </td>
                        <td><?php echo $item->name; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        
    </div>
</div>
<script>
    const add_coupon_url = "<?php echo url("admin/coupon/add"); ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>