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
<h1 class="heading">Thuộc tính: <?php echo $attribute->name; ?></h1>
<div class="row">
    <div class="col-4">
        <form method="POST" action="<?php echo url("admin/attribute/{$attribute->id}/value/add") ?>">
            <div class="mb-3">
                <label class="form-label">Giá trị</label>
                <input type="text" name="value" value="<?php echo $old["value"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['value'])): ?>
                    <?php foreach($errors['value'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Thêm giá trị</button>
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
                        <th scope="col">Giá trị</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attribute->getValues() as $item): ?>
                        <tr>
                            <td>
                                <?php echo $item->id ?>
                                <div class="row-actions">
                                    <span class="delete"><a href="javascript:void(0)" onclick="deleteAttributeValue(<?php echo $item->id ?>)">Xóa</a><form id="delete-attribute-value-<?php echo $item->id ?>" style="display:none" method="POST" action="<?php echo url("admin/attribute/{$attribute->id}/value/{$item->id}/delete") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                    <span class="edit"><a href="<?php echo url("admin/attribute/{$attribute->id}/value/{$item->id}/edit") ?>">Sửa</a></span>
                                </div>
                            </td>
                            <td><?php echo $item->value; ?></td>
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