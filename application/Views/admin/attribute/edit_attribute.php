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
<div class="heading">Sửa thuộc tính</div>
<form id="update-cat-form" method="POST" action="<?php echo url("admin/attribute/update/{$attribute->id}") ?>">
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Tên thuộc tính</label>
        </div>
        <div class="col-5">
            <input type="text" name="name" value="<?php echo $old["name"] ?? $attribute->name ?>" class="form-control">
            <?php if(isset($errors['name'])): ?>
                <?php foreach($errors['name'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <label class="form-label">Sử dụng cho danh mục</label>
        </div>
        <div class="col-5">
            <div style="max-height:300px;overflow-y:scroll">
                <?php 
                    $forCats = $attribute->getForCats(); 
                    displayCategoriesOptions($categories, 0, $forCats); 
                ?>
            </div>
            <?php if(isset($errors['cats'])): ?>
                <?php foreach($errors['cats'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <button class="btn btn-primary mr-2">Cập nhật thuộc tính</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
</form>
<?php
    function displayCategoriesOptions($categories, $indent = 0, $forCats)
    {
        foreach($categories as $cat)
        { ?>
            <ul style="padding-left: <?php echo $indent > 0 ? 20 * $indent . 'px' : '0' ?>">
                <li>   
                    <label><input id="<?php echo $cat->id ?>" <?php echo in_array($cat->id,$forCats) ? 'checked' : '' ?> type="checkbox" name="cats[]" value="<?php echo $cat->id; ?>"> <?php echo $cat->cat_name; ?></label>
                </li>
                <?php
                    if($cat->children){
                        displayCategoriesOptions($cat->children, $indent + 1, $forCats);
                    }
                ?>
            </ul>
    <?php }
    }
?>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>