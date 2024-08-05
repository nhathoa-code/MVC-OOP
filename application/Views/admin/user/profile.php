<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
    <div class="mb-2">
        Hạng thành viên: <span class="user-rank"><?php echo $user->meta['rank'] ?? "member" ?></span>
    </div>
    <div>
        Điểm tích lũy: <span class="user-point"><?php echo $user->meta['point'] ?? 0 ?></span> điểm
    </div>
    <div>
        Tổng tiêu dùng: <span class="user-point"><?php echo number_format($user->meta['total_spend'] ?? 0,0,"",".") ?></span>đ
    </div>
    <div class="profile mt-3">
        <div style="font-size: 24px;">Thống kê đơn hàng</div>
        <div>
            <div class="mb-2">Đơn hàng đã xác nhận: <span><?php echo $user->toship_orders ?></span></div>
            <div class="mb-2">Đơn hàng đang vận chuyển: <span><?php echo $user->shipping_orders ?></span></div>
            <div class="mb-2">Đơn hàng hoàn thành: <span><?php echo $user->completed_orders ?></span></div>
            <div class="mb-2">Đơn hàng đã hủy: <span><?php echo $user->cancelled_orders ?></span></div>
        </div>
    </div>
    <div class="profile mt-3">
        <div style="font-size: 24px;">Thông tin tài khoản</div>
        <div>
            <div class="mb-2">Họ tên: <span><?php echo $user->profile['name'] ?? ""; ?></span></div>
            <div class="mb-2">Ngày sinh: <span><?php echo $user->profile['birth_day'] ?? ""; ?></span></div>
            <div class="mb-2">Số điện thoại: <span><?php echo $user->profile['phone'] ?? ""; ?></span></div>
            <div class="mb-2">Email: <span><?php echo $user->profile['email'] ?? ""; ?></span></div>
            <div class="mb-2">Thành phố: <span><?php echo $user->profile['province'] ?? ""; ?></span></div>
            <div class="mb-2">Quận/huyện: <span><?php echo $user->profile['district'] ?? ""; ?></span></div>
            <div class="mb-2">Phường/xã: <span><?php echo $user->profile['ward'] ?? ""; ?></span></div>
        </div>
    </div>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>