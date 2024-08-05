<?php if(!empty($data['coupons'])): ?>
<div class="user-title">Mã giảm giá</div>
<div class="voucher-items-list row row-cols-1 gy-4 row-cols-md-2">
  <?php foreach($data['coupons'] as $item): ?>
    <div class="voucher-item col">
      <div class="voucher-item-info">
        <div class="voucher-item-detail">
          <div class="voucher-item-title">Giảm <?php echo $item->amount / 1000 ?>k cho đơn từ <?php echo $item->minimum_spend / 1000 ?>k</div>
          <div class="voucher-item-code"><span><?php echo $item->code ?></span><div>Đã dùng: <?php echo $item->coupon_used ?>/<?php echo $item->coupon_usage ?> lượt</div></div>
          <div class="voucher-item-date">HSD: <?php echo $item->end_time; ?></div>
        </div>
        <div class="voucher-item-action">
          <div class="action"><span onclick="window.location.href='<?php echo url('cart') ?>'">Sử dụng</span></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>  
</div>
<?php else: ?>
  <div>Chưa có mã giảm giá nào!</div>
<?php endif; ?>
