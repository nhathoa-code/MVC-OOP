<?php include(APP_PATH . "/application/Views/client/header.php") ?>
    <section id="content" class="collection-content">
        <div class="container-fluid">
            <div class="mb-5 border-top border-bottom py-2" id="search-title">
                Tìm thấy <span style="font-weight:500"><?php echo $number_of_products ?> sản phẩm</span> cho từ khóa <span>"<?php echo get_query("keyword"); ?>"</span>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 gy-5 collection">
            <?php foreach($collection as $p): ?>
                <div class="col">
                    <div class="collection-item">
                        <a href="<?php echo url("product/detail/{$p->id}") ?>">
                            <img width="187.5" height="187.5" loading="lazy" src="<?php echo $p->thumbnail ?>" style="display: inline;">
                            <div class="title"><?php echo $p->p_name ?></div>
                        </a>
                        <div class="price">
                            <div>
                                <span class="num"><?php echo number_format($p->p_price,0,"",".") ?></span><span class="currency">đ</span>
                            </div>
                            <ul class="colors">
                                <?php if(count($p->colors) <= 3): ?>
                                <?php foreach($p->colors as $color): ?>
                                    <li>
                                        <span>
                                            <img src="<?php echo url($color->color_image) ?>" alt="">
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <?php 
                                        $break_index = 0;
                                    ?>
                                    <?php foreach($p->colors as $color): ?>
                                        <li>
                                            <span>
                                                <img src="<?php echo url($color->color_image) ?>" alt="">
                                            </span>
                                        </li>
                                        <?php if($break_index === 2): ?>
                                            <li>
                                                <span class="num-colors">+<?php echo count($p->colors) - 3; ?></span>
                                            </li>
                                        <?php endif; ?>    
                                    <?php if($break_index === 2){break;} $break_index++; endforeach; ?>
                                <?php endif; ?>    
                            </ul>
                        </div>
                    </div>
                </div>                           
            <?php endforeach; ?>    
            </div>
            <?php if($total_pages > 1 && $total_pages > $page): ?>
                <div class="group-pagging flex-column mt-5">
                    <div style="font-size: 13px;color:#8a8a8a;margin-bottom:5px">hiển thị <span data-displayed-products="<?php echo $displayed_products; ?>" id="displayed_products"><?php echo $displayed_products ?></span> trong <span id="total_products"><?php echo $number_of_products; ?></span> sản phẩm</div>
                    <button class="view-more view-more-search cate-view-more" data-currentpage="<?php echo $page; ?>">
                        Xem thêm <span class="viewmore-totalitem"><?php echo $number_of_products - $displayed_products > $limit ? $limit : $number_of_products - $displayed_products ?></span> sản phẩm
                    </button>
                </div>
                <?php else: ?>
                <div class="group-pagging flex-column mt-5">
                    <div style="font-size: 13px;color:#8a8a8a;margin-bottom:5px">hiển thị <span data-displayed-products="<?php echo $displayed_products; ?>" id="displayed_products"><?php echo $displayed_products ?></span> trong <span id="total_products"><?php echo $number_of_products; ?></span> sản phẩm</div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
        const keyword = "<?php echo get_query("keyword") ?>";
        const search_url = "<?php echo current_url() ?>";
        const search = true;
        var total_products = <?php echo $number_of_products; ?>;
        const limit = <?php echo $limit; ?>
    </script>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>