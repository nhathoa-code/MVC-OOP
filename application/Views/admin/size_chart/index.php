<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<?php
    if(isset($message["success"])) :
?>
<script>
    notif({
        msg:"<?php echo $message['success']; ?>",
        type:"success",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 10000,
        animation:'slide'
    });
</script>
<?php endif; ?> 
<h1 class="heading">Danh sách bảng kích cỡ</h1>
<div class="row">
      <div class="col-12">
        <form class="mb-3">
            <input type="text" name="keyword" value="<?php echo get_query("keyword") ?? "" ?>" class="form-control" placeholder="Nhập tên bảng kích cỡ">
        </form> 
        <table class="table table-borderless position-relative">
            <thead>
                <tr>
                    <th style="width:25%" scope="col">Tên bảng</th>
                    <th scope="col">Nội dung</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    foreach($size_charts as $size_chart): 
                ?>
                    <tr id="<?php echo "size-chart-{$size_chart->id}" ?>">
                        <td>
                            <?php echo $size_chart->name ?>
                            <div class="row-actions">
                                <span data-route="<?php echo url("admin/size-chart/delete/{$size_chart->id}") ?>" class="delete"><a href="javascript:void(0)" onclick="deleteSizeChart(<?php echo $size_chart->id ?>)">Xóa</a><form id="delete-size-chart-<?php echo $size_chart->id ?>" style="display:none" action="<?php echo url("admin/size-chart/delete/$size_chart->id") ?>" method="POST"><input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>"></form></span>  | 
                                <span class="edit"><a href="<?php echo url("admin/size-chart/edit/{$size_chart->id}") ?>">Sửa</a></span>
                            </div>
                        </td>
                        <td>
                            <?php
                                $size_chart->drawSizeChart();
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo pagination($totalPages,$currentPage); ?>
    </div>
</div>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>