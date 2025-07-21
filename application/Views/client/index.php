<?php include(APP_PATH . "/application/Views/client/header.php") ?>
    <section id="banner">
        <div id="banner-slider" class="container-fluid">
            <div class="d-block">
                <a href="<?php echo url("collection/gia-dung/do-noi-that") ?>">
                    <img class="d-none d-md-block" src="https://api.muji.com.vn/media/catalog/category/furniture_cate_1.jpg" alt="">
                    <img class="d-block d-md-none" src="https://api.muji.com.vn/media/catalog/category/furniture_cate_-_mobile_1.jpg" alt="">
                </a>       
            </div>
            <div class="d-block">
                <a href="<?php echo url("collection/suc-khoe-lam-dep/san-pham-lam-dep") ?>">
                    <img class="d-none d-md-block" src="https://api.muji.com.vn/media/catalog/category/x2.png" alt="">
                    <img class="d-block d-md-none" src="https://api.muji.com.vn/media/catalog/category/skincare_-_mobile.jpg" alt="">
                </a>       
            </div>
        </div>
    </section>         
    <section id="product-categories" class="mt-5">
        <div class="container-fluid">
            <h3 class="collection-title">Danh mục</h3>
            <div class="row row-cols-2 gy-5 row-cols-md-4">
                <?php use NhatHoa\App\Services\CategoryService; ?>
                <?php foreach($featured_categories as $cat): ?>
                        <?php $full_slug = CategoryService::getFullSlug($cat); ?>
                        <a href="<?php echo url("collection/{$full_slug}") ?>" class="col category">
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
    <section id="soldest-products" class="collection-content mt-5">
        <div class="container-fluid">
            <h3 class="collection-title">Bán chạy</h3>
            <div class="collection owl-carousel owl-theme">
                <?php foreach($top_saled_products as $p): ?>
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
                <?php endforeach; ?>    
            </div>
        </div>
    </section>
    <section id="latest-products" class="collection-content mt-5">
        <div class="container-fluid">
            <h3 class="collection-title">Mới nhất</h3>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 gy-5 collection">
                <?php foreach($latest_products as $p): ?>
                    <div class="col">
                        <div class="collection-item position-relative">
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
                            <div class="position-absolute" style="top:0;left:0"><p class="label-new text-white bg-vnh_primary px-2 py-1">Mới</p></div>
                        </div>
                    </div>                           
                <?php endforeach; ?>    
            </div>
        </div>
    </section>
    <section class="collection-hints mt-5">
        <div class="container-fluid">
            <h2 class="collection-title">Quần Áo</h2>
              <div id="quan-ao" class="collection-content mt-5">
                <div class="collection quan-ao owl-carousel owl-theme">
                    <div class="collection-item">
                        <a href="<?php echo url("product/detail/{$p->id}") ?>">
                            <img width="187.5" height="187.5" loading="lazy" src="https://api.muji.com.vn/media/catalog/category/ladies_wear_thumb.png" style="display: inline;">
                            <div class="title">Trang phục nữ</div>
                        </a>
                    </div>    
                     <div class="collection-item">
                        <a href="<?php echo url("product/detail/{$p->id}") ?>">
                            <img width="187.5" height="187.5" loading="lazy" src="https://api.muji.com.vn/media/catalog/category/mens_thumb.png" style="display: inline;">
                            <div class="title">Trang phục nam</div>
                        </a>
                    </div>    
                     <div class="collection-item">
                        <a href="<?php echo url("product/detail/{$p->id}") ?>">
                            <img width="187.5" height="187.5" loading="lazy" src="https://api.muji.com.vn/media/catalog/category/Shoes_Sandals_-_thumb.jpg" style="display: inline;">
                            <div class="title">Giày & dép</div>
                        </a>
                    </div>    
                    <div class="collection-item">
                        <a href="<?php echo url("product/detail/{$p->id}") ?>">
                            <img width="187.5" height="187.5" loading="lazy" src="https://api.muji.com.vn/media/catalog/category/thumb_1.png" style="display: inline;">
                            <div class="title">Phụ kiện</div>
                        </a>
                    </div>                           
                </div>
            </div>
        </div>
    </section>
    <section class="collection-featured mt-5">
        <div class="container-fluid">
            <h3 class="collection-title">Sản phẩm nổi bật</h3>
              <div id="quan-ao" class="collection-content mt-5">
                <div class="collection owl-carousel owl-theme">
                    <?php foreach($quan_ao as $item): ?>
                    <div class="collection-item">
                        <a href="<?php echo url("product/detail/{$item->id}") ?>">
                            <img width="187.5" height="187.5" loading="lazy" src="<?php echo $item->thumbnail; ?>" style="display: inline;">
                            <div class="title"><?php echo $item->p_name; ?></div>
                        </a>
                        <div class="price">
                            <div>
                                <span class="num"><?php echo number_format($item->p_price,0,"",".") ?></span><span class="currency">đ</span>
                            </div>
                        </div>
                        <a class="a-button mt-3" href="<?php echo url('product/detail/' . $item->id); ?>"><span>Mua Hàng</span></a>
                    </div>    
                    <?php endforeach; ?>                          
                </div>
            </div>
        </div>
    </section>
    <script>
        const main = true;
        $("section.collection-hints .collection").slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            prevArrow: `<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
        </svg></button>`,
            nextArrow: `<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi        bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
            </svg></button>`,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    },
                },
                {
                    breakpoint: 600,
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    },
                },
                {
                    breakpoint: 480,
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    },
                },
            ],
        });
        $("section.collection-featured .collection,section#soldest-products .collection").slick({
            slidesToShow: 6,
            slidesToScroll: 1,
            autoplay: true,
            prevArrow: `<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
        </svg></button>`,
            nextArrow: `<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi        bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
            </svg></button>`,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    },
                },
                {
                    breakpoint: 600,
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    },
                },
                {
                    breakpoint: 480,
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    },
                },
            ],
        });
    </script>
    <style>
        .slick-slide {
            margin-left:27px;
        }
        .slick-list {
            margin-left: -27px;
        }
    </style>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>