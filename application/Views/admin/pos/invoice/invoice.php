<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<style>
    #invoice{
        width: 40%;
        margin: 0 auto;
    }
    #header{
        text-align:center;
    }
    #logo{
        text-align: center;
    }
    #logo img{
        width:100px;
        margin-bottom:10px;
    }
    table{
        width:100%;
    }
    #footer{
        text-align: center;
    }
    table {
        border-collapse:separate; 
        border-spacing: 0 1em;
    }
    #header{
        border-bottom: 1px dashed black;
        padding-bottom: 10px;
    }
    #invoice-number{
        border-bottom: 1px dashed black;
        padding: 10px 0;
    }
    #customer{
        padding: 10px 0;
        border-bottom: 1px dashed black;
    }
    #footer{
        border-top: 1px dashed black;
        padding-top: 20px;
    }   
</style>
<div class="heading" style="margin-bottom: 0;">Chi tiết hóa đơn | <?php echo $sale->id; ?><span style="float: right;"><a target="_blank" style="font-size: 20px;" href="<?php echo url("admin/pos/invoice/{$sale->id}/print") ?>">In hóa đơn</a></span></div>
<div class="row">
    <div class="col-12">
        <div id="invoice">
            <div id="logo">
                <img style="width:100px" src="<?php echo url("client_assets/images/logo.png"); ?>" alt="">
            </div>
            <div id="header">
                <div>
                    Tel:<Tel:tel>0943166208</Tel:tel>
                </div>
                <div>
                    nhathoa512@gmail.com
                </div>
                <div>
                    https://vnhfashion.shop
                </div>
            </div>
            <div id="invoice-number">
                <div>
                    Invoice Number: <?php echo $sale->id; ?>
                </div>
                <div>
                    Date: <?php echo $sale->created_at; ?>
                </div>
                <div>
                    Cashier: <?php echo $sale->employee; ?>
                </div>
            </div>
            <div id="customer">
                <div>
                    Customer Name: <?php echo $sale->customer_name; ?>
                </div>
                <div>
                    Customer Phone: <?php echo $sale->customer_phone; ?>
                </div>
            </div>
            <div id="invoice-content">
                <table>
                    <thead>
                        <tr>
                            <th style="width:70%;text-align:left">Sản phẩm</th>
                            <th style="width:30%;text-align:right">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $subtotal = 0;
                        ?>
                        <?php foreach($sale->items as $item): ?>
                            <?php 
                                $subtotal += $item->quantity * $item->price; 
                                $variant = null;
                                if($item->color_id){
                                    $variant = $item->color_name;
                                    if($item->size){
                                        $variant .= " - " . $item->size;
                                    }
                                }else if($item->size){
                                    $variant = $item->size;
                                }
                            ?>
                            <tr>
                                <td>
                                    <div>
                                        <?php echo $item->product_name; ?>
                                    </div> 
                                    <?php if($variant): ?>
                                    <div>
                                        <?php echo $variant; ?>
                                    </div>   
                                    <?php endif; ?>
                                    <div>
                                        <?php echo $item->quantity ?> x <?php echo number_format($item->price,0,"",".") ?>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <?php echo number_format($item->quantity * $item->price,0,"","."); ?> 
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <table style="border-top: 1px dashed black;">
                    <thead>
                        <tr>
                            <td style="width:70%;text-align:left">Tổng thanh toán</td>
                            <td style="width:30%;text-align:right"><?php echo number_format($subtotal,0,"",".") ?></td>
                        </tr>
                    </thead>
                </table>
            </div>
            <div id="footer">
                <div>
                    CÁM ƠN BẠN ĐÃ MUA HÀNG CỦA CHÚNG TÔI
                </div>
            </div>
        </div>
    </div>
</div>       
<script>
    const base_url = "<?php echo url("/") ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>