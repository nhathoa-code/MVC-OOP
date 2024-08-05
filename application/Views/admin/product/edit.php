<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
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
        </div>
        <script>
            const request_url = "<?php echo url("admin/product/update/{$product->id}"); ?>";
            const delete_url = "<?php echo url("admin/product/delete/{$product->id}"); ?>";
            var picked_categories = <?php echo json_encode($product->categories); ?>;
            const categories = <?php echo json_encode($categories) ?>;
            const back_url = "<?php echo back_url(); ?>";
        </script>
        <form id="add-product-form">
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
            <div class="row">
                <div class="col-12">
                    <div class="row align-items-center input-row">
                        <div class="col-2 input-label">
                            Tên sản phẩm
                        </div>
                        <div class="col-10">
                            <input spellcheck="false" value="<?php echo $product->p_name ?>" type="text" name="p_name" class="form-control">
                        </div>
                    </div>
                    <div class="row align-items-center input-row">
                        <div class="col-2 input-label">
                            Mô tả sản phẩm
                        </div>
                        <div class="col-10">
                            <textarea spellcheck="false" name="p_desc" class="form-control" rows="4"><?php echo $product->p_desc ?></textarea>
                        </div>
                    </div>
                    <div class="row align-items-center input-row">
                        <div class="col-2 input-label">
                            Danh mục
                        </div>
                        <div class="col-10">
                            <div style="height: 38px;" id="categories" data-toggle="modal" data-target="#categoriesModal">
                                <span id="picked-categories-label"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center input-row">
                        <div class="col-2 input-label">
                            Bảng kích cỡ
                        </div>
                        <div class="col-10">
                            <select name="size_chart" class="form-control">
                                <option value="">none</option>
                                <?php foreach($size_charts as $item): ?>
                                    <option <?php echo $item->id === ($product->size_chart->id ?? null) ? "selected" : "" ?> value="<?php echo $item->id ?>"><?php echo $item->name; ?></option>
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
                        <script>
                            var image_urls = <?php echo json_encode($product->images); ?>;
                        </script>
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
                    <?php if(!isset($product->colors_sizes) && !isset($product->colors) && !isset($product->sizes)): ?>
                    <div id="p-price-stock-row" class="row align-items-center input-row">
                        <div class="col-2 input-label">
                            Giá sản phẩm
                        </div>
                        <div style="align-self: flex-start;" id="p-price-container" class="col-4">
                            <input value="<?php echo $product->p_price ?>" type="text" name="p_price" class="form-control">               
                        </div>
                        
                        <div style="align-self: flex-start;" class="col-6 row align-items-center">
                            <div class="col-4 input-label">
                                Kho hàng
                            </div>
                            <div id="p-price-container" class="col-8" style="padding-right:0">
                                <input value="<?php echo $product->p_stock ?>" type="text" name="p_stock" class="form-control">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($product->colors_sizes): ?>
                        <script> 
                            var colors_sizes_from_db = []; 
                            let color;
                        </script>
                        <?php foreach($product->colors_sizes as $color): ?>
                            <script>
                                color = {id: <?php echo $color->id ?>,color_path:"<?php echo $color->color_image ?>",color : "<?php echo url($color->color_image); ?>",gallery_dir:"<?php echo $color->gallery_dir ?>",name : "<?php echo $color->color_name ?>"};
                                <?php if(isset($color->sizes)): ?>
                                    color.sizes = [];
                                    <?php foreach($color->sizes as $size): ?>
                                        color.sizes.push({size: "<?php echo $size->size ?>",stock:<?php echo $size->stock ?>,price:<?php echo $size->price ?>})
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                color.gallery = [];
                                <?php foreach($color->gallery_images as $item): ?>
                                    color.gallery.push("<?php echo $item ?>");
                                <?php endforeach; ?>
                                colors_sizes_from_db.push(color);
                            </script>
                        <?php endforeach; ?>
                    <?php elseif($product->colors): ?>
                            <script>
                                var colors_from_db = [];
                                let color;
                            </script>
                            <?php foreach($product->colors as $color): ?>
                            <script>
                                color = {id: <?php echo $color->id ?>,color_path:"<?php echo $color->color_image ?>",color : "<?php echo url($color->color_image); ?>",price : <?php echo $color->price; ?>,stock : <?php echo $color->stock; ?>,gallery_dir:"<?php echo $color->gallery_dir ?>",name : "<?php echo $color->color_name ?>"};
                                color.gallery = [];
                                <?php foreach($color->gallery_images as $item): ?>
                                    color.gallery.push("<?php echo $item ?>");
                                <?php endforeach; ?>
                                colors_from_db.push(color);
                            </script>
                            <?php endforeach; ?>
                    <?php elseif($product->sizes): ?>
                        <script>
                            var sizes_from_db = [];
                            let size;
                        </script>
                        <?php foreach($product->sizes as $size): ?>
                            <script>
                                size = {id: <?php echo $size->id ?>,value:"<?php echo $size->size; ?>",price : <?php echo $size->price; ?>,stock : <?php echo $size->stock; ?>};
                                sizes_from_db.push(size);
                            </script>
                        <?php endforeach; ?>                    
                    <?php endif; ?>
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
                            <?php if($product->colors_sizes || $product->colors || $product->sizes): ?>
                            <?php if($product->colors_sizes) : ?>
                                <div id="color-variation">
                                    <h1 style="font-size: 1rem;">Phân loại màu sắc <svg style="width:20px;height:20px;float:right;cursor:pointer" id="close-color-variation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.85355339,1.98959236 L8.157,7.29314575 L13.4601551,1.98959236 C13.6337215,1.81602601 13.9031459,1.79674086 14.098014,1.93173691 L14.1672619,1.98959236 C14.362524,2.18485451 14.362524,2.501437 14.1672619,2.69669914 L14.1672619,2.69669914 L8.864,8.00014575 L14.1672619,13.3033009 C14.362524,13.498563 14.362524,13.8151455 14.1672619,14.0104076 C13.9719997,14.2056698 13.6554173,14.2056698 13.4601551,14.0104076 L8.157,8.70714575 L2.85355339,14.0104076 C2.67998704,14.183974 2.41056264,14.2032591 2.2156945,14.0682631 L2.14644661,14.0104076 C1.95118446,13.8151455 1.95118446,13.498563 2.14644661,13.3033009 L2.14644661,13.3033009 L7.45,8.00014575 L2.14644661,2.69669914 C1.95118446,2.501437 1.95118446,2.18485451 2.14644661,1.98959236 C2.34170876,1.79433021 2.65829124,1.79433021 2.85355339,1.98959236 Z"></path></svg></h1>
                                    <div id="color-variation-inputs" class="row align-items-center ui-sortable">
                                        <div class="col-4 mb-3 ui-sortable-handle">
                                            <button id="add-color-input" style="font-size:0.9rem" type="button" class="btn btn-outline">Add</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="size-variation">
                                    <h1 style="font-size: 1rem;">Phân loại kích cỡ <svg style="width:20px;height:20px;float:right;cursor:pointer" id="close-size-variation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.85355339,1.98959236 L8.157,7.29314575 L13.4601551,1.98959236 C13.6337215,1.81602601 13.9031459,1.79674086 14.098014,1.93173691 L14.1672619,1.98959236 C14.362524,2.18485451 14.362524,2.501437 14.1672619,2.69669914 L14.1672619,2.69669914 L8.864,8.00014575 L14.1672619,13.3033009 C14.362524,13.498563 14.362524,13.8151455 14.1672619,14.0104076 C13.9719997,14.2056698 13.6554173,14.2056698 13.4601551,14.0104076 L8.157,8.70714575 L2.85355339,14.0104076 C2.67998704,14.183974 2.41056264,14.2032591 2.2156945,14.0682631 L2.14644661,14.0104076 C1.95118446,13.8151455 1.95118446,13.498563 2.14644661,13.3033009 L2.14644661,13.3033009 L7.45,8.00014575 L2.14644661,2.69669914 C1.95118446,2.501437 1.95118446,2.18485451 2.14644661,1.98959236 C2.34170876,1.79433021 2.65829124,1.79433021 2.85355339,1.98959236 Z"></path></svg></h1>
                                    <div id="size-variation-inputs" class="row align-items-center ui-sortable">                                     
                                        <div class="col-4 mb-3 ui-sortable-handle">
                                            <button id="add-size-input" style="font-size:0.9rem" type="button" class="btn btn-outline">Add</button>
                                        </div>
                                    </div>
                                </div>                             
                            <?php elseif($product->colors): ?>
                                    <div id="color-variation">
                                    <h1 style="font-size: 1rem;">Phân loại màu sắc <svg style="width:20px;height:20px;float:right;cursor:pointer" id="close-color-variation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.85355339,1.98959236 L8.157,7.29314575 L13.4601551,1.98959236 C13.6337215,1.81602601 13.9031459,1.79674086 14.098014,1.93173691 L14.1672619,1.98959236 C14.362524,2.18485451 14.362524,2.501437 14.1672619,2.69669914 L14.1672619,2.69669914 L8.864,8.00014575 L14.1672619,13.3033009 C14.362524,13.498563 14.362524,13.8151455 14.1672619,14.0104076 C13.9719997,14.2056698 13.6554173,14.2056698 13.4601551,14.0104076 L8.157,8.70714575 L2.85355339,14.0104076 C2.67998704,14.183974 2.41056264,14.2032591 2.2156945,14.0682631 L2.14644661,14.0104076 C1.95118446,13.8151455 1.95118446,13.498563 2.14644661,13.3033009 L2.14644661,13.3033009 L7.45,8.00014575 L2.14644661,2.69669914 C1.95118446,2.501437 1.95118446,2.18485451 2.14644661,1.98959236 C2.34170876,1.79433021 2.65829124,1.79433021 2.85355339,1.98959236 Z"></path></svg></h1>
                                    <div id="color-variation-inputs" class="row align-items-center ui-sortable">
                                        <div class="col-4 mb-3 ui-sortable-handle">
                                            <button id="add-color-input" style="font-size:0.9rem" type="button" class="btn btn-outline">Add</button>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif($product->sizes): ?>
                                    <div id="size-variation">
                                    <h1 style="font-size: 1rem;">Phân loại kích cỡ <svg style="width:20px;height:20px;float:right;cursor:pointer" id="close-size-variation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.85355339,1.98959236 L8.157,7.29314575 L13.4601551,1.98959236 C13.6337215,1.81602601 13.9031459,1.79674086 14.098014,1.93173691 L14.1672619,1.98959236 C14.362524,2.18485451 14.362524,2.501437 14.1672619,2.69669914 L14.1672619,2.69669914 L8.864,8.00014575 L14.1672619,13.3033009 C14.362524,13.498563 14.362524,13.8151455 14.1672619,14.0104076 C13.9719997,14.2056698 13.6554173,14.2056698 13.4601551,14.0104076 L8.157,8.70714575 L2.85355339,14.0104076 C2.67998704,14.183974 2.41056264,14.2032591 2.2156945,14.0682631 L2.14644661,14.0104076 C1.95118446,13.8151455 1.95118446,13.498563 2.14644661,13.3033009 L2.14644661,13.3033009 L7.45,8.00014575 L2.14644661,2.69669914 C1.95118446,2.501437 1.95118446,2.18485451 2.14644661,1.98959236 C2.34170876,1.79433021 2.65829124,1.79433021 2.85355339,1.98959236 Z"></path></svg></h1>
                                    <div id="size-variation-inputs" class="row align-items-center ui-sortable">
                                        <div class="col-4 mb-3 ui-sortable-handle">
                                            <button id="add-size-input" style="font-size:0.9rem" type="button" class="btn btn-outline">Add</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div id="type-all" class="my-3 row mx-0 align-items-center">
                                <div class="mr-3">Nhập hàng loạt</div>
                                <input data-name="price" type="text" class="form-control col-3 mr-3" placeholder="Giá">
                                <input data-name="stock" type="text" class="form-control col-3 mr-3" placeholder="Kho hàng">
                                <button data-name="reset" type="button" class="btn btn-primary col-2">Nhập lại</button>
                            </div>
                            <div id="variation-table" class="row w-100 mr-0 ml-0">
                                    <div id="header" class="row w-100 mr-0 ml-0">
                                        <?php if($product->colors_sizes): ?>
                                        <div id="color-header" class="col-2">Màu sắc</div>
                                        <div id="size-header" class="col-2">Kích cỡ</div>
                                        <?php elseif($product->colors): ?>
                                        <div id="color-header" class="col-2">Màu sắc</div>
                                        <?php elseif($product->sizes): ?>
                                        <div id="size-header" class="col-3">Kích cỡ</div>
                                        <?php endif; ?>
                                        <div id="price-header" class="<?php echo $product->sizes ? "col" : "col-2" ?>">Giá</div>
                                        <div id="inventory-header" class="<?php echo $product->sizes ? "col" : "col-2" ?>">Kho hàng</div>
                                        <?php if($product->colors_sizes || $product->colors): ?>
                                        <div id="color-gallery-header" class="col">Hình ảnh</div>
                                        <?php endif; ?>
                                    </div>
                                    <div id="body" class="w-100 mr-0 ml-0"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="position: sticky;bottom:0" class="row align-items-center input-row">
                        <div class="offset-2 col-10">
                            <div style="gap:10px" class="d-flex">
                                <button type="button" id="delete-product" class="btn btn-secondary py-2 w-50">Xóa</button>
                                <button class="btn btn-primary py-2 w-50">Cập nhật</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        </form>
       
        <script>
            let back = "<?php echo back_url(); ?>";
        </script>    
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>