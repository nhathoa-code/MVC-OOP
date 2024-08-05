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
<div class="heading"><?php echo $store->name ?></div>
<div class="row">
    <div class="col-12">
        <form id="inventory-update" method="POST" action="<?php echo url("admin/store/{$store->id}/inventory/product/{$product->id}/edit"); ?>">
            <div id="variation-table" class="row w-100 mr-0 ml-0">
                <div id="header" class="row w-100 mr-0 ml-0">
                    <div id="color-gallery-header" class="col">Sản phẩm</div>
                    <div id="color-header" class="col-2">Màu sắc</div>
                    <div id="size-header" class="col-2">Kích cỡ</div>
                    <div id="inventory-header" class="col-2">Kho hàng</div>
                    <div id="inventory-header" class="col-2">Giá</div>
                </div>
                <div id="body" class="w-100 mr-0 ml-0">
                    <div class="w-100">
                        <div class="w-100 row mr-0 ml-0">
                            <div class="col-4 border-right border-bottom d-flex flex-column">
                                <div class="col cell color-gallery-cell">
                                    <div>
                                        <?php echo $product->p_name ?>
                                        <br>
                                        <span style="font-size: 0.85rem;">(<?php echo $product->id ?>)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8 px-0">
                                <?php
                                    $product_variant = $product->colors_sizes ?: $product->colors ?: $product->sizes ?: array(1);
                                ?>
                                <?php foreach($product_variant as $item): ?>
                                    <div class="row w-100 mr-0 ml-0 border-bottom <?php echo $item === 1 ? "h-100" : "" ?>">
                                        <div class="col-3 cell color-cell">
                                            <?php if($product->hasColorsSizes() || $product->hasColors()): ?>
                                                <div class="d-inline-flex flex-column align-items-center">
                                                    <div class="color-upload-icon-wrap">
                                                        <img style="width:30px" src="<?php echo url($item->color_image) ?>" alt="">
                                                    </div>
                                                    <span style="margin-top: 5px"><?php echo $item->color_name ?></span>
                                                    <input type="hidden" name="colors_of_product_<?php echo $product->id ?>[<?php echo $item->id ?>]" value="<?php echo $item->id ?>">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-3 cell size-cell">
                                            <?php if($product->hasColorsSizes()): ?>
                                                <?php foreach($item->sizes as $size): ?>
                                                    <div class="row-cell">
                                                        <?php echo $size->size; ?>
                                                        <input type="hidden" name="sizes_of_color_<?php echo $item->id ?>[]" value="<?php echo $size->size ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php elseif($product->hasSizes()): ?>
                                                    <div class="row-cell">
                                                        <?php echo $item->size; ?>
                                                        <input type="hidden" name="sizes_of_product_<?php echo $product->id ?>[]" value="<?php echo $item->size ?>">
                                                    </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-3 cell inventory-cell">
                                            <?php if($product->hasColorsSizes()): ?>
                                                <?php foreach($item->sizes as $size): ?>
                                                    <div class="row-cell">
                                                        <input
                                                            style="padding: 10px; height: 30px; width: 100%"
                                                            name="stock_of_product_<?php echo $product->id ?>_color_<?php echo $item->id ?>_<?php echo str_replace(".","*",$size->size) ?>"
                                                            value="<?php echo array_find($product->inventory,function($object) use($item,$size){
                                                                return $object->color_id == $item->id && $object->size == $size->size;
                                                            })->stock; ?>"
                                                            type="text"
                                                            class="form-control inventory"
                                                        />
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="row-cell">
                                                        <input
                                                            style="padding: 10px; height: 30px; width: 100%"
                                                            name="stock_of_product_<?php echo $product->id ?><?php echo $product->hasColors() ? "_color_{$item->id}" : ($product->hasSizes() ? "_size_" . str_replace(".","*",$item->size) : "") ?>"
                                                            value="<?php echo array_find($product->inventory,function($object) use($item,$product){
                                                                return $product->hasColors() ? $object->color_id == $item->id : ($product->hasSizes() ? ($object->size == $item->size) : ($object->color_id == null && $object->size == null));
                                                            })->stock; ?>"
                                                            type="text"
                                                            class="form-control inventory"
                                                        />
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-3 cell price-cell">
                                            <?php if($product->hasColorsSizes()): ?>
                                                <?php foreach($item->sizes as $size): ?>
                                                    <div class="row-cell">
                                                        <input
                                                            style="padding: 10px; height: 30px; width: 100%"
                                                            name="price_of_product_<?php echo $product->id ?>_color_<?php echo $item->id ?>_<?php echo str_replace(".","*",$size->size) ?>"
                                                            value="<?php echo array_find($product->inventory,function($object) use($item,$size){
                                                                return $object->color_id == $item->id && $object->size == $size->size;
                                                            })->price; ?>"
                                                            type="text"
                                                            class="form-control inventory"
                                                        />
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="row-cell">
                                                        <input
                                                            style="padding: 10px; height: 30px; width: 100%"
                                                            name="price_of_product_<?php echo $product->id ?><?php echo $product->hasColors() ? "_color_{$item->id}" : ($product->hasSizes() ? "_size_" . str_replace(".","*",$item->size) : "") ?>"
                                                            value="<?php echo array_find($product->inventory,function($object) use($item,$product){
                                                                return $product->hasColors() ? $object->color_id == $item->id : ($product->hasSizes() ? ($object->size == $item->size) : ($object->color_id == null && $object->size == null));
                                                            })->price; ?>"
                                                            type="text"
                                                            class="form-control inventory"
                                                        />
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>  
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="product" value="<?php echo $product->id; ?>">
            <input type="hidden" name="store" value="<?php echo $store->id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div style="position: sticky;bottom:0" class="row w-100 mr-0 ml-0 mt-2">
                <button type="submit" class="btn btn-primary py-2 w-100">Cập nhật kho</button>
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
    const inventory_update_url = "<?php echo url("admin/store/{$store->id}/inventory/product/{$product->id}/edit") ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>