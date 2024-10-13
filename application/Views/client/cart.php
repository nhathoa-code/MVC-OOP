<?php include(APP_PATH . "/application/Views/client/header.php") ?>
    <script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
    <link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>"> 
    <section id="content">
        <div class="container-fluid" style="height: 100%;">
            <div class="cart-content" style="height: 100%;">
            <?php if(isset($cart) && $totalItems > 0): ?>
                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="css-vf0xkj">
                            <p class="css-1y6b781">GIỎ HÀNG</p>
                            <p class="css-1xo0mgw">(<span id="cart-number"><?php echo $totalItems ?></span> sản phẩm)</p>
                        </div>
                    </div>
                </div>
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div id="cart-table">
                        <div id="cart-header">
                            <div class="row">
                                <div class="col-12 col-sm-5">
                                    <span>Sản phẩm</span>
                                </div>
                                <div class="col-2 d-none d-sm-block">
                                    <span>Giá</span>
                                </div>
                                <div class="col-2 d-none d-sm-block">
                                    <span>Số lượng</span>
                                </div>
                                <div class="col d-none d-sm-block text-end">
                                    <span>Tổng tiền</span>
                                </div>
                            </div>
                        </div>
                        <div id="cart-body">
                            <?php foreach(array_reverse($cart) as $index => $item): ?>
                                <div class="row cart-item-<?php echo $index ?>">
                                    <div class="col-12 col-sm-5">
                                        <div class="cart-p-name-wrap row">
                                            <div class="cart-p-image col-3 col-sm-4 px-0">
                                                <a href="<?php echo url("product/detail/{$item['p_id']}") ?>"><img src="<?php echo $item['p_image'] ?>" alt=""></a>  
                                            </div>
                                            <div class="cart-p-name col">
                                                <p class="name">
                                                    <a href="<?php echo url("product/detail/{$item['p_id']}") ?>"><?php echo $item['p_name'] ?></a>
                                                </p>
                                                <ul class="cart-variations d-flex">
                                                    <?php if(isset($item['size'])): ?>
                                                    <li>
                                                        <span class="value"><?php echo $item['size'] ?></span>
                                                    </li>
                                                    <?php if(isset($item['color_id'])): ?>
                                                    <div class="vr mx-1 mx-2"></div>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if(isset($item['color_id'])): ?>
                                                    <li class="d-flex align-items-center">
                                                        <img class="color-image me-1" src="<?php echo $item['color_image'] ?>" alt="">
                                                        <span class="value"><?php echo $item['color_name'] ?></span>
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                                <!-- for mobile screen -->
                                                <div class="cart-p-price d-block d-sm-none">
                                                    <p class="price"><?php echo number_format($item["price"],0,"",".") ?>đ</p>
                                                </div>
                                                <div class="d-flex justify-content-between d-block d-sm-none">
                                                    <div class="cart-item-qty" style="height:30px">
                                                        <button <?php echo isMobileDevice() ? "style='color:black;width:0px'" : "" ?> data-index="<?php echo $index ?>" data-sign="-" type="button" class="minus toggle-qty d-flex align-items-center justify-content-center ps-0">
                                                            <span>−</span>
                                                        </button>
                                                        <input disabled value="<?php echo $item['quantity'] ?>" type="text" title="Số lượng" class="input-text qty cart-qty-<?php echo $index ?>">
                                                        <button <?php echo isMobileDevice() ? "style='color:black;width:0px'" : "" ?> data-index="<?php echo $index ?>" data-sign="+" type="button" class="plus toggle-qty d-flex align-items-center justify-content-center">
                                                            <span>+</span>
                                                        </button>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-between h-100">
                                                        <p class="cart-item-total"><?php echo number_format($item['price'] * $item['quantity'],0,"",".") ?>đ</p>
                                                        <svg data-index="<?php echo $index ?>" class="delete-cart-item" style="align-self:end;cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <!-- for mobile screen -->   
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="col-2 d-none d-sm-block">
                                        <div class="cart-p-price">
                                            <p class="price"><?php echo number_format($item["price"],0,"",".") ?>đ</p>
                                            <!-- <p class="price-through">429.000 VND</p> -->
                                        </div>   
                                    </div>
                                    <div class="col-2 d-none d-sm-block">
                                        <div class="cart-item-qty">
                                            <button data-index="<?php echo $index ?>" data-sign="-" type="button" class="minus toggle-qty">
                                                <span>−</span>
                                            </button>
                                            <input disabled value="<?php echo $item['quantity'] ?>" type="text" title="Số lượng" class="input-text qty cart-qty-<?php echo $index ?>">
                                            <button data-index="<?php echo $index ?>" data-sign="+" type="button" class="plus toggle-qty">
                                                <span>+</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col d-none d-sm-block">
                                        <div class="d-flex flex-column justify-content-between h-100">
                                            <p class="cart-item-total"><?php echo number_format($item['price'] * $item['quantity'],0,"",".") ?>đ</p>
                                            <svg data-index="<?php echo $index ?>" class="delete-cart-item" style="align-self:end;cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div id="cart-subtotal" class="sticky mt-3 mt-lg-0" style="top:78.42px">
                        <div class="css-1cxjnkj">
                            <p class="css-1y6b781">ĐƠN HÀNG</p>
                            <div class="MuiBox-root css-vk76k8">
                                <p class="MuiTypography-root MuiTypography-body1 css-1oom1dr">Tổng giá trị đơn hàng</p>
                                <p id="number" class="MuiTypography-root MuiTypography-body1 css-13a9of"><?php echo number_format($subtotal,0,"",".") ?>đ</p>
                            </div>
                            <a href="<?php echo url("checkout") ?>" class="contained css-1coq16z" tabindex="0" type="button">TIẾP TỤC THANH TOÁN  ➔
                                <span class="MuiTouchRipple-root css-w0pj6f"></span>
                            </a>
                            <p class="MuiTypography-root MuiTypography-body1 css-gyrlz6">Dùng mã giảm giá của 
                                <span style="font-weight: 600; color: rgb(201, 33, 39);">VNH</span> 
                                trong bước tiếp theo
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div style="padding-top:50px" class="text-center">
                    <div style="font-weight: 600;font-size:20px;margin-top:20px">Chưa có sản phẩm trong giỏ hàng</div>
                    <a class="btn btn-secondary mt-2" href="<?php echo url("/"); ?>">Tiếp tục mua sắm</a>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </section>
    <script>
        const add_to_cart_url = "<?php echo url("cart/add") ?>";
        const delete_cart_item_url = "<?php echo url("cart/delete") ?>";
        const update_cart_item_url = "<?php echo url("cart/update") ?>";
    </script>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>