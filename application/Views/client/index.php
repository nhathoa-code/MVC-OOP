<?php include(APP_PATH . "/application/Views/client/header.php") ?>
    <section id="banner">
        <div id="banner-slider" class="container-fluid">
            <div class="d-block">
                <a href="">
                    <img src="https://down-bs-vn.img.susercontent.com/vn-11134210-7ras8-m0ttyugzd66n3d.webp" alt="">
                    <!-- <img src="<?php echo url("client_assets/images/WebBanner.jpg") ?>" alt=""> -->
                </a>       
            </div>
            <div class="d-block">
                <a href="">
                    <img src="https://down-bs-vn.img.susercontent.com/vn-11134210-7ras8-m0ttyugzekr32f.webp" alt="">
                </a>       
            </div>
        </div>
    </section>         
    <section id="product-categories" class="mt-5">
        <div class="container-fluid">
            <h2 class="collection-title">Danh mục</h2>
            <div class="row row-cols-2 gy-5 row-cols-md-4">
                <?php foreach($categories as $cat): ?>
                        <a href="<?php echo url("collection/{$cat->cat_slug}") ?>" class="col category">
                            <div class="category-item">
                                <div class="img">
                                    <img src="<?php echo url("{$cat->cat_image}") ?>" alt="">
                                </div>
                                <div class="text-category"><?php echo $cat->cat_name ?></div>
                            </div>
                        </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section id="latest-products" class="collection-content mt-5">
        <div class="container-fluid">
            <h2 class="collection-title">Bán chạy</h2>
            <div class="row row-cols-2 row-cols-md-4 gy-5 collection">
                <?php foreach($top_saled_products as $p): ?>
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
        </div>
    </section>
    <section id="latest-products" class="collection-content mt-5">
        <div class="container-fluid">
            <h2 class="collection-title">Mới nhất</h2>
            <div class="row row-cols-2 row-cols-md-4 gy-5 collection">
                <?php foreach($latest_products as $p): ?>
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
        </div>
    </section>
    <script>
        const main = true;
    </script>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>