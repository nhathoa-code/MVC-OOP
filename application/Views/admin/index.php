<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
    <?php
        $status_map = array("pending"=>"Chờ xác nhận","toship"=>"Chờ lấy hàng","shipping"=>"Đang vận chuyển","completed"=>"Hoàn thành","cancelled"=>"Đã hủy","returned"=>"Trả hàng","failed_delivery"=>"Giao không thành công")
    ?>
    <div class="heading">Thống kê</div>
    <div class="row">
        <div class="col-6">
            <div class="statistic">
                <div class="header">Trạng thái đơn hàng</div>
                <div class="content d-flex justify-content-between align-items-center">
                    <ul class="statistic-list">
                        <?php foreach($orders as $status => $number): if($status === "all") continue; ?>
                        <a href="<?php echo url("admin/order?status={$status}") ?>"><li><?php echo "{$status_map[$status]} - $number" ?></li></a>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-center p-5 mr-5">
                        <div style="font-weight:700;font-size:20px"><?php echo $orders['all'] ?></div>
                        <div>Đơn hàng</div>
                    </div>
                </div>
            </div>
            <div class="statistic">
                <div class="header">Doanh thu</div>
                <div class="content">
                    <div><?php echo number_format($total_sales,0,"","."); ?>đ</div>
                </div>
            </div>
                <div class="statistic">
                <div class="header">Danh mục và sản phẩm</div>
                <div class="content">
                   <div class="row">
                        <div class="col">
                            <div><i style="color:#646970" class="fas fa-shirt mr-2"></i><a href="<?php echo url("admin/product") ?>"><?php echo "$total_products sản phẩm" ?></a></div>
                        </div>
                        <div class="col">
                            <div><i style="color:#646970" class="fas fa-folder mr-2"></i><a href="<?php echo url("admin/category") ?>"><?php echo "$total_categories danh mục" ?></a></div>
                        </div>
                   </div>
                </div>
            </div>
            <div class="statistic">
                <div class="header">Sản phẩm bán chạy</div>
                <div class="content" style="height: 314px;overflow-y:auto">
                  <ul class="statistic-list" style="font-size:14px;">
                    <?php foreach($top_10_saled_products as $item): ?>
                        <li>
                            <a href="<?php echo url("admin/product/edit/{$item->id}") ?>"><?php echo $item->p_name ?></a>
                            <?php if($item->color_name): ?>
                            <span> | <?php echo $item->color_name ?? "" ?></span>
                            <?php endif; ?>
                            <?php if($item->p_size): ?>
                            <span> - <?php echo $item->p_size ?? "" ?></span>
                            <?php endif; ?>
                            <div>Đã bán: <?php echo $item->total_saled_items; ?></div>
                        </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="statistic">
                <div class="header">Sản phẩm hết hàng</div>
                <div class="content"  style="height: 728px;overflow-y:auto">
                  <ul class="statistic-list" style="font-size:14px;">
                    <?php foreach($out_of_stock_products as $item): ?>
                        <?php if($item->ps_stock === null && $item->pcs_stock !== null): ?>
                            <li>
                                <a href="<?php echo url("admin/product/edit/{$item->id}") ?>"><?php echo $item->p_name ?></a>
                                <span> | <?php echo $item->color_name ?? "" ?></span>
                                <span> - <?php echo $item->pcs_size ?? "" ?></span>
                                <div>Tồn kho: <?php echo $item->pcs_stock; ?></div>
                            </li>
                        <?php elseif($item->ps_stock === null && $item->pcs_stock === null): ?>
                            <li>
                                <a href="<?php echo url("admin/product/edit/{$item->id}") ?>"><?php echo $item->p_name ?></a>
                                <span> | <?php echo $item->color_name ?? "" ?></span>
                                <div>Tồn kho: <?php echo $item->pcl_stock; ?></div>
                            </li> 
                        <?php elseif($item->pcs_stock === null && $item->pcl_stock === null): ?>
                            <li>
                                <a href="<?php echo url("admin/product/edit/{$item->id}") ?>"><?php echo $item->p_name ?></a>
                                <span> | <?php echo $item->ps_size ?? "" ?></span>
                                <div>Tồn kho: <?php echo $item->ps_stock; ?></div>
                            </li>           
                        <?php endif; ?>
                    <?php endforeach; ?>    
                  </ul>
                </div>
            </div>
        </div>
    </div>
    <div style="padding-bottom: 30px;" class="row mt-3">
        <div class="col-12">
            <div class="statistic w-100">
                <div class="header d-flex justify-content-between">
                    <ul id="statistical-options" class="list-group list-group-horizontal">
                        <li class="list-group-item active" data-option="date">Ngày</li>
                        <li class="list-group-item" data-option="week">Tuần</li>
                        <li class="list-group-item" data-option="month">Tháng</li>
                        <li class="list-group-item" data-option="year">Năm</li>
                    </ul>
                    <div id="picker">
                        <input type="text" class="form-control" id="date-picker">
                    </div>
                </div>
                <div class="content d-flex justify-content-between align-items-center pt-3">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        const orders_statistics = <?php echo json_encode($orders_statistics); ?>;
        const admin = true;
        const statistical_url = "<?php echo url("admin/statistical") ?>";
    </script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>