<?php include(APP_PATH . "/application/Views/client/header.php") ?>
<script>
    const add_to_cart_url = "<?php echo url("cart/add"); ?>";
    const add_to_wl_url = "<?php echo url("wishlist/add"); ?>";
    const search_stores_url = "<?php echo url("stores/search") ?>";
    const p_id = "<?php echo $product->id ?>";
    const p_name = "<?php echo $product->p_name ?>";
    const product_detail = true;
    var selected_size = "";
    var must_pick_size = false;
    const remove_from_wl_url = "<?php echo url("wishlist/remove") ?>";
    var provinces = <?php echo json_encode($provinces) ?>;
</script>
<script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
<link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>"> 
<section id="content">
    <div class="container-fluid">
        <div class="row">
            <div id="product-detail" class="col-12">
                <div class="row">
                    <div class="col-lg-8 col-ms-12">
                        <div class="detail-product-picture photoswipe" id="picture">
                            <?php
                                $out_of_stock = false;
                                $is_color_selected = false; 
                                $color_selected_index = 0;
                                $selected_size = false;
                                if(isset($product->colors) || isset($product->colors_sizes)){
                                    $available_stock = 0;
                                    if(isset($product->colors))
                                    {
                                        $colors = $product->colors;
                                    }
                                    if(isset($product->colors_sizes))
                                    {
                                        $colors = $product->colors_sizes;
                                    }
                                    foreach($colors as $index => $color)
                                    {
                                        if($is_color_selected)
                                        {
                                            break;
                                        }
                                        if(isset($color->sizes))
                                        {
                                            foreach($color->sizes as $item){
                                                if($item->stock > 0)
                                                {
                                                    $selected_size = $item->size;
                                                    $available_stock = $item->stock;
                                                    $color_selected_index = $index;
                                                    $is_color_selected = true;
                                                    break;
                                                }
                                            }
                                        }else
                                        {
                                            if($color->stock > 0)
                                            {
                                                $available_stock = $color->stock;
                                                $color_selected_index = $index;
                                                $is_color_selected = true;
                                                break;
                                            }
                                        }   
                                    }
                                    if(!$is_color_selected)
                                    {
                                        $out_of_stock = true;
                                    }
                                }else if(isset($product->sizes))
                                {
                                    foreach($product->sizes as $item)
                                    {
                                        if($item->stock > 0)
                                        {
                                            $selected_size = $item->size;
                                            $available_stock = $item->stock;
                                            break;
                                        }
                                    }
                                    if(!$selected_size)
                                    {
                                        $out_of_stock = true;
                                    }
                                }else
                                {
                                    $available_stock = $product->p_stock;
                                    if($product->p_stock === 0)
                                    {
                                        $out_of_stock = true;
                                    }
                                }
                            ?>
                            <script>
                                selected_size = "<?php echo $selected_size ?>";
                                size = selected_size;
                            </script>
                            <div>
                                <ul>
                                    <div class="detail-product-picture-main">
                                        <?php if(isset($colors)): ?>
                                        <li>
                                            <div class="photoswipeImages">
                                                <span>
                                                    <a href="<?php echo $colors[$color_selected_index]->gallery_images[0]; ?>" data-pswp-width="600" data-pswp-height="600" target="_blank">
                                                        <img title="<?php $product->p_name ?>" loading="lazy" alt="<?php $product->p_name ?>" src="<?php echo $colors[$color_selected_index]->gallery_images[0]; ?>" id="mainImage" class="picture--mainimage mainimage0" width="400" height="400">
                                                    </a>
                                                </span>
                                            </div>
                                        </li>  
                                        <?php elseif(isset($product->sizes)): ?>
                                            <li>
                                                <div class="photoswipeImages">
                                                    <span>
                                                        <a href="<?php echo $product->images[0]; ?>" data-pswp-width="600" data-pswp-height="600" target="_blank">
                                                            <img title="<?php $product->p_name ?>" loading="lazy" alt="<?php $product->p_name ?>" src="<?php echo $product->images[0]; ?>" id="mainImage" class="picture--mainimage mainimage0" width="400" height="400">
                                                        </a>    
                                                    </span>
                                                </div>
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <div class="photoswipeImages">
                                                    <span>
                                                        <a href="<?php echo $product->images[0]; ?>" data-pswp-width="600" data-pswp-height="600" target="_blank">
                                                            <img title="<?php $product->p_name ?>" loading="lazy" alt="<?php $product->p_name ?>" src="<?php echo $product->images[0]; ?>" id="mainImage" class="picture--mainimage mainimage0" width="400" height="400">
                                                        </a>
                                                    </span>
                                                </div>
                                            </li>
                                        <?php endif; ?>
                                    </div>
                                </ul>
                            </div>
                            <div class="detail-product-photoswipe">
                                <ul class="pswp-gallery pswp-gallery--single-column" id="photoswipe">
                                    <?php if(isset($colors)): ?>
                                        <?php for($i = 1;$i < count($colors[$color_selected_index]->gallery_images);$i++): ?>
                                            <li>
                                                <div class="photoswipeImages" data-pswp-uid="2">
                                                <span data-src="//img.muji.net/img/item/4550583095635_01_1260.jpg" data-size="1260x1260">
                                                    <a href="<?php echo $colors[$color_selected_index]->gallery_images[$i] ?>" data-pswp-width="600" data-pswp-height="600" target="_blank">
                                                        <img src="<?php echo $colors[$color_selected_index]->gallery_images[$i] ?>" class="picture--mainimage" loading="lazy" alt="" width="400" height="400">
                                                    </a>
                                                </span>
                                                </div>
                                            </li>
                                        <?php endfor; ?>
                                    <?php else: ?>
                                        <?php for($i = 1;$i < count($product->images);$i++): ?>
                                            <li>
                                                <div class="photoswipeImages" data-pswp-uid="2">
                                                <span data-src="//img.muji.net/img/item/4550583095635_01_1260.jpg" data-size="1260x1260">
                                                    <a href="<?php echo $product->images[$i] ?>" data-pswp-width="600" data-pswp-height="600" target="_blank">
                                                        <img id="1" src="<?php echo $product->images[$i] ?>" class="picture--mainimage" alt="" loading="lazy" width="400" height="400">
                                                    </a>
                                                </span>
                                                </div>
                                            </li>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-ms-12 mt-4 mt-lg-0 sticky" style="top:78.42px">
                        <h1 class="detail-product-name"><?php echo $product->p_name ?>   
                            <span class="sale"><span class="icon">SALE</span></span>
                            <?php if(!isset($product->wl) || !$product->wl): ?>
                                <svg id="add-wl" style="margin-bottom: 5px;cursor:pointer" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M11.5332 6.93144L10.7688 6.14476C8.97322 4.29941 5.68169 4.93586 4.49325 7.25588C3.93592 8.34745 3.8097 9.92258 4.82836 11.9341C5.80969 13.8702 7.85056 16.1884 11.5332 18.7147C15.2158 16.1884 17.2558 13.8702 18.2389 11.9341C19.2567 9.92169 19.1323 8.34745 18.5732 7.25677C17.3847 4.93675 14.0932 4.29852 12.2976 6.14387L11.5332 6.93144ZM11.5332 20C-4.82224 9.19279 6.49768 0.757159 11.3465 5.21942C11.4096 5.27809 11.4728 5.33853 11.5332 5.40165C11.5936 5.33853 11.6559 5.2772 11.7208 5.22031C16.5687 0.755381 27.8886 9.19101 11.5341 20H11.5332Z" fill="#25282B"></path>
                                </svg>
                            <?php else: ?>
                                <svg class="remove-wl" data-p_id="<?php echo $product->id ?>" style="margin-bottom: 5px;cursor:pointer" width="24" height="24" viewBox="0 0 32 32" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.6526 5.94266C22.5995 -1.35734 39.9656 11.416 15.6526 27.84C-8.66048 11.4173 8.70691 -1.35734 15.6526 5.94266Z" fill="#52575C"> </path>
                                </svg>
                            <?php endif; ?>
                        </h1>
                        <p  style="white-space: pre-line" class="desc"><?php echo $product->p_desc ?></p>
                        <section class="detail-price-label">
                            <div class="price">
                                <span class="num"><?php echo number_format($product->p_price,0,"",".") ?></span>Đ
                            </div>
                        </section>
                        <div class="jan">
                            MÃ SẢN PHẨM:<span class="num"><?php echo $product->id ?></span>
                        </div>
                        <?php if(isset($colors)): ?>
                            <div class="detail-product-color">
                                <dl class="color">
                                <dt>MÀU: <span id="color-name"></span></dt>
                                    <?php foreach($colors as $index => $color): ?>
                                        <dd class="available">
                                            <span class="color-item <?php echo $index === $color_selected_index ? "selected" : "" ?>">
                                                <img alt="" title="" src="<?php echo url($color->color_image) ?>" width="50" height="50">
                                            </span>
                                        </dd>
                                    <?php endforeach; ?>   
                                    <script>
                                        const selected_color = <?php echo json_encode($colors[$color_selected_index]); ?>;
                                        color_name = selected_color.color_name;
                                        color_image = selected_color.color_image;
                                        color_ID = selected_color.id;
                                        p_image = selected_color.gallery_images[0];
                                        if(!selected_color.hasOwnProperty("sizes")){
                                            price = selected_color.price;
                                        }else{
                                            must_pick_size = true;
                                            price = selected_color.sizes.find((item)=>item.size === size)?.price;
                                        }
                                        $(".detail-product-color .color .color-item").eq(<?php echo $color_selected_index; ?>).data("selected",true);
          
                                        const product_colors = <?php echo json_encode($colors); ?>;
                                        $("#color-name").text(`${selected_color.color_name}`);
                                        $(".detail-product-color .color .color-item").each((index,item)=>{
                                            $(item).click(function(){                                                  
                                                if (!$(item).data("selected")) {
                                                    $("#product-detail .detail-product-color .color-item").removeData(
                                                    "selected"
                                                    );
                                                    $(item).data("selected", true);
                                                } else {
                                                    return;
                                                }
                                                $("#product-detail .detail-product-color .color span").removeClass("selected");
                                                $(item).addClass("selected");
                                                let color = product_colors[index];
                                                price = null;
                                                size = null;
                                                color_name = color.color_name;
                                                color_image = color.color_image;
                                                color_ID = color.id;
                                                p_image = color.gallery_images[0];
                                                $(`#product-detail .detail-product-picture-main a`).prop(
                                                "href",
                                                color.gallery_images[0]
                                                );
                                                $(`#product-detail .detail-product-picture-main img`).prop(
                                                "src",
                                                color.gallery_images[0]
                                                );
                                                if(color.hasOwnProperty("sizes")){
                                                    let s = color.sizes.find((item)=>item.size === selected_size);
                                                    price = s?.price;
                                                    size = s?.size;
                                                    $(".detail-price-label .price span").text(new Intl.NumberFormat({style: "currency"}).format(Math.max(...color.sizes.map((item)=> item.price))));
                                                    $(".detail-product-size .size dt").nextAll().remove();
                                                    color.sizes.forEach((item) => {
                                                        let dd = $("<dd></dd>");
                                                        let span = $(`<span ${item.stock <= 0 && item.size === selected_size ? "class='out-stock selected'" : (item.stock <= 0 ? "class='out-stock'" : (item.size === selected_size ? "class='selected'" : ""))}>${item.size}</span>`);
                                                        if(item.size === selected_size){
                                                            if(item.stock > 0){
                                                                size = selected_size;
                                                                if($("#stock-notification").children("span#in-stock").length > 0){
                                                                    $("#stock-notification span").text(item.stock);
                                                                }else{
                                                                    $("#stock-notification").html(`Còn <span id="in-stock" style="color:#af2522">${item.stock}</span> sản phẩm`);
                                                                }
                                                            }else{
                                                                $("#stock-notification").html(`<span id="out-stock" style="color:#af2522">Tạm hết hàng<span>`);
                                                            }
                                                        }
                                                        $(span).click(function(){
                                                            if(item.stock > 0){
                                                                if($("#stock-notification").children("span#in-stock").length > 0){
                                                                    $("#stock-notification span").text(item.stock);
                                                                }else{
                                                                    $("#stock-notification").html(`Còn <span id="in-stock" style="color:#af2522">${item.stock}</span> sản phẩm`);
                                                                }
                                                                $(".detail-product-size .size span").removeClass("selected");
                                                                $(this).addClass("selected");
                                                                size = item.size;
                                                                selected_size = size;
                                                                price = item.price;
                                                                $(".detail-price-label .price span").text(new Intl.NumberFormat({style: "currency"}).format(price))
                                                            }
                                                        })
                                                        dd.append(span);
                                                        $(".detail-product-size .size").append(dd);
                                                    });
                                                }else{
                                                    price = color.price;
                                                    if(color.stock > 0){
                                                        if($("#stock-notification").children("span#in-stock").length > 0){
                                                            $("#stock-notification span").text(color.stock);
                                                        }else{
                                                            $("#stock-notification").html(`Còn <span id="in-stock" style="color:#af2522">${color.stock}</span> sản phẩm`);
                                                        }
                                                    }else{
                                                        $("#stock-notification").html(`<span id="out-stock" style="color:#af2522">Tạm hết hàng<span>`);
                                                    }
                                                    $(".detail-price-label .price span").text(new Intl.NumberFormat({style: "currency"}).format(price))
                                                }
                                                let photo_swipe = "";
                                                for (let i = 1; i < color.gallery_images.length; i++) {
                                                photo_swipe += `<li>
                                                                    <div class="photoswipeImages" data-pswp-uid="2">
                                                                    <span data-src="//img.muji.net/img/item/4550583095635_01_1260.jpg" data-size="1260x1260">
                                                                        <a href="${color.gallery_images[i]}" data-pswp-width="600" data-pswp-height="600" target="_blank">
                                                                            <img id="${i}" src="${color.gallery_images[i]}" class="picture--mainimage" alt="" width="400" height="400">
                                                                        </a>    
                                                                    </span>
                                                                    </div>
                                                                </li>`;
                                                }
                                                $("#product-detail .detail-product-photoswipe ul").html(photo_swipe);
                                                $("#product-detail #color-name").text(`${color.color_name}`);
                                            })                                              
                                        })
                                    </script>
                                </dl>
                            </div>
                            <?php if(isset($colors[$color_selected_index]->sizes)):  ?>
                                <div class="detail-product-size">
                                    <dl class="size">
                                        <dt>KÍCH CỠ</dt>
                                        <script>
                                            function selectSize(stock,size)
                                            {
                                                if(stock <= 0)
                                                {
                                                    return;
                                                }
                                                $("#stock-notification span").text(stock);
                                            }
                                        </script>
                                        <?php foreach($colors[$color_selected_index]->sizes as $size): ?>
                                            <dd>
                                                <span onclick="selectSize(<?php echo $size->stock ?>,'<?php echo $size->size ?>')" <?php echo $size->stock <= 0 ? 'class="out-stock"' : ($size->size === $selected_size ? "class='selected'" : "") ?>>
                                                    <?php echo $size->size ?>
                                                </span>
                                            </dd>
                                        <?php endforeach; ?>                         
                                    </dl>
                                </div>
                                <script>
                                    const sizes = <?php echo json_encode($colors[$color_selected_index]->sizes); ?>;
                                    $(".detail-product-size .size span").each(function(index,item){
                                        $(item).click(function(){
                                            if(sizes[index].stock > 0){
                                                $(".detail-product-size .size span").removeClass("selected");
                                                $(item).addClass("selected");
                                                size = sizes[index].size;
                                                selected_size = size;
                                                price = sizes[index].price;
                                            }
                                        })
                                    })
                                </script>
                            <?php endif; ?>
                        <?php elseif(isset($product->sizes)): ?>
                            <div class="detail-product-size">
                                <dl class="size">
                                    <dt>KÍCH THƯỚC</dt>
                                    <?php foreach($product->sizes as $size): ?>
                                        <dd>
                                            <span <?php echo $size->stock <= 0 ? 'class="out-stock"' : ($size->size === $selected_size ? 'class="selected"' : "") ?>>
                                                <?php echo $size->size ?>
                                            </span>
                                        </dd>
                                    <?php endforeach; ?>                         
                                </dl>
                            </div>
                            <script>
                                p_image = "<?php echo $product->images[0]; ?>";
                                must_pick_size = true;
                                const sizes = <?php echo json_encode($product->sizes) ?>;
                                price = sizes.find((item)=>item.size === size)?.price;
                                $(".detail-product-size .size dd span").each((index,item) => {
                                    $(item).click(function(){
                                        if(sizes[index].stock > 0){
                                            if(sizes[index].stock > 0){
                                                if($("#stock-notification").children("span#in-stock").length > 0){
                                                    $("#stock-notification span").text(sizes[index].stock);
                                                }else{
                                                    $("#stock-notification").html(`Còn <span id="in-stock" style="color:#af2522">${sizes[index].stock}</span> sản phẩm`);
                                                }
                                            }else{
                                                $("#stock-notification").html(`<span id="out-stock" style="color:#af2522">Tạm hết hàng<span>`);
                                            }
                                            $(".detail-product-size .size span").removeClass("selected");
                                            $(this).addClass("selected");
                                            size = sizes[index].size;
                                            price = sizes[index].price;
                                            $(".detail-price-label .price span").text(new Intl.NumberFormat({style: "currency"}).format(price))
                                        }
                                    })
                                });
                            </script>
                        <?php else: ?>    
                            <script>
                                p_image = "<?php echo $product->images[0]; ?>";
                                price = <?php echo $product->p_price; ?>;
                            </script>    
                        <?php endif; ?>
                        <div style="font-size:14px" id="stock-notification" class="mb-3">
                            <?php if($out_of_stock) : ?>
                                <span id="out-stock" style="color:#af2522">Tạm hết hàng<span>
                            <?php else: ?>
                                Còn <span id="in-stock" style="color:#af2522"><?php echo $available_stock ?></span> sản phẩm
                            <?php endif; ?>    
                           
                        </div> 
                        <?php if(isset($product->size_chart)): ?>
                            <div id="size-chart-open" style="cursor: pointer;display:inline-block" class="mb-3">
                                <img style="width:40px;height:auto" src="<?php echo url("client_assets/images/size_chart.png") ?>" alt="">
                                <span style="text-decoration: underline;">bảng kích thước</span>
                            </div>
                            <div id="sizeChartModal">
                                <div class="table-responsive">
                                    <?php $product->size_chart->display(); ?>
                                </div>
                                <span id="close-chart">
                                    <svg width="35" height="35" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div id="actions" class="d-flex">
                            <button id="stores" class="btn btn-outline border-radius-none col">Tìm cửa hàng có sản phẩm</button>
                        </div>
                        <div class="d-flex mt-2" style="gap:10px">
                            <button id="buy-now" class="btn btn-secondary border-radius-none col">Mua ngay</button>
                            <button id="add-to-cart" class="btn btn-outline border-radius-none col">Thêm vào giỏ hàng</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="looking-stores">
                <div id="stores-content" class="bg-white position-relative px-2">
                    <div id="store-content-head">
                        <div class="stores-content-top">
                            <div class="text-center title mt-2">Tìm cửa hàng có sản phẩm</div>
                            <div style="position:absolute;top:5px;right:5px;cursor:pointer" id="close-looking-stores" class="close-pickup">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.49589 7.49508C7.55163 7.43914 7.61787 7.39476 7.6908 7.36448C7.76373 7.33419 7.84192 7.3186 7.92089 7.3186C7.99986 7.3186 8.07805 7.33419 8.15098 7.36448C8.22391 7.39476 8.29015 7.43914 8.34589 7.49508L11.5199 10.6711L14.6959 7.49508C14.8086 7.38236 14.9615 7.31904 15.1209 7.31904C15.2803 7.31904 15.4332 7.38236 15.5459 7.49508C15.6586 7.60779 15.7219 7.76067 15.7219 7.92008C15.7219 8.07948 15.6586 8.23236 15.5459 8.34508L12.3689 11.5191L15.5449 14.6951C15.6571 14.808 15.7201 14.9608 15.7201 15.1201C15.7201 15.2793 15.6571 15.4321 15.5449 15.5451C15.4891 15.601 15.4229 15.6454 15.35 15.6757C15.277 15.706 15.1989 15.7215 15.1199 15.7215C15.0409 15.7215 14.9627 15.706 14.8898 15.6757C14.8169 15.6454 14.7506 15.601 14.6949 15.5451L11.5199 12.3681L8.34489 15.5441C8.23217 15.6568 8.07929 15.7201 7.91989 15.7201C7.76048 15.7201 7.60761 15.6568 7.49489 15.5441C7.38217 15.4314 7.31885 15.2785 7.31885 15.1191C7.31885 14.9597 7.38217 14.8068 7.49489 14.6941L10.6719 11.5191L7.49589 8.34408C7.43995 8.28833 7.39557 8.2221 7.36529 8.14917C7.335 8.07624 7.31942 7.99805 7.31942 7.91908C7.31942 7.84011 7.335 7.76192 7.36529 7.68899C7.39557 7.61605 7.43995 7.55082 7.49589 7.49508Z" fill="#25282B"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-5 col-md-3 pe-0">
                                <?php if(isset($product->colors_sizes) || isset($product->colors)): ?>
                                    <img id="color-image" style="width:100%;" src="<?php echo (isset($product->colors_sizes) ? $product->colors_sizes[0] : $product->colors[0])->gallery_images[0] ?>" alt="">
                                <?php else: ?>
                                    <img id="color-image" style="width:100%;" src="<?php echo $product->images[0] ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <div class="col-7 col-md-9">
                                <div class="product-name">
                                    <?php echo $product->p_name; ?>
                                </div>
                                <div class="product-variant row">
                                    <?php if(isset($product->colors_sizes)): ?>
                                        <div class="col-md-6">
                                            <label class="title-attr">Màu sắc:</label>
                                            <select name="color" class="form-select">
                                                <?php foreach($product->colors_sizes as $color): ?>
                                                    <option data-image="<?php echo $color->gallery_images[0]; ?>" value="<?php echo $color->id; ?>"><?php echo $color->color_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="title-attr">Kích cỡ:</label>
                                            <select name="size" class="form-select col">
                                                <?php foreach($product->colors_sizes[0]->sizes as $size): ?>
                                                    <option value="<?php echo $size->size; ?>"><?php echo $size->size ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php elseif(isset($product->colors)): ?>
                                        <div class="col-12">
                                            <label class="title-attr">Màu sắc:</label>
                                            <select name="color" class="form-select">
                                                <?php foreach($product->colors as $color): ?>
                                                    <option data-image="<?php echo $color->gallery_images[0]; ?>" value="<?php echo $color->id; ?>"><?php echo $color->color_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php elseif(isset($product->sizes)): ?>
                                        <div class="col-12">
                                            <label class="title-attr">Kích cỡ:</label>
                                            <select name="size" class="form-select">
                                                <?php foreach($product->sizes as $size): ?>
                                                    <option value="<?php echo $size->size; ?>"><?php echo $size->size; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php else: ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-2">
                            <div class="title-group">Chọn khu vực cần tìm</div>
                            <div class="row align-items-center">
                                <div class="col-6 col-md-4">
                                    <select name="province" class="form-select">
                                        <option selected disabled>Tỉnh/thành</option>
                                        <?php foreach($provinces as $item): ?>
                                            <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <select name="district" class="form-select">
                                        <option selected disabled>Quận/huyện</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 mt-md-0 mt-2">
                                    <button id="search-stores" class="btn btn-secondary w-100" style="padding:0.44rem !important">Tìm cửa hàng</button>
                                </div>
                            </div>
                            <div style="display:none" class="note-result mt-2 mb-3">Sản phẩm hiện có tại <span id="totalstore">1</span> cửa hàng</span></div>
                        </div>
                    </div>
                    <div id="list-store" style="overflow-y:auto;height:200px"></div>
                </div>
            </div>
            <?php if(count($related_products) > 0): ?>
            <div id="recent-viewed-products" class="collection-content mt-5">
                <div style="font-weight: 500;font-size:24px;text-align:center" class="mb-4 mt-3">Sản phẩm liên quan</div>
                    <div class="collection recent-viewed-products owl-carousel owl-theme">
                    <?php foreach($related_products as $p): ?>
                            <div class="collection-item">
                                <a href="<?php echo url("product/detail/{$p->id}") ?>">
                                    <img width="187.5" height="187.5" loading="lazy" src="<?php echo $p->thumbnail ?>" style="display: inline;">
                                    <div class="title"><?php echo $p->p_name ?></div>
                                </a>
                                <div class="price">
                                    <div>
                                        <span class="num"><?php echo number_format($p->p_price,0,"",".") ?></span><span class="currency">đ</span>
                                    </div>
                                    <ul style="align-items:center" class="colors">
                                        <?php 
                                            if(isset($p->colors_sizes)){
                                                $colors = $p->colors_sizes;
                                            }elseif(isset($p->colors)){
                                                $colors = $p->colors;
                                            }else{
                                                $colors = [];
                                            }
                                        ?>
                                        <?php if(count($colors) <= 3): ?>
                                            <?php foreach($colors as $color): ?>
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
                                            <?php foreach($colors as $color): ?>
                                            <li>
                                                <span>
                                                    <img src="<?php echo url($color->color_image) ?>" alt="">
                                                </span>
                                            </li>
                                            <?php if($break_index === 2): ?>
                                            <li>
                                                <span class="num-colors">+<?php echo count($colors) - 3; ?></span>
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
            <?php endif; ?>
            <?php if(count($recent_viewed_products) > 0) : ?>
            <div id="recent-viewed-products" class="collection-content mt-5">
                <div style="font-weight: 500;font-size:24px;text-align:center" class="mb-4 mt-3">Đã xem gần đây</div>
                    <div class="collection recent-viewed-products owl-carousel owl-theme">
                    <?php foreach($recent_viewed_products as $p): ?>
                            <?php 
                                if($p->id === $product->id){
                                    continue;
                                }    
                            ?>
                            <div class="collection-item">
                               <a href="<?php echo url("product/detail/{$p->id}") ?>">
                                    <img width="187.5" height="187.5" loading="lazy" src="<?php echo $p->images[0] ?>" style="display: inline;">
                                    <div class="title"><?php echo $p->p_name ?></div>
                                </a>
                                <div class="price">
                                    <div>
                                        <span class="num"><?php echo number_format($p->p_price,0,"",".") ?></span><span class="currency">đ</span>
                                    </div>
                                    <ul style="align-items:center" class="colors">
                                        <?php 
                                            if(isset($p->colors_sizes)){
                                                $colors = $p->colors_sizes;
                                            }elseif(isset($p->colors)){
                                                $colors = $p->colors;
                                            }else{
                                                $colors = [];
                                            }
                                        ?>
                                        <?php if(count($colors) <= 3): ?>
                                            <?php foreach($colors as $color): ?>
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
                                            <?php foreach($colors as $color): ?>
                                            <li>
                                                <span>
                                                    <img src="<?php echo url($color->color_image) ?>" alt="">
                                                </span>
                                            </li>
                                            <?php if($break_index === 2): ?>
                                            <li>
                                                <span class="num-colors">+<?php echo count($colors) - 3; ?></span>
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
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
    const cart_url = "<?php echo url("cart"); ?>";
    const checkout_url = "<?php echo url("checkout"); ?>";
</script>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>
