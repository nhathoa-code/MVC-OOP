<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
    <?php
        $coupon_amount = 0;
        $point = 0;
    ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Mã đơn hàng: <?php echo $order->id ?>
        </div>
        <div>
            <?php echo $order->created_at ?>
        </div>
    </div>
    <div style="border-radius:3px" class="row bg-white mx-0 my-3 py-2" id="order-info">
        <div class="col">
            <div>
                <div class="mb-1">
                    <span>Họ tên:</span><span style="font-weight:700;margin-left:5px"><?php echo $order->name ?></span>
                </div>
                <div class="mb-1">
                    <span>Số điện thoại:</span><span style="font-weight:700;margin-left:5px"><?php echo $order->phone ?></span>
                </div>
                <div>
                    <span>Địa chỉ email:</span><span style="font-weight:700;margin-left:5px"><?php echo $order->email ?></span>
                </div>   
            </div>
        </div>
        <div class="col">
            <span style="font-weight:700">Địa chỉ giao hàng:</span>
            <div>
                <?php
                    $address = json_decode($order->address);
                    echo "{$address->address}, {$address->ward}, {$address->district}, {$address->city}"
                ?>
            </div>
        </div>
        <div class="col">
            <span style="font-weight:700">Trạng thái:</span>
            <?php
                $status = array("pending"=>"Chờ xác nhận","toship"=>"Chờ lấy hàng","shipping"=>"Đang vận chuyển","completed"=>"Hoàn thành","cancelled"=>"Đã hủy")
            ?>
            <select name="status" class="form-select form-control" aria-label="Default select example">
                <?php foreach($status as $key => $item): ?>
                    <option <?php echo $key === $order->status ? "selected" : "" ?> value="<?php echo $key ?>"><?php echo $item; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="d-flex align-items-center mt-2">
                <button onclick="orderUpdate(this)" class="btn btn-primary mr-2">Cập nhật đơn hàng</button>
                
            </div>
        </div>
    </div>
      <div style="border-radius:3px" class="row mx-0 my-3 py-2">
        <div class="col-3">
            <span style="font-weight:700">Phương thức thanh toán:</span>
            <div>
              <?php echo $order->payment_method === "cod" ? "Thanh toán khi nhận hàng" : ($order->payment_method === "vnpay" ? "Thanh toán online qua VNPAY" : "") ?>
            </div>
        </div>
        <div class="col-3">
            <span style="font-weight:700">Trạng thái thanh toán:</span>
            <div>
              <?php echo $order->paid_status ?>
            </div>
        </div>
    </div>
    <table id="cat-table" class="table table-borderless table-striped-columns">
        <thead>
            <tr>
                <th style="width: 50%;" scope="col">Sản phẩm</th>
                <th scope="col">Số lượng</th>
                <th scope="col">Giá</th>
                <th scope="col">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($order->items as $item): ?>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-3">
                                <img src="<?php echo $item->image ?>" alt="">
                            </div>
                            <div style="font-size: 14px;" class="col-9">
                                <span style="font-weight: 700;"><?php echo $item->p_name ?></span>
                                <?php if(isset($item->p_size)): ?>
                                <div>Kích cỡ: <?php echo $item->p_size ?></div>
                                <?php endif; ?>
                                <?php if(isset($item->p_color_id)): ?>
                                <div class="d-flex align-items-center">Màu sắc: <img class="color-image mx-1" src="<?php echo url($item->color_image) ?>" alt=""> <?php echo $item->color_name ?></div>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                    </td>
                    <td><?php echo $item->quantity ?></td>
                    <td><?php echo number_format($item->p_price,0,"",".") ?> VND</td>
                    <td><span style="font-weight: 700;"><?php echo number_format($item->quantity * $item->p_price,0,"",".") ?> VND</span></td>
                </tr>   
            <?php endforeach; ?>   
        </tbody>
    </table>
    <div class="row mb-3">
        <div class="col-2 offset-7">Tạm tính</div>
        <div class="col-2 text-right"><?php echo number_format($order->total,0,"","."); ?>đ</div>
    </div>
    <div class="row mb-3">
        <div class="col-2 offset-7">Phí vận chuyển</div>
        <div class="col-2 text-right"><?php echo number_format($order->shipping_fee,0,"",".") ?>đ</div>
    </div>
    <?php
     
        if(array_key_exists("coupon",$order->meta)):
        $coupon = unserialize($order->meta['coupon']);
        $coupon_amount = $coupon['coupon_amount'];
    ?>
    <div class="row mb-3">
        <div class="col-2 offset-7">Mã giảm giá <span style="font-size: 14px;">(<?php echo $coupon['coupon_code']; ?>)</span></div>
        <div class="col-2 text-right">-<?php echo number_format($coupon['coupon_amount'],0,"",".") ?>đ</div>
    </div>
    <?php endif; ?>
    <?php
        if(array_key_exists("v_point",$order->meta)):
        $point = $order->meta["v_point"];
    ?>
    <div class="row mb-3">
        <div class="col-2 offset-7">V point</span></div>
        <div class="col-2 text-right">-<?php echo number_format($point * 10000,0,"",".") ?>đ</div>
    </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-2 offset-7">Tổng tiền thanh toán</div>
        <div style="font-weight: 700;" class="col-2 text-right"><?php echo number_format(($order->total + $order->shipping_fee - $coupon_amount - $point * 10000) < 0 ? 0 : $order->total + $order->shipping_fee - $coupon_amount - $point * 10000,0,"","."); ?>đ</div>
    </div>                                    
    <script>
        const update_order_status_url = "<?php echo url("admin/order/status/{$order->id}"); ?>";
    </script>
     
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>