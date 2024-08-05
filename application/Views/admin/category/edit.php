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
<div class="heading">Sửa danh mục</div>
<form id="update-cat-form" method="POST" enctype="multipart/form-data" action="<?php echo url("admin/category/update/{$category->id}") ?>">
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Name</label>
        </div>
        <div class="col-5">
            <input type="text" name="cat_name" value="<?php echo $old["cat_name"] ?? $category->cat_name ?>" class="form-control">
            <?php if(isset($errors['cat_name'])): ?>
                <?php foreach($errors['cat_name'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Slug</label>
        </div>
        <div class="col-5">
            <input type="text" name="cat_slug" value="<?php echo $old["cat_slug"] ?? $category->cat_slug ?>" class="form-control">
            <?php if(isset($errors['cat_slug'])): ?>
                <?php foreach($errors['cat_slug'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Parent Category</label>
        </div>
        <div class="col-5">
            <select name="parent_id" class="form-select form-control" aria-label="Default select example">
                <option selected value="">none</option>
                <?php
                    function displayParentOptions($categories, $indent, $category,$old)
                    {
                        foreach($categories as $cat)
                        { ?>
                            <option <?php echo ($old["parent_id"] ?? $category->parent_id) == $cat->id ? "selected" : "" ?> id="<?php echo "parent-id-{$cat->id}" ?>" value="<?php echo $cat->id ?>" data-level="<?php echo $indent ?>"><?php echo str_repeat("— ",$indent) . $cat->cat_name ?></option>
                            <?php 
                                if($cat->children)
                                {
                                    displayParentOptions($cat->children, $indent + 1, $category,$old);
                                }
                            ?>
                    <?php }
                    }
                    ?>
                <?php displayParentOptions($categories, 0, $category,$old); ?>
            </select>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <label for="">Hình ảnh</label>
        </div>
        <div class="col-5">
            <input style="display:none" id="file-input" type="file" name="cat_image">
            <div style="display: flex;">
                <div class="image-upload-container image-upload">
                    <div class="image-upload-icon" style="width: 30px;height:30px">
                        <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                    </div>
                </div>
                <?php if($category->cat_image): ?>
                    <div class="img-wrap image-container">
                        <img src="<?php echo url($category->cat_image) ?>" class="preview-image" style="max-width: 80px; max-height: 80px;float:right" />
                        <div class="image-delete">
                            <i onclick="deleteCatImage('<?php echo $category->cat_image ?>',event)" data-name="1_4550583653293_1260.jpg" data-id="1716546424998" class="delete-p-image fa fa-solid fa-trash"></i>
                        </div>
                    </div>
                <?php else: ?>   
                    <img src="" class="preview-image" style="display:none;max-width: 80px; max-height: 80px;float:right" /> 
                <?php endif; ?>
                
            </div>
             <?php if(isset($errors['cat_image'])): ?>
                <?php foreach($errors['cat_image'] as $message): ?>
                    <p class="error mt-1"><?php echo $message; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <button class="btn btn-primary mr-2">Update</button>
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
</form>
<script>
    const update_coupon_url = "<?php echo url("admin/category/update/{$category->id}") ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>