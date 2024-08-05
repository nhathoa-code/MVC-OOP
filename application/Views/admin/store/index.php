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
<div class="heading">Cửa hàng</div>
<div class="row">
    <div class="col-4">
        <form method="POST" action="<?php echo url("admin/store/add") ?>">
            <div class="mb-3">
                <label class="form-label">Tên cửa hàng</label>
                <input type="text" name="name" value="<?php echo $old["name"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['name'])): ?>
                    <?php foreach($errors['name'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Tỉnh/thành</label>
                <select name="province_id" class="form-control">
                    <option disabled selected>Tỉnh/thành</option>
                    <?php foreach($provinces as $item): ?>
                        <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
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
                        <option disabled selected>Quận/huyện</option>
                </select>
                <?php if(isset($errors['district_id'])): ?>
                    <?php foreach($errors['district_id'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="address" value="<?php echo $old["address"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['address'])): ?>
                    <?php foreach($errors['address'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Tọa độ</label>
                <textarea name="coordinates" cols="30" rows="5" class="form-control" spellcheck="false"><?php echo $old["coordinates"] ?? "" ?></textarea>
                <?php if(isset($errors['coordinates'])): ?>
                    <?php foreach($errors['coordinates'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button type="submit" class="btn btn-primary mr-2">Thêm cửa hàng</button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                              
        </form>
    </div>
   
    <div class="col-8">
        <table class="table table-borderless position-relative">
            <thead>
                <tr>
                    <th scope="col">Tên cửa hàng</th>
                    <th scope="col" style="width:40%">Địa chỉ</th>
                    <th scope="col">Tọa độ</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($stores as $store): ?>
                    <tr id="<?php echo "store-{$store->id}" ?>">
                        <td>
                            <?php echo $store->name ?>
                            <div class="row-actions">
                                <span data-route="<?php echo url("admin/store/delete/{$store->id}") ?>" class="delete"><a href="javascript:void(0)" onclick="deleteStore(<?php echo $store->id ?>)">Xóa</a><form id="delete-store-<?php echo $store->id ?>" style="display:none" action="<?php echo url("admin/store/delete/$store->id") ?>" method="POST"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                <span class="edit"><a href="<?php echo url("admin/store/edit/{$store->id}") ?>">Sửa</a></span>
                            </div>
                        </td>
                        <td><?php echo "{$store->address}, {$store->district}, {$store->province}"; ?></td>
                        <td>
                            <a target="_blank" href="<?php echo $store->coordinates ?>">Tọa độ</a>
                        </td>
                        <td>
                            <div class="row-actions d-block">
                                <a href="<?php echo url("admin/store/{$store->id}/inventory/add") ?>">Nhập kho</a>
                                <br>
                                <a href="<?php echo url("admin/store/{$store->id}/inventory") ?>">Duyệt kho</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    var provinces = <?php echo json_encode($provinces); ?>;
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>