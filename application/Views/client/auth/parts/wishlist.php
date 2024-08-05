<?php if(count($data['products']) > 0): ?>  
<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 gy-5 collections collection-content">
    <script>
        const remove_from_wl_url = "<?php echo url("wishlist/remove") ?>";
    </script>
    <script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
    <link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>">
    <?php foreach($data['products'] as $p): ?>
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
                <svg class="remove-wl" data-p_id="<?php echo $p->id ?>" style="position:absolute;top:5;right:5;cursor:pointer" title="Loại khỏi wishlist" width="24" height="24" viewBox="0 0 32 32" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.6526 5.94266C22.5995 -1.35734 39.9656 11.416 15.6526 27.84C-8.66048 11.4173 8.70691 -1.35734 15.6526 5.94266Z" fill="#52575C"></path>
                </svg>
            </div>
        </div>                           
    <?php endforeach; ?>    
</div>
<?php else: ?>
    <div>Chưa có sản phẩm nào trong wishlist của bạn !</div>
<?php endif; ?>    