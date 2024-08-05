<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<?php if(isset($message["message"])) : ?>
<script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
<link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>">    
<script>
    notif({
        msg:"<?php echo $message['message']; ?>",
        type:"success",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 5000,
        animation:'slide'
    });
</script>
<?php endif; ?>
<script>
    const variation_of_products = [];
</script>
<div class="heading">Kho online | <span><a href="<?php echo url("admin/product/add") ?>">Thêm sản phẩm</a></span></div>
<div class="row">
    <div class="row col-12 pr-0">
        <div class="col-7">
            <form class="mb-3">
                <input type="text" name="keyword" value="<?php echo get_query("keyword") ?? "" ?>" class="form-control" placeholder="Tìm sản phẩm theo tên hoặc mã">
            </form>    
        </div>
        <div class="col-5 text-right pr-0">
            <a href="<?php echo url("admin/product/export/excel" . "?" . get_query_string(current_url())); ?>" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <svg viewBox="0 0 48 48" width="28px" height="28px"><path fill="#169154" d="M29,6H15.744C14.781,6,14,6.781,14,7.744v7.259h15V6z"/><path fill="#18482a" d="M14,33.054v7.202C14,41.219,14.781,42,15.743,42H29v-8.946H14z"/><path fill="#0c8045" d="M14 15.003H29V24.005000000000003H14z"/><path fill="#17472a" d="M14 24.005H29V33.055H14z"/><g><path fill="#29c27f" d="M42.256,6H29v9.003h15V7.744C44,6.781,43.219,6,42.256,6z"/><path fill="#27663f" d="M29,33.054V42h13.257C43.219,42,44,41.219,44,40.257v-7.202H29z"/><path fill="#19ac65" d="M29 15.003H44V24.005000000000003H29z"/><path fill="#129652" d="M29 24.005H44V33.055H29z"/></g><path fill="#0c7238" d="M22.319,34H5.681C4.753,34,4,33.247,4,32.319V15.681C4,14.753,4.753,14,5.681,14h16.638 C23.247,14,24,14.753,24,15.681v16.638C24,33.247,23.247,34,22.319,34z"/><path fill="#fff" d="M9.807 19L12.193 19 14.129 22.754 16.175 19 18.404 19 15.333 24 18.474 29 16.123 29 14.013 25.07 11.912 29 9.526 29 12.719 23.982z"/></svg>
                </span>
                <span class="text">Download excel</span>
            </a>
        </div>
    </div>
    <div class="col-12">
        <table id="product-table" class="table bg-white">
            <thead>
                <tr>
                    <th style="width:30%" scope="col">Tên sản phẩm</th>
                    <th style="width:20%" scope="col">Phân loại hàng</th>
                    <th style="width:15%" scope="col">Giá</th>
                    <th style="width:10%" scope="col">Kho</th>
                    <th style="width:15%" scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                    <?php if(isset($p->colors_sizes)): ?>
                        <?php
                            $groupedColors = [];
                            foreach($p->colors_sizes as $item){
                                $groupedColors[$item->color_name][] = $item;
                            }
                        ?>
                        <script>
                            variation_of_products.push({p_id:"<?php echo $p->id; ?>", variation:<?php echo json_encode($groupedColors); ?>});
                        </script>
                    <?php endif; ?>      
                    <tr id="<?php echo $p->id; ?>">
                        <td class="product-name-wrap">
                            <div class="d-flex">
                                <img style="width: 100px;margin-right:10px" src="<?php echo $p->product_images[0]; ?>" alt="">
                                <span><?php echo $p->p_name ?></span>
                            </div>
                        </td>
                        <?php
                            if($p->colors_sizes){
                                $color = $p->colors_sizes[0]->color_name;
                            }
                        ?>
                        <td class="product-variation-name">
                            <?php if(isset($p->colors_sizes)): ?>
                                <?php foreach($p->colors_sizes as $item):?>
                                    <?php if($color === $item->color_name): ?>
                                        <div><?php echo "{$item->color_name},{$item->size}"; ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if(count($groupedColors) > 1): ?>
                                    <div class="toggle-variation more" data-p_id="<?php echo $p->id ?>">Xem thêm</div>
                                <?php endif; ?>
                            <?php elseif(isset($p->colors)): ?>
                                <?php foreach($p->colors as $item):?>
                                <div><?php echo "{$item->color_name}"; ?></div>
                                <?php endforeach; ?>
                            <?php elseif(isset($p->sizes)): ?>
                                <?php foreach($p->sizes as $item):?>
                                <div><?php echo "{$item->size}"; ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <td class="product-variation-price">
                            <?php if(isset($p->colors_sizes)): ?>
                                <?php foreach($p->colors_sizes as $item): ?>
                                    <?php if($color === $item->color_name): ?>
                                        <div><?php echo number_format($item->price,0,"",".") . "₫"; ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php elseif(isset($p->colors)): ?>
                                <?php foreach($p->colors as $item): ?>
                                    <div><?php echo number_format($item->price,0,"",".") . "₫"; ?></div>
                                <?php endforeach; ?>
                            <?php elseif(isset($p->sizes)): ?>
                                <?php foreach($p->sizes as $item): ?>
                                    <div><?php echo number_format($item->price,0,"",".") . "₫"; ?></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div><?php echo number_format($p->p_price,0,"",".") . "₫"; ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="product-variation-stock">
                            <?php if(isset($p->colors_sizes)): ?>
                                <?php foreach($p->colors_sizes as $item): ?>
                                    <?php if($color === $item->color_name): ?>
                                        <div><?php echo $item->stock; ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php elseif($p->colors): ?>
                                <?php foreach($p->colors as $item): ?>
                                    <div><?php echo $item->stock; ?></div>
                                <?php endforeach; ?>
                            <?php elseif($p->sizes): ?>
                                <?php foreach($p->sizes as $item): ?>
                                    <div><?php echo $item->stock; ?></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div><?php echo $p->p_stock; ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="product-actions">
                            <a href="<?php echo url("admin/product/edit/{$p->id}") ?>">Cập nhật</a>
                            <a href="<?php echo url("product/detail/{$p->id}") ?>">Xem sản phẩm</a>
                            <a style="color:brown" href="javascript:void(0)" onclick="deleteProduct(<?php echo $p->id ?>,'<?php echo $p->id ?>')">Xóa sản phẩm</a>
                            <form id="delete-product-<?php echo $p->id; ?>" style="display:none" method="POST" action="<?php echo url("admin/product/delete/{$p->id}"); ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form>
                        </td>
                    </tr>
                <?php endforeach; ?>    
            </tbody>
        </table>
    </div>
</div>
<?php echo pagination($totalPages,$currentPage); ?>           
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>