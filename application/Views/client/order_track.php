<?php include(APP_PATH . "/application/Views/client/header.php") ?>
<section id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <form style="margin: 0 auto;" class="w-100 mt-3" method="get">
                    <div class="p-3">
                        <div style="text-align:center" class="heading">Kiểm tra thông tin đơn hàng</div>
                        <div class="row mx-0">
                            <div class="col-10 col-md-6 col-xl-5 mx-auto mb-3 p-0">
                                <input style="text-align: center;" value="<?php echo get_query("order_id") ?? "" ?>" type="text" class="form-control" placeholder="Nhập mã đơn hàng" name="order_id">
                            </div>
                        </div>
                        <?php if(isset($message)): ?>
                            <div style="text-align:center;margin-top:-10px;margin-bottom:10px" class="error"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <?php if(isset($not_found)): ?>
                            <div style="text-align:center;margin-top:-10px;margin-bottom:10px" class="error"><?php echo "không tìm thấy đơn hàng"; ?></div>
                        <?php endif; ?>        
                        <div class="col-6 col-md-4 mx-auto row mx-0">
                            <button class="btn btn-secondary p-2" type="submit">Kiểm tra ngay</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if(isset($order)): ?>
            <div class="row">
                <div class="col-12 col-lg-8 mx-auto border px-3 py-2">
                    <div class="d-flex justify-content-between pb-3 border-bottom">
                        <div>
                            <div style="font-weight: 600;"><?php echo "#{$order->id}" ?></div>
                            <div style="font-size: 14px;"><?php echo $order->created_at ?></div>
                        </div>
                        <div style="font-weight:600"><?php echo $status_map[$order->status] ?></div>
                    </div>
                    <div style="color:#800019;font-weight:500" class="border-bottom pb-2 my-2">Thông tin đặt hàng</div>
                    <div class="row">
                        <div class="col-12 col-md">
                            <div style="font-weight:600">Địa chỉ giao hàng</div>
                            <div class="mt-2" style="font-size: 14px;">
                                <div><?php echo $order->name ?> | <?php echo $order->phone ?></div>
                                <div>
                                    <?php 
                                        $address = json_decode($order->address);
                                        echo "{$address->address}, {$address->ward}, {$address->district}, {$address->city}"
                                    ?>
                                </div>
                            </div>
                        
                        </div>
                        <div class="col-12 my-3 col-md my-md-0">
                            <div style="font-weight:600">Phương thức thanh toán</div>
                            <div class="mt-2" style="font-size: 14px;">
                                <div><?php echo $order->payment_method === "cod" ? "Thanh toán khi nhận hàng" : ($order->payment_method === "vnpay" ? "Thanh toán online qua VNPAY" : "") ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-md">
                            <div style="font-weight:600">Trạng thái thanh toán</div>
                            <div class="mt-2" style="font-size: 14px;">
                                <?php echo $order->paid_status; ?>
                            </div>
                        </div>
                    </div>
                    <div style="color:#800019;font-weight:500" class="border-bottom border-top pt-2 pb-2 my-2">Chi tiết đơn hàng</div>
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
                            <?php foreach($order->items as $index => $item): ?>
                                <div class="row">
                                    <div class="col-12 col-sm-5">
                                        <div class="cart-p-name-wrap row">
                                            <div class="cart-p-image col-3 col-sm-4 px-0">
                                                <a href="<?php echo url("product/detail/{$item->p_id}") ?>"><img src="<?php echo $item->image ?>" alt=""></a>  
                                            </div>
                                            <div class="cart-p-name col">
                                                <p class="name">
                                                    <a href="<?php echo url("product/detail/{$item->p_id}") ?>"><?php echo $item->p_name ?></a>
                                                </p>
                                                <ul class="cart-variations">
                                                    <?php if(isset($item->p_size)): ?>
                                                    <li>
                                                        <span class="variation">Kích cỡ:</span>
                                                        <span class="value"><?php echo $item->p_size ?></span>
                                                    </li>
                                                    <?php endif; ?>
                                                    <?php if(isset($item->p_color_id)): ?>
                                                    <li class="d-flex align-items-center">
                                                        <span class="variation">Màu sắc:</span>
                                                        <img class="color-image ms-2 me-1" src="<?php echo url($item->color_image); ?>" alt="">
                                                        <span class="value"><?php echo $item->color_name ?></span>
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                                <!-- for mobile screen -->
                                                <div class="cart-p-price d-block d-sm-none">
                                                    <p class="price"><?php echo number_format($item->p_price,0,"",".") ?> VND</p>
                                                </div>
                                                <div class="d-flex justify-content-between d-block d-sm-none">
                                                    <span><?php echo $item->quantity ?></span>
                                                    <p class="cart-item-total"><?php echo number_format($item->p_price * $item->quantity,0,"",".") ?> VND</p>
                                                </div>
                                                <!-- for mobile screen -->   
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2 d-none d-sm-block">
                                        <div class="cart-p-price">
                                            <p class="price"><?php echo number_format($item->p_price,0,"",".") ?>đ</p>
                                            <!-- <p class="price-through">429.000 VND</p> -->
                                        </div>   
                                    </div>
                                    <div class="col-2 d-none d-sm-block">
                                        <span><?php echo $item->quantity ?></span>
                                    </div>
                                    <div class="col d-none d-sm-block">
                                        <div class="d-flex flex-column justify-content-between h-100">
                                            <p class="cart-item-total"><?php echo number_format($item->p_price * $item->quantity,0,"",".") ?>đ</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="font-size:15px" class="border-top row pt-3">
                            <div class="col-7 col-md-3  offset-md-7">
                                <div class="mb-2">Tạm tính</div>
                                <div class="mb-2">Phí vận chuyển</div>
                                <?php if(isset($order_meta['coupon'])): $coupon = unserialize($order_meta['coupon']); ?>
                                    <div class="mb-2">Mã giảm giá</div>
                                <?php endif; ?>
                                <?php if(isset($order_meta['v_point'])): $point = $order_meta['v_point']; ?>
                                    <div class="mb-2">V point</div>
                                <?php endif; ?>
                                <div>Tổng tiền thanh toán</div>
                            </div>
                            <div style="font-weight:600" class="col text-end">
                                <div class="mb-2"><?php echo number_format($order->total,0,"",".") ?>đ</div>
                                <div class="mb-2"><?php echo number_format($order->shipping_fee,0,"",".") ?>đ</div>
                                <?php if(isset($order_meta['coupon'])): ?>
                                    <div class="mb-2">-<?php echo number_format($coupon['coupon_amount'],0,"","."); ?>đ</div>
                                <?php endif; ?>
                                <?php if(isset($order_meta['v_point'])): ?>
                                    <div class="mb-2">-<?php echo number_format($point * 10000,0,"","."); ?>đ</div>
                                <?php endif; ?>
                                <div><?php echo number_format(($order->total + $order->shipping_fee - (isset($coupon) ? $coupon['coupon_amount'] : 0) - (isset($point) ? $point * 10000 : 0)) < 0 ? 0 : $order->total + $order->shipping_fee - (isset($coupon) ? $coupon['coupon_amount'] : 0) - (isset($point) ? $point * 10000 : 0),0,"",".") ?>đ</div>
                            </div>
                        </div>
                    </div>   
                </div> 
            </div>
        <?php endif; ?>                
    </div>
</section>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>