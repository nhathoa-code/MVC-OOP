<?php include(APP_PATH . "/application/Views/client/header.php") ?>
    <?php
        if(isset($message["message"])) :
    ?>
    <script>
        notif({
            msg:"<?php echo $message['message']; ?>",
            type:"warning",
            position:"center",
            height :"auto",
            top: 80,
            timeout: 10000,
            animation:'slide'
        });
    </script>
    <?php endif; ?>    
    <section id="content">
        <div class="container-fluid">
            <div class="checkout-content">
                <form id="checkout-form" class="row" method="post" action="<?php echo url("checkout"); ?>">
                    <div class="col-12 col-lg-8">
                        <div style="padding:10px;background-color:#fff;min-height:300px;border-radius:5px">
                            <?php if(!login()): ?>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label">Họ tên<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                                    <input type="text" value="<?php echo $old["name"] ?? "" ?>" class="form-control" placeholder="Nhập họ tên" name="name">
                                    <?php if(isset($errors["name"])): ?>
                                        <?php foreach($errors["name"] as $error): ?>
                                            <span class="error"><?php echo $error ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label">Email<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                                    <input type="text" value="<?php echo $old["email"] ?? "" ?>" class="form-control" placeholder="Nhập địa chỉ email" name="email">
                                    <?php if(isset($errors["email"])): ?>
                                        <?php foreach($errors["email"] as $error): ?>
                                            <span class="error"><?php echo $error ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col">
                                    <label class="form-label">SĐT<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                                    <input type="text" value="<?php echo $old["phone"] ?? "" ?>" class="form-control" placeholder="Nhập số điện thoại" name="phone">
                                    <?php if(isset($errors["phone"])): ?>
                                        <?php foreach($errors["phone"] as $error): ?>
                                            <span class="error"><?php echo $error ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm">
                                    <label class="form-label">TỈNH / THÀNH PHỐ<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                                    <select id="province" class="form-select" name="province"></select>
                                    <?php if(isset($errors["province"])): ?>
                                        <?php foreach($errors["province"] as $error): ?>
                                            <span class="error"><?php echo $error ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                </div>
                                <div class="col-12 col-sm">
                                    <label class="form-label">QUẬN / HUYỆN<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                                    <select id="district" class="form-select" name="district"></select>
                                    <?php if(isset($errors["district"])): ?>
                                        <?php foreach($errors["district"] as $error): ?>
                                            <span class="error"><?php echo $error ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                </div>
                                <div class="col-12 col-sm">
                                    <label class="form-label">PHƯỜNG / XÃ<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                                    <select id="ward" class="form-select" name="ward"></select>
                                    <?php if(isset($errors["ward"])): ?>
                                        <?php foreach($errors["ward"] as $error): ?>
                                            <span class="error"><?php echo $error ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col">
                                    <label class="form-label">Địa chỉ<span aria-hidden="true" class="MuiInputLabel-asterisk css-sp68t1"> *</span></label>
                                    <input type="text" value="<?php echo $old["address"] ?? "" ?>" class="form-control" placeholder="Nhập địa chỉ nhận hàng" name="address">
                                    <?php if(isset($errors["address"])): ?>
                                        <?php foreach($errors["address"] as $error): ?>
                                            <span class="error"><?php echo $error ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>    
                                </div>
                            </div>
                            <?php else: ?>
                            <?php if(!empty($addresses)): ?> 
                                    <?php
                                        $addresses = array_map(function($item){
                                            $item->meta_value = unserialize($item->meta_value);
                                            return $item;
                                        },$addresses);
                                        $default_address = array_find($addresses,function($item){
                                        return array_key_exists("default",$item->meta_value);
                                        });    
                                        if($default_address):
                                            echo "<script>
                                                    address_id = $default_address->id;
                                                </script>";
                                        $default_address = $default_address->meta_value;
                                    ?>
                                        <div id="user-address" class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center" style="font-weight: 500;"><?php echo $default_address['name'] ?><span style="font-size:13px;font-weight:normal;color:#800019">Mặc định</span></div>
                                            <div style="font-size: 14px;">
                                                Địa chỉ: <?php echo "{$default_address['address']}, {$default_address['ward']}, {$default_address['district']}, {$default_address['province']}" ?>
                                            </div>
                                            <div style="font-size: 14px;">
                                                Số điện thoại: <?php echo $default_address['phone']; ?>
                                            </div>
                                        </div>
                                        <div id="address-toggle" class="text-end">
                                            <a style="font-size:15px" href="<?php echo url("user/address/add") ?>">Thêm địa chỉ mới</a> hoặc <a data-bs-toggle="modal" data-bs-target="#listAddressModal" style="font-size:15px" href="javascript:void(0)">Chọn địa chỉ khác</a>
                                        </div>
                                        <input type="hidden" name="province" value="<?php echo $default_address['province'] ?>">
                                        <input type="hidden" name="district" value="<?php echo $default_address['district'] ?>">
                                        <input type="hidden" name="ward" value="<?php echo $default_address['ward'] ?>">
                                        <input type="hidden" name="address" value="<?php echo $default_address['address'] ?>">
                                        <input type="hidden" name="name" value="<?php echo $default_address['name'] ?>">
                                        <input type="hidden" name="phone" value="<?php echo $default_address['phone'] ?>">
                                        <input type="hidden" name="email" value="<?php echo getUser()->email ?>">
                                        <script>
                                            district_id = <?php echo $default_address['district_id']; ?>

                                            ward_code = <?php echo $default_address['ward_code']; ?>;
                                        </script>
                                    <?php else: ?>
                                        <div id="user-address" class="mb-2">
                                            <div>Bạn chưa có địa chỉ giao hàng mặc đinh, vui lòng <a style="font-size:15px" href="<?php echo url("user/address/add") ?>">thêm địa chỉ mới</a> hoặc <a data-bs-toggle="modal" data-bs-target="#listAddressModal" style="font-size:15px" href="javascript:void(0)">chọn địa chỉ khác</a></div>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div id="user-address" class="mb-2">
                                    <div>Bạn chưa có địa chỉ giao hàng,vui lòng thêm địa chỉ giao hàng <a href="<?php echo url("user/address/add") ?>">tại đây</a></div>
                                    </div>
                                <?php endif; ?>    
                            <?php endif; ?>    
                            <div class="row g-3 mb-4">
                                <div class="col">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea class="form-control" placeholder="Nhập ghi chú của bạn" name="note" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="css-uzo88x col-12">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.8445 14.8124C21.6705 14.8128 21.5038 14.8821 21.3809 15.0053C21.258 15.1285 21.189 15.2954 21.189 15.4694V20.0639H2.8125V12.1934H10.032C10.2006 12.1853 10.3597 12.1126 10.4762 11.9905C10.5926 11.8683 10.6576 11.7059 10.6576 11.5371C10.6576 11.3683 10.5926 11.206 10.4762 11.0838C10.3597 10.9616 10.2006 10.8889 10.032 10.8809H2.8125V8.25586H10.032C10.2006 8.2478 10.3597 8.17514 10.4762 8.05296C10.5926 7.93077 10.6576 7.76842 10.6576 7.59961C10.6576 7.4308 10.5926 7.26845 10.4762 7.14626C10.3597 7.02408 10.2006 6.95142 10.032 6.94336H2.8125C2.4644 6.94336 2.13056 7.08165 1.88442 7.32779C1.63828 7.57393 1.5 7.90776 1.5 8.25586L1.5 20.0684C1.5 20.4165 1.63828 20.7503 1.88442 20.9964C2.13056 21.2426 2.4644 21.3809 2.8125 21.3809H21.1875C21.5356 21.3809 21.8694 21.2426 22.1156 20.9964C22.3617 20.7503 22.5 20.4165 22.5 20.0684V15.4754C22.5008 15.3887 22.4844 15.3028 22.4519 15.2225C22.4193 15.1421 22.3712 15.0691 22.3102 15.0074C22.2493 14.9458 22.1768 14.8969 22.0969 14.8634C22.0169 14.8299 21.9312 14.8126 21.8445 14.8124Z" fill="black"></path><path d="M7.40688 14.8184H4.78188C4.61325 14.8264 4.4542 14.8991 4.33772 15.0213C4.22124 15.1435 4.15625 15.3058 4.15625 15.4746C4.15625 15.6434 4.22124 15.8057 4.33772 15.9279C4.4542 16.0501 4.61325 16.1228 4.78188 16.1309H7.40688C7.5755 16.1228 7.73454 16.0501 7.85102 15.9279C7.9675 15.8057 8.03249 15.6434 8.03249 15.4746C8.03249 15.3058 7.9675 15.1435 7.85102 15.0213C7.73454 14.8991 7.5755 14.8264 7.40688 14.8184Z" fill="black"></path><path d="M22.1029 5.02025L17.5099 3.05225C17.4278 3.01776 17.3396 3 17.2504 3C17.1613 3 17.0731 3.01776 16.9909 3.05225L12.398 5.02175C12.2809 5.07263 12.1812 5.15642 12.1109 5.26294C12.0406 5.36947 12.0027 5.49412 12.002 5.62175V8.24375C12.002 11.8543 13.3369 13.9648 16.9249 16.0318C17.0245 16.0886 17.1373 16.1185 17.252 16.1185C17.3666 16.1185 17.4793 16.0886 17.5789 16.0318C21.1654 13.9753 22.5004 11.8648 22.5004 8.24375V5.61875C22.4995 5.49117 22.4614 5.36663 22.3908 5.26035C22.3202 5.15407 22.2201 5.07064 22.1029 5.02025ZM21.1879 8.24825C21.1879 11.2782 20.1844 12.9477 17.2504 14.7057C14.3164 12.9432 13.3129 11.2752 13.3129 8.24825V6.05825L17.2504 4.37075L21.1879 6.05825V8.24825Z" fill="black"></path><path d="M19.629 7.08002C19.4928 6.97217 19.3196 6.92241 19.1469 6.9415C18.9742 6.9606 18.8159 7.04702 18.7065 7.18203L16.644 9.76204L15.828 8.54104C15.7803 8.46934 15.7189 8.40773 15.6473 8.35977C15.5757 8.3118 15.4954 8.27841 15.4109 8.26149C15.3264 8.24456 15.2395 8.24445 15.1549 8.26114C15.0704 8.27784 14.99 8.31101 14.9183 8.35878C14.8466 8.40655 14.785 8.46798 14.737 8.53955C14.6891 8.61113 14.6557 8.69144 14.6388 8.77592C14.6218 8.8604 14.6217 8.94738 14.6384 9.0319C14.6551 9.11643 14.6883 9.19684 14.736 9.26854L16.0485 11.238C16.1053 11.3254 16.1819 11.3981 16.2721 11.4502C16.3623 11.5023 16.4635 11.5324 16.5676 11.538H16.5946C16.6932 11.5379 16.7905 11.5156 16.8793 11.4728C16.9681 11.4299 17.0461 11.3676 17.1075 11.2905L19.7325 8.01004C19.7874 7.94243 19.8283 7.86458 19.8528 7.78104C19.8773 7.6975 19.885 7.60992 19.8754 7.52339C19.8657 7.43685 19.839 7.35308 19.7967 7.27697C19.7544 7.20086 19.6974 7.13391 19.629 7.08002Z" fill="black"></path>
                                    </svg>
                                    <div style="margin-left: 10px;">PHƯƠNG THỨC THANH TOÁN</div>
                                </div>
                                <div>
                                    <div style="gap:10px" class="form-check mb-4 d-flex align-items-center">
                                        <input style="width:13px;height:13px" class="form-check-input" type="radio" name="payment_method" id="method1" value="cod" checked>
                                        <label class="form-check-label" for="method1">
                                            <img style="margin-right: 10px;width:auto" src="<?php echo url("client_assets/images/cod.svg") ?>" alt="">
                                            Thanh toán khi nhận hàng (COD)
                                        </label>
                                    </div>
                                    <div style="gap:10px" class="form-check d-flex align-items-center">
                                        <input style="width:13px;height:13px" class="form-check-input" type="radio" name="payment_method" value="vnpay" id="method2">
                                        <label class="form-check-label" for="method2">
                                            <img style="margin-right: 10px;width:auto" src="<?php echo url("client_assets/images/vnpay.svg") ?>" alt="">
                                            Thanh toán Online qua VNPAY
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="css-uzo88x col-12">
                                    <svg fill="#000000" height="24" width="24" viewBox="0 0 491.1 491.1">
                                        <g transform="translate(0 -540.36)">
                                            <g>
                                                <g>
                                                    <path d="M401.5,863.31c-12,0-23.4,4.7-32,13.2c-8.6,8.6-13.4,19.8-13.4,31.8s4.7,23.2,13.4,31.8c8.7,8.5,20,13.2,32,13.2
                                                        c24.6,0,44.6-20.2,44.6-45S426.1,863.31,401.5,863.31z M401.5,933.31c-13.8,0-25.4-11.4-25.4-25s11.6-25,25.4-25
                                                        c13.6,0,24.6,11.2,24.6,25S415.1,933.31,401.5,933.31z"/>
                                                    <path d="M413.1,713.41c-1.8-1.7-4.2-2.6-6.7-2.6h-51.3c-5.5,0-10,4.5-10,10v82c0,5.5,4.5,10,10,10h81.4c5.5,0,10-4.5,10-10v-54.9
                                                        c0-2.8-1.2-5.5-3.3-7.4L413.1,713.41z M426.5,792.81h-61.4v-62.1h37.4l24,21.6V792.81z"/>
                                                    <path d="M157.3,863.31c-12,0-23.4,4.7-32,13.2c-8.6,8.6-13.4,19.8-13.4,31.8s4.7,23.2,13.4,31.8c8.7,8.5,20,13.2,32,13.2
                                                        c24.6,0,44.6-20.2,44.6-45S181.9,863.31,157.3,863.31z M157.3,933.31c-13.8,0-25.4-11.4-25.4-25s11.6-25,25.4-25
                                                        c13.6,0,24.6,11.2,24.6,25S170.9,933.31,157.3,933.31z"/>
                                                    <path d="M90.6,875.61H70.5v-26.6c0-5.5-4.5-10-10-10s-10,4.5-10,10v36.6c0,5.5,4.5,10,10,10h30.1c5.5,0,10-4.5,10-10
                                                        S96.1,875.61,90.6,875.61z"/>
                                                    <path d="M141.3,821.11c0-5.5-4.5-10-10-10H10c-5.5,0-10,4.5-10,10s4.5,10,10,10h121.3C136.8,831.11,141.3,826.71,141.3,821.11z"
                                                        />
                                                    <path d="M30.3,785.01l121.3,0.7c5.5,0,10-4.4,10.1-9.9c0.1-5.6-4.4-10.1-9.9-10.1l-121.3-0.7c-0.1,0-0.1,0-0.1,0
                                                        c-5.5,0-10,4.4-10,9.9C20.3,780.51,24.8,785.01,30.3,785.01z"/>
                                                    <path d="M50.7,739.61H172c5.5,0,10-4.5,10-10s-4.5-10-10-10H50.7c-5.5,0-10,4.5-10,10S45.2,739.61,50.7,739.61z"/>
                                                    <path d="M487.4,726.11L487.4,726.11l-71.6-59.3c-1.8-1.5-4-2.3-6.4-2.3h-84.2v-36c0-5.5-4.5-10-10-10H60.5c-5.5,0-10,4.5-10,10
                                                        v73.2c0,5.5,4.5,10,10,10s10-4.5,10-10v-63.2h234.8v237.1h-82c-5.5,0-10,4.5-10,10s4.5,10,10,10h122.1c5.5,0,10-4.5,10-10
                                                        s-4.5-10-10-10h-20.1v-191.1h80.6l65.2,54l-0.7,136.9H460c-5.5,0-10,4.5-10,10s4.5,10,10,10h20.3c5.5,0,10-4.4,10-9.9l0.8-151.6
                                                        C491,730.91,489.7,728.01,487.4,726.11z"/>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <div style="margin-left: 10px;margin-right:15px">ĐỐI TÁC VẬN CHUYỂN</div>
                                    <img style="width: 70px;" src="<?php echo url("client_assets/images/Logo-GHN.webp") ?>" alt="">
                                </div>
                                <div id="delivery-options">
                                    <div style="gap:10px" class="form-check d-flex align-items-center">
                                        <input style="width:13px;height:13px" class="form-check-input" checked type="radio" name="shipment" value="standard" id="standard">
                                        <label class="form-check-label" for="standard">
                                            Chuyển phát thương mại điện tử
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="css-vf0xkj my-3">
                            <p class="css-1y6b781">GIỎ HÀNG</p>
                            <p class="css-1xo0mgw">(<?php echo $totalItems ?> sản phẩm)</p>
                        </div>
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
                                    <div class="row">
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
                                                    <div class="d-flex justify-content-between align-items-center d-block d-sm-none">
                                                        <span style="font-size:13px">x <?php echo $item['quantity'] ?></span>
                                                        <p class="cart-item-total"><?php echo number_format($item['price'] * $item['quantity'],0,"",".") ?>đ</p>
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
                                            <span style="font-size:13px"><?php echo $item['quantity'] ?></span>
                                        </div>
                                        <div class="col d-none d-sm-block">
                                            <div class="d-flex flex-column justify-content-between h-100">
                                                <p class="cart-item-total"><?php echo number_format($item['price'] * $item['quantity'],0,"",".") ?>đ</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>                    
                    </div>
                    <div class="col-12 col-lg-4 mt-3 mt-lg-0">
                        <div class="css-1w8i4is sticky" style="top:78.42px">
                            <div>
                                <div class="title-form mb-3">ĐƠN HÀNG</div>
                                <?php if(login()): ?>
                                <div class="mb-4">
                                    <div class="label_reward mb-2 d-flex justify-content-between">
                                        <span class="label_text">SỬ DỤNG V-POINT</span>
                                        <span>V-point khả dụng: <span style="color:green"><?php echo $v_point ?></span></span>
                                    </div>
                                    <div class="input-group">
                                        <input name="v-point" style="border-radius: 0;" type="text" class="form-control" placeholder="Nhập số v-point">
                                        <span style="border-radius: 0;cursor:pointer" class="input-group-text" id="apply-point">SỬ DỤNG</span>
                                    </div>
                                    <div id="point-error" style="color:red;font-size:14px"></div>
                                </div>
                                <?php endif; ?>
                                <div class="mb-4">
                                    <div class="label_reward mb-2">
                                        <span class="label_text">MÃ PHIẾU GIẢM GIÁ</span>
                                    </div>
                                    <div class="input-group">
                                        <input name="coupon_code" style="border-radius: 0;" type="text" class="form-control" placeholder="Mã phiếu giảm giá">
                                        <span style="border-radius: 0;cursor:pointer" class="input-group-text" id="apply-coupon">ÁP DỤNG</span>
                                    </div>
                                    <div id="coupon-error" style="color:red;font-size:14px"></div>
                                </div>
                            </div>
                            <div class="css-1nymbq0 css-1uds4os">
                                <div class="info_price">
                                    <div class="divider"></div>
                                    <div class="row-info">
                                        <span class="info_title">Tạm tính</span>
                                        <span class="price"><?php echo number_format($subtotal,0,"",".") ?>đ</span>
                                    </div>
                                    <div class="row-info">
                                        <span class="info_title">Phí vận chuyển</span>
                                        <span style="font-size:14px" id="delivery_fee">--</span>
                                    </div>
                                    <div class="row-info coupon-row">
                                        <span class="info_title coupon">Mã giảm giá </span>
                                        <span style="font-size:14px" id="coupon">--</span>
                                    <!-- <span onclick="removeCoupon(this)" style="font-size: 12px;color:#800019;cursor:pointer">(VNHCP111) xóa</span> -->
                                    </div>
                                    <div class="divider"></div>
                                    <div class="row-info">
                                    <span class="info_title">Tổng thanh toán</span>
                                    <p class="price_total css-1iwrego">
                                        <?php echo number_format($subtotal,0,"",".") ?>đ
                                    </p>
                                    </div>
                                    <div class="divider"></div>
                                </div>
                            </div>
                            <div class="css-1uds4os mt-4">
                                <button
                                    id="place-order"
                                    class="btn btn-secondary w-100"
                                    tabindex="0"
                                    type="submit"
                                >
                                    ĐẶT HÀNG<span class="css-w0pj6f"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                </form>
            </div>
        </div>
        <div id="overlay" class="overlay">
            <div class="spinner">
                <div class="cls_loader" style="display: flex">
                    <div class="sk-three-bounce">
                        <div class="sk-child sk-bounce1"></div>
                        <div class="sk-child sk-bounce2"></div>
                        <div class="sk-child sk-bounce3"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>    
    <?php if(login()): ?>
    <!-- Modal -->
    <div class="modal" id="listAddressModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2 text-center w-100 border-bottom-0">
                    <div style="font-weight:500" class="w-100">Sổ địa chỉ</div>
                </div>
                <div class="modal-body p-0 list-address">
                        <?php foreach($addresses as $index => $item): $address = $item->meta_value;?>
                        <div class="address-item mb-4">
                            <div class="d-flex mb-2">
                                <div class="op d-flex align-items-center flex-grow-1">
                                    <div class="fullname"><?php echo $address['name'] ?></div>
                                    <div class="vr mx-1"></div>
                                    <div class="phone"><?php echo $address['phone'] ?></div>
                                </div>
                                <div class="op">
                                    <a
                                    href="<?php echo url("user/address/edit?id={$item->id}") ?>"
                                    aria-label="Link"
                                    class="link"
                                    >Thay đổi</a
                                    >
                                </div>
                            </div>
                            <div class="row mx-0">
                                <div class="col-11 px-0">
                                    <div class="op">
                                    <div class="address"><?php echo "{$address['address']}, {$address['ward']}, {$address['district']}, {$address['province']}" ?></div>
                                    </div>
                                    <?php if(array_key_exists("default",$address)): ?>
                                    <div class="op mt-2"> 
                                        <div class="ar-status">Địa chỉ mặc định</div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-1 px-0">
                                    <input <?php echo array_key_exists("default",$address) ? "checked" : "" ?> style="border-color: black;" class="form-check-input" type="radio" data-id="<?php echo $item->id ?>" name="address">
                                </div>
                                
                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="gap:10px" class="modal-footer row p-0 m-0 py-2 border-top-0">
                    <button type="button" class="btn btn-outline col m-0" data-bs-dismiss="modal">Hủy</button>
                    <button id="address-confirm" type="button" class="btn btn-secondary col m-0">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <script>
        const cart_checkout_url = "<?php echo url("cart/checkout") ?>";
        const apply_coupon_url = "<?php echo url("apply/coupon") ?>";
        const apply_point_url = "<?php echo url("apply/point") ?>";
        const add_address_url = "<?php echo url("user/address/add") ?>";
        const check_out = true;
        const subtotal = <?php echo $subtotal ?>;
        $('#checkout-form').on('keydown', 'input', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
        }
      });
    </script>
    <?php if(login()): ?>
    <script>
        const addresses = <?php echo json_encode($addresses) ?>;
    </script>
    <?php endif; ?>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>