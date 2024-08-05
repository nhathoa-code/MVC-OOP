<?php include(APP_PATH . "/application/Views/client/header.php") ?>
<section id="content">
    <div class="container-fluid" style="height: 100%;">
        <div id="success-content" class="main-content">
            <div class="css-125q30n">
                <img style="width: 100px;" src="<?php echo url("client/images/success.png"); ?>" alt="">
                <div class="thank-you">Đặt hàng thành công</div>
                <div class="order-info">Mã đơn đặt hàng: <br> <b>#<?php echo $order_id; ?></b></div>
                <div style="width:fit-content;margin:0 auto" class="border-top mt-3 pt-3">
                    <div style="font-weight: 600;">Cám ơn quý khách</div>
                    <div class="order-info note">
                        Chúng tôi sẽ gửi thông tin chi tiết đơn hàng về địa chỉ email của Quý Khách.
                    </div>
                </div>
                <div class="col-9 col-md-4 col-lg-3 mx-auto">
                    <div class="w-100">
                        <a href="<?php echo login() ? url("user/order?id={$order_id}") : url("order/track?order_id={$order_id}") ?>" class="btn btn-outline w-100" href="">Xem đơn hàng</a>
                    </div>
                    <div class="w-100">
                        <a href="<?php echo url("/"); ?>" class="btn btn-secondary w-100" href="" style="margin: 8px 0px">TIẾP TỤC MUA SẮM</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 
      
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>