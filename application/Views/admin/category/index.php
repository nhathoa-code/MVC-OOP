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
        timeout: 5000,
        animation:'slide'
    });
</script>
<?php endif; ?>  
<div class="heading">Danh mục</div>
<div class="row">
    <div class="col-4">
        <form id="add-cat-form" method="POST" action="<?php echo url("admin/category/add") ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="cat_name" value="<?php echo $old["cat_name"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['cat_name'])): ?>
                    <?php foreach($errors['cat_name'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="cat_slug" value="<?php echo $old["cat_slug"] ?? "" ?>" class="form-control">
                <?php if(isset($errors['cat_slug'])): ?>
                    <?php foreach($errors['cat_slug'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Parent</label>
                <select name="parent_id" class="form-select form-control" aria-label="Default select example">
                    <option selected value="">none</option>
                    <?php
                        function displayParentOptions($categories, $indent, $old)
                        {
                            foreach($categories as $cat)
                            { ?>
                                <option <?php echo ($old["parent_id"] ?? null) == $cat->id ? "selected" : ""?> id="<?php echo "parent-id-{$cat->id}" ?>" value="<?php echo $cat->id ?>" data-level="<?php echo $indent ?>"><?php echo str_repeat("— ",$indent) . $cat->cat_name ?></option>
                                <?php 
                                    if($cat->children)
                                    {
                                        displayParentOptions($cat->children,$indent + 1,$old);
                                    }
                                ?>
                        <?php }
                        }
                    ?>
                    <?php displayParentOptions($categories,0,$old ?? null); ?>
                </select>
                <?php if(isset($errors['parent_id'])): ?>
                    <?php foreach($errors['parent_id'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" name="featured" type="checkbox" id="featured">
                    <label class="form-check-label" for="featured">
                        Featured category
                    </label>
                </div>
            </div>
            <div class="mb-3">
                <label for="">Thêm hình ảnh</label>
                <input style="display:none" id="file-input" type="file" name="cat_image">
                <div style="display: flex;">
                    <div class="image-upload-container image-upload">
                        <div class="image-upload-icon" style="width: 30px;height:30px">
                            <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                        </div>
                    </div>
                    <img src="" class="preview-image" style="display:none;max-width: 80px; max-height: 80px;float:right" />
                </div>
                <?php if(isset($errors['cat_image'])): ?>
                    <?php foreach($errors['cat_image'] as $message): ?>
                        <p class="error mt-1"><?php echo $message; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-primary mr-2">Add New Category</button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">                              
        </form>
    </div>
    <div class="col-8">
        <table id="cat-table" class="table table-borderless position-relative">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Picture</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Count</th>
                    <th scope="col">Featured</th>
                </tr>
            </thead>
            <?php
                function displayCategories($categories, $indent = 0)
                {
                    foreach($categories as $cat)
                    { ?>
                        <tr id="<?php echo "cat-{$cat->id}" ?>">
                            <td>#<?php echo $cat->id; ?></td>
                            <td>
                                <?php echo str_repeat("— ",$indent) . $cat->cat_name ?>
                                <div class="row-actions">
                                    <span class="delete"><a href="javascript:void(0)" onclick="deleteCat(<?php echo $cat->id ?>)">Xóa</a><form id="delete-cat-<?php echo $cat->id ?>" style="display:none" method="POST" action="<?php echo url("admin/category/delete/{$cat->id}") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                    <span class="edit"><a href="<?php echo url("admin/category/edit/{$cat->id}") ?>">Sửa</a></span>
                                </div>
                            </td>
                            <td>
                                <?php if($cat->cat_image): ?>
                                    <img style="width: 50px;" src="<?php echo url($cat->cat_image) ?>" alt="">
                                <?php else: ?>
                                    <div style="width:50px;height:50px"></div>
                                <?php endif; ?>
                                
                            </td>
                            <td><?php echo $cat->cat_slug ?></td>
                            <td><?php echo $cat->products; ?></td>
                            <td><?php echo $cat->featured ? 'featured' : ''; ?></td>
                        </tr>
                        <?php
                            if($cat->children){
                                displayCategories($cat->children, $indent + 1);
                            }
                        ?>
                <?php }
                }
            ?>
            <tbody>
                <?php displayCategories($categories); ?>
            </tbody>
        </table>
    </div>
</div>       
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>