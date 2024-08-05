<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>

        #container{
            width: 50%;
        }

        *{
            font-family: Arial, Helvetica, sans-serif;
            color: black;
        }

        #confirm{
            background-color: #f6f6f6;
            padding: 20px 80px;
            width: fit-content;
            margin: 20px auto;
            text-align: center;
        }
        @media screen and (max-width: 992px) {
           #container{
                width: 100% !important;
           }
        }

        
    </style>
</head>
<body>
    <div id="container" style="width:50%;margin:0 auto">
        <div id="logo" style="text-align:center">
            <!-- <img style="width: 80px;" src="<?php //echo url("client_assets/images/logo.png") ?>" alt=""> -->
            <img style="width: 80px;" src="https://vnhfashion.shop/client_assets/images/logo.png" alt="">
        </div>
        <div id="thank">
            <div style="text-align:center;margin-top:10px">Cám ơn <span style="font-weight: 600;"><?php echo $name ?></span> đã mua hàng tại website của chúng tôi</div>
        </div>
        <div id="confirm">
            <!-- <img src="<?php //echo url("client_assets/images/order_confirmed.png") ?>" alt=""> -->
            <img src="https://ci3.googleusercontent.com/meips/ADKq_NayVTu3_FLF4gEvYg24-nxk0T6DbqtThdJMygcB4sg-Qh5SjC5UbJVfO8IEX3QbbjSzx7i21P3AG6Ou5LBucgsiWn2emwubSru_EOW7CwwZsqxLTqfppd2i-hr6-z9v1XBsKvFEADVIjkt56kwPeH7a9Ozm6WQsv4iANTTvqJ356-4O5TWj2VvmvpVqP0qXAYFqpKA2QMDzXBaNVts=s0-d-e1-ft#https://www.acfc.com.vn/static/version1709711284/frontend/Acfc/mobile/vi_VN/Magento_Email/images/acfc-email-images/order-confirmed.png" alt="">
            <div style="margin:5px 0">VNH Hoàn thành đơn hàng</div>
            <div style="font-weight: 600;">#<?php echo $order_id; ?></div>
        </div>
        <div id="order">
            <div style="text-align:center;margin-bottom:10px">THÔNG TIN ĐƠN HÀNG</div>
            <table>
                <tbody>
                    <tr>
                        <td style="width:50%;vertical-align:top">
                            <div style="font-weight:600;margin-bottom:10px">Thông tin giao hàng</div>
                            <div style="font-size: 14px;color:black">
                                <div><?php echo $name ?> | <?php echo $phone ?></div>
                                <div>
                                    <?php echo "{$address['address']}, {$address['ward']}, {$address['district']}, {$address['city']}"; ?>                                 
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: top;">
                            <div style="font-weight:600;margin-bottom:10px">Phương thức thanh toán</div>
                            <div style="font-size: 14px;">
                                <?php echo $payment_method ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="text-align:center;margin:20px;clear:both">
                <a href="<?php echo url("order/track?order_id={$order_id}") ?>" style="background-color: #000;color:#f6f6f6;border:none;padding:7.5px 15px;text-decoration:none">Kiểm tra đơn hàng</a>
            </div>
        </div>
    </div>
</body>
</html>