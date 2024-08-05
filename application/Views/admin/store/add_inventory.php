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
<div class="heading">Nhập kho: <?php echo $store->name ?></div>
<div class="row">
    <div class="col-6">
        <form method="GET" id="find-product">
            <div style="gap:10px" class="mb-3 d-flex">
                <select name="product_id" id="product_select" class="form-control">
                    <option disabled selected>Chọn sản phẩm cần thêm vào kho</option>
                    <?php foreach($products as $item): ?>
                        <option id="product-<?php echo $item->id; ?>" value="<?php echo $item->id ?>"><?php echo $item->p_name; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="url" value="<?php echo url("admin/store/findproduct"); ?>">
                <button style="white-space: nowrap;" type="submit" class="btn btn-primary">Thêm sản phẩm</button>
            </div>
        </form>
        <script>
            $(document).ready(function() {
                $('select#product_select').select2();
            });
        </script>
    </div>
    <div class="col-12">
        <form id="inventory" method="POST">
            <div id="variation-table" class="row w-100 mr-0 ml-0">
                <div id="header" class="row w-100 mr-0 ml-0">
                    <div id="color-gallery-header" class="col">Sản phẩm</div>
                    <div id="color-header" class="col-2">Màu sắc</div>
                    <div id="size-header" class="col-2">Kích cỡ</div>
                    <div id="inventory-header" class="col-2">Kho hàng</div>
                    <div id="price-header" class="col-2">Giá</div>
                </div>
                <div id="body" class="w-100 mr-0 ml-0">
                    <!-- <div class="w-100">
                        <div class="w-100 row mr-0 ml-0">
                            <div class="col-6 border-right border-bottom d-flex flex-column">
                                <div class="col cell color-gallery-cell">
                                    <div>
                                        ÁO SƠ MI TAY DÀI VẢI XÔ PHA GÒN
                                        <br>
                                        <span style="font-size: 0.85rem;">(VNH8716254967)</span>
                                        <div>
                                            <svg class="remove-product" style="cursor:pointer" width="20px" height="20px" viewBox="0 0 16 16" version="1.1">
                                                <rect width="16" height="16" id="icon-bound" fill="none" />
                                                <polygon points="14.707,2.707 13.293,1.293 8,6.586 2.707,1.293 1.293,2.707 6.586,8 1.293,13.293 2.707,14.707 8,9.414 
                                                    13.293,14.707 14.707,13.293 9.414,8 "/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 px-0">
                                <div class="row w-100 mr-0 ml-0 border-bottom">
                                    <div class="col-4 cell color-cell">
                                        <div class="d-inline-flex flex-column align-items-center">
                                            <div class="color-upload-icon-wrap">
                                                <img style="width:30px" src="<?php echo url("images/products/071a62fa-5143-467a-ae3e-7a5b65b41cf8/colors/4550583653477_99_95.jpg") ?>" alt="">
                                            </div>
                                            <input type="hidden" name="color[]" value="<?php echo 1 ?>">
                                            <span style="margin-top: 5px">black</span>
                                        </div>
                                    </div>
                                    <div class="col-4 cell size-cell">
                                        <div class="row-cell">
                                            S
                                            <input type="hidden" name="">
                                        </div>
                                        <div class="row-cell">b</div>
                                        <div class="row-cell">c</div>
                                    </div>
                                    <div class="col-4 cell inventory-cell">
                                        <div class="row-cell">
                                            <input
                                                style="padding: 10px; height: 30px; width: 100%"
                                                value="0"
                                                type="text"
                                                class="form-control inventory"
                                            />
                                        </div>
                                        <div class="row-cell">
                                        <input
                                            style="padding: 10px; height: 30px; width: 100%"
                                            value="0"
                                            type="text"
                                            class="form-control inventory"
                                        />
                                        </div>
                                        <div class="row-cell">
                                        <input
                                            style="padding: 10px; height: 30px; width: 100%"
                                            value="0"
                                            type="text"
                                            class="form-control inventory"
                                        />
                                        </div>
                                    </div>
                                </div>  
                                <div class="row w-100 mr-0 ml-0">
                                    <div class="col-4 cell color-cell">
                                        <div class="d-inline-flex flex-column align-items-center">
                                            <div class="color-upload-icon-wrap">
                                                <img style="width:30px" src="<?php echo url("images/products/071a62fa-5143-467a-ae3e-7a5b65b41cf8/colors/4550583653477_99_95.jpg") ?>" alt="">
                                            </div>
                                            <span style="margin-top: 5px">black</span>
                                        </div>
                                    </div>
                                    <div class="col-4 cell size-cell">
                                        <div class="row-cell">a</div>
                                        <div class="row-cell">b</div>
                                        <div class="row-cell">c</div>
                                    </div>
                                    <div class="col-4 cell inventory-cell">
                                        <div class="row-cell">
                                        <input
                                            style="padding: 10px; height: 30px; width: 100%"
                                            value="0"
                                            type="text"
                                            class="form-control inventory"
                                        />
                                        </div>
                                        <div class="row-cell">
                                        <input
                                            style="padding: 10px; height: 30px; width: 100%"
                                            value="0"
                                            type="text"
                                            class="form-control inventory"
                                        />
                                        </div>
                                        <div class="row-cell">
                                        <input
                                            style="padding: 10px; height: 30px; width: 100%"
                                            value="0"
                                            type="text"
                                            class="form-control inventory"
                                        />
                                        </div>
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
            <input type="hidden" name="store" value="<?php echo $store->id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div style="position: sticky;bottom:0" class="row w-100 mr-0 ml-0 mt-2">
                <button type="submit" class="btn btn-primary py-2 w-100">Lưu Kho</button>
            </div>
        </form>
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
<script>
    const base_url = "<?php echo baseUrl() ?>";
    const inventory_url = "<?php echo url("admin/store/{$store->id}/inventory/add") ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>