<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
    <ul class="order-nav nav my-3">
        <li class="nav-item <?php echo current_route() === "admin/order" && !query("status") ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/order") ?>">Tất cả<?php echo $number_map['all'] > 0 ? " ({$number_map['all']})" : "" ?></a>
        </li>
        <li class="nav-item <?php echo current_route() === "admin/order" && query("status") === "pending" ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/order?status=pending") ?>">Chờ xác nhận<?php echo $number_map['pending'] > 0 ? " ({$number_map['pending']})" : "" ?></a>
        </li>
        <li class="nav-item <?php echo current_route() === "admin/order" && query("status") === "toship" ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/order?status=toship") ?>">Chờ lấy hàng<?php echo $number_map['toship'] > 0 ? " ({$number_map['toship']})" : "" ?></a>
        </li>
        <li class="nav-item <?php echo current_route() === "admin/order" && query("status") === "shipping" ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/order?status=shipping") ?>">Đang giao<?php echo $number_map['shipping'] > 0 ? " ({$number_map['shipping']})" : "" ?></a>
        </li>
        <li class="nav-item <?php echo current_route() === "admin/order" && query("status") === "completed" ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/order?status=completed") ?>">Hoàn thành<?php echo $number_map['completed'] > 0 ? " ({$number_map['completed']})" : "" ?></a>
        </li>
        <li class="nav-item <?php echo current_route() === "admin/order" && query("status") === "cancelled" ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/order?status=cancelled") ?>">Đơn hủy<?php echo $number_map['cancelled'] > 0 ? " ({$number_map['cancelled']})" : "" ?></a>
        </li>
    </ul>
    <div class="mb-3 d-flex justify-content-between align-items-center" style="font-size: 16px;">
        <div>Đơn hàng: <span style="font-weight:700"><?php echo $total_orders ?></span></div>
        <div style="width:35%">
            <form>
                <?php if(get_query("status")): ?>
                    <input type="hidden" name="status" value="<?php echo get_query("status"); ?>">
                <?php endif; ?>    
                <input value="<?php echo get_query("keyword") ?? "" ?>" name="keyword" type="text" class="form-control" placeholder="Tìm đơn hàng theo mã hoặc tên khách hàng">
            </form>
        </div>
    </div>
    <table id="product-table" class="table bg-white">
        <thead>
            <tr>
                <th style="width:18%" scope="col">Mã đơn hàng</th>
                <th scope="col">Khách hàng</th>
                <th scope="col">Tổng tiền</th>
                <th scope="col">Pttt</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
                <tr>
                    <td>
                        <?php echo $o->id; ?>
                        <div class="row-actions">
                            <span class="edit"><a href="<?php echo url("admin/order/{$o->id}") ?>">Chi tiết</a></span><?php if($o->status === "cancelled"): ?> | 
                            <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?php echo url("admin/order/delete/{$o->id}") ?>">Delete</a></span><?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo $o->user_id ? "<a href='".  url("admin/user/profile/{$o->user_id}") ."'>{$o->name}</a>" : "Khách vãng lai"; ?></td>
                    <td><?php echo number_format($o->total,0,"","."); ?>đ</td>
                    <td><?php echo $o->payment_method; ?></td>
                    <td><?php echo $o->created_at; ?></td>
                    <td><?php echo $status_map[$o->status]; ?></td>
                </tr>
            
            <?php endforeach; ?>    
        </tbody>
    </table>
    <?php echo pagination($totalPages,$currentPage); ?>
              
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>