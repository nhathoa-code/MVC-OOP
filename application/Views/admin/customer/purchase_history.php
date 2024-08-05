<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<?php
    if(isset($message)) :
?>
<script>
    notif({
        msg:"<?php echo isset($message['success']) ? $message["success"] : $message["error"] ?>",
        type:"<?php echo isset($message['success']) ? "success" : "warning" ?>",
        position:"center",
        height :"auto",
        top: 80,
        timeout: 5000,
        animation:'slide'
    });
</script>
<?php endif; ?>  
<div class="heading">Lịch sử mua hàng | <?php echo "{$customer->name} - {$customer->phone}"; ?></div>
<div class="row">
    <div class="row col-12 pr-0">
        <div class="col-7">
            <form method="GET" class="mb-3">
                <input type="text" name="keyword" value="<?php echo get_query("keyword") ?? "" ?>" class="form-control" placeholder="Nhập mã hóa đơn">
            </form>  
        </div>
        <div class="col-5 text-right pr-0">
            <a href="<?php echo url("admin/pos/invoice/export/excel" . "?" . get_query_string(current_url())); ?>" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <svg viewBox="0 0 48 48" width="28px" height="28px"><path fill="#169154" d="M29,6H15.744C14.781,6,14,6.781,14,7.744v7.259h15V6z"/><path fill="#18482a" d="M14,33.054v7.202C14,41.219,14.781,42,15.743,42H29v-8.946H14z"/><path fill="#0c8045" d="M14 15.003H29V24.005000000000003H14z"/><path fill="#17472a" d="M14 24.005H29V33.055H14z"/><g><path fill="#29c27f" d="M42.256,6H29v9.003h15V7.744C44,6.781,43.219,6,42.256,6z"/><path fill="#27663f" d="M29,33.054V42h13.257C43.219,42,44,41.219,44,40.257v-7.202H29z"/><path fill="#19ac65" d="M29 15.003H44V24.005000000000003H29z"/><path fill="#129652" d="M29 24.005H44V33.055H29z"/></g><path fill="#0c7238" d="M22.319,34H5.681C4.753,34,4,33.247,4,32.319V15.681C4,14.753,4.753,14,5.681,14h16.638 C23.247,14,24,14.753,24,15.681v16.638C24,33.247,23.247,34,22.319,34z"/><path fill="#fff" d="M9.807 19L12.193 19 14.129 22.754 16.175 19 18.404 19 15.333 24 18.474 29 16.123 29 14.013 25.07 11.912 29 9.526 29 12.719 23.982z"/></svg>
                </span>
                <span class="text">Download excel</span>
            </a>
        </div>
    </div>
    <div class="col-12">
        <table id="cat-table" class="table table-borderless position-relative">
            <thead>
                <tr>
                    <th style="width:15%" scope="col">Mã hóa đơn</th>
                    <th style="width:20%" scope="col">Ngày tạo</th>
                    <th style="width:15%" scope="col">Tổng thanh toán</th>
                    <th style="width:20%" scope="col">Cửa hàng</th>
                    <th style="width:10%" scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($invoices as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item->id; ?>
                            <div class="row-actions">
                                <span class="delete"><a href="javascript:void(0)" onclick="deleteInvoice(<?php echo $item->id ?>)">Xóa</a><form id="delete-invoice-<?php echo $item->id ?>" style="display:none" method="POST" action="<?php echo url("admin/pos/invoice/{$item->id}/delete") ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>"></form></span>  | 
                                <span class="edit"><a target="_blank" href="<?php echo url("admin/pos/invoice/{$item->id}/print") ?>" >In hóa đơn</a></span>
                            </div>
                        </td>
                        <td>
                           <?php echo $item->created_at; ?>
                        </td>
                        <td>
                           <?php echo number_format($item->total_amount,0,"","."); ?>đ
                        </td>
                        <td>
                           <?php echo $item->store; ?>
                        </td>
                        <td>
                           <a href="<?php echo url("admin/pos/invoice/{$item->id}"); ?>">Chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>       
<?php echo pagination($totalPages,$currentPage); ?>    
<script>
    const base_url = "<?php echo url("/") ?>";
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>