<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<style>
    .attribute > label{
        margin-right: 10px;
    }
</style>
<form style="position:relative" id="add-product-form" action="<?php echo url("admin/product/add"); ?>">                     
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Tên sản phẩm
                </div>
                <div class="col-10">
                    <input spellcheck="false" type="text" name="p_name" placeholder="nhập tên sản phẩm" class="form-control">
                </div>
            </div>
            <div class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Mô tả sản phẩm
                </div>
                <div class="col-10">
                    <textarea spellcheck="false" name="p_desc" class="form-control" placeholder="nhập mô tả sản phẩm" rows="4"></textarea>
                </div>
            </div>
            <div class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Danh mục
                </div>
                <div class="col-10">
                    <div id="categories" data-toggle="modal" data-target="#categoriesModal">
                        <span class="placeholder">chọn danh mục</span>
                    </div>
                </div>
            </div>
            <!-- <div class="row align-items-start input-row">
                <div class="col-2 input-label">
                    Thuộc tính
                </div>
                <div class="col-10">
                    <div class="attribute">
                        <span style="font-weight:bold" class="mr-2">Chất liệu:</span>
                        <label><input type="radio" name="attribute_values"> Cotton</label>
                        <label><input type="radio" name="attribute_values"> Linen</label>
                        <label><input type="radio" name="attribute_values"> GA</label>
                    </div>
                </div>
            </div> -->
            <div class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Bảng kích cỡ
                </div>
                <div class="col-10">
                    <select name="size_chart" class="form-control">
                        <option value="">none</option>
                        <?php foreach($size_charts as $item): ?>
                            <option value="<?php echo $item->id ?>"><?php echo $item->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <script>
                    $(document).ready(function() {
                        $('select[name=size_chart]').select2();
                    });
                </script>
            </div>
            <div id="images-row" class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Ảnh sản phẩm
                </div>
                <div class="col-10 d-flex flex-wrap">
                    <input style="display:none" id="files-input" type="file" multiple>
                    <div class="d-flex flex-wrap" id="images-preview">
                        
                        <div id="upload-icon-wrap">
                            <div style="border-radius: 4px;" class="image-upload-container image-upload">
                                <div class="image-upload-icon" style="width: 30px;height:30px">
                                    <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="p-price-stock-row" class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Giá sản phẩm
                </div>
                <div style="align-self: flex-start;" id="p-price-container" class="col-4">
                    <input type="text" name="p_price" class="form-control">            
                </div>
                
                <div style="align-self: flex-start;" class="col-6 row align-items-center pr-0">
                    <div class="col-4 input-label">
                        Kho hàng
                    </div>
                    <div id="p-price-container" class="col-8" style="padding-right:0">
                        <input type="text" name="p_stock" class="form-control">
                    </div>
                </div>
            </div>              
            <div class="row input-row">
                <div class="col-2 input-label">
                    Phân loại hàng
                </div>
                <div id="product-variation" class="col-10">
                    <div id="variation-wrap" class="d-flex">
                        <div id="add-color-variation" class="variation">
                            <svg class="plus-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8.48176704,1.5 C8.75790942,1.5 8.98176704,1.72385763 8.98176704,2 L8.981,7.997 L15,7.99797574 C15.2761424,7.99797574 15.5,8.22183336 15.5,8.49797574 C15.5,8.77411811 15.2761424,8.99797574 15,8.99797574 L8.981,8.997 L8.98176704,15 C8.98176704,15.2761424 8.75790942,15.5 8.48176704,15.5 C8.20562467,15.5 7.98176704,15.2761424 7.98176704,15 L7.981,8.997 L2,8.99797574 C1.72385763,8.99797574 1.5,8.77411811 1.5,8.49797574 C1.5,8.22183336 1.72385763,7.99797574 2,7.99797574 L7.981,7.997 L7.98176704,2 C7.98176704,1.72385763 8.20562467,1.5 8.48176704,1.5 Z"></path></svg>
                            <span style="color:#2271b1;font-size:0.9rem;margin-left:5px">Thêm phân loại màu sắc</span>
                        </div>
                        <div id="add-size-variation" class="variation" style="margin-left:10px">
                            <svg class="plus-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8.48176704,1.5 C8.75790942,1.5 8.98176704,1.72385763 8.98176704,2 L8.981,7.997 L15,7.99797574 C15.2761424,7.99797574 15.5,8.22183336 15.5,8.49797574 C15.5,8.77411811 15.2761424,8.99797574 15,8.99797574 L8.981,8.997 L8.98176704,15 C8.98176704,15.2761424 8.75790942,15.5 8.48176704,15.5 C8.20562467,15.5 7.98176704,15.2761424 7.98176704,15 L7.981,8.997 L2,8.99797574 C1.72385763,8.99797574 1.5,8.77411811 1.5,8.49797574 C1.5,8.22183336 1.72385763,7.99797574 2,7.99797574 L7.981,7.997 L7.98176704,2 C7.98176704,1.72385763 8.20562467,1.5 8.48176704,1.5 Z"></path></svg>
                            <span style="color:#2271b1;font-size:0.9rem;margin-left:5px">Thêm phân loại kích cỡ</span>
                        </div>
                    </div>
                </div>
            </div>
            <div style="position: sticky;bottom:0" class="row align-items-center input-row">
                <div class="offset-2 col-10">
                    <div>
                        <button class="btn btn-primary py-2 w-100">Lưu</button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div id="overlay" class="overlay">
        <div class="spinner">
            <svg
                width="44"
                height="44"
                viewBox="0 0 24 24"
                >
                    <g>
                        <rect width="1.5" height="5" x="11" y="1" fill="currentColor" opacity=".14" />
                        <rect
                        width="1.5"
                        height="5"
                        x="11"
                        y="1"
                        fill="currentColor"
                        opacity=".29"
                        transform="rotate(30 12 12)"
                        />
                        <rect
                        width="1.5"
                        height="5"
                        x="11"
                        y="1"
                        fill="currentColor"
                        opacity=".43"
                        transform="rotate(60 12 12)"
                        />
                        <rect
                        width="1.5"
                        height="5"
                        x="11"
                        y="1"
                        fill="currentColor"
                        opacity=".57"
                        transform="rotate(90 12 12)"
                        />
                        <rect
                        width="1.5"
                        height="5"
                        x="11"
                        y="1"
                        fill="currentColor"
                        opacity=".71"
                        transform="rotate(120 12 12)"
                        />
                        <rect
                        width="1.5"
                        height="5"
                        x="11"
                        y="1"
                        fill="currentColor"
                        opacity=".86"
                        transform="rotate(150 12 12)"
                        />
                        <rect
                        width="1.5"
                        height="5"
                        x="11"
                        y="1"
                        fill="currentColor"
                        transform="rotate(180 12 12)"
                        />
                        <animateTransform
                        attributeName="transform"
                        calcMode="discrete"
                        dur="0.75s"
                        repeatCount="indefinite"
                        type="rotate"
                        values="0 12 12;30 12 12;60 12 12;90 12 12;120 12 12;150 12 12;180 12 12;210 12 12;240 12 12;270 12 12;300 12 12;330 12 12;360 12 12"
                        />
                    </g>
            </svg>
        </div>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
</form>
<div class="modal" id="categoriesModal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <div id="categories-container" class="d-flex">
            </div>
        </div>
        <div class="modal-footer">
            <div id="picked-categories" style="flex:1;font-weight:700"></div>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            <button id="categories-confirm" type="button" class="btn btn-primary">Xác nhận</button>
        </div>
    </div>
</div>
<script>
    const categories = <?php echo json_encode($categories) ?>;
    const loading_icon_url = "<?php echo url("loading-icon.svg") ?>";
    const admin_url = "<?php echo url("admin") ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>