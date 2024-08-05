<div class="user-title">Lịch sử đơn hàng</div>
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link <?php echo !isset($data['status']) ? "active" : "" ?>" href="<?php echo url("user/orders") ?>">Tất cả<?php echo $data['number_map']['all'] > 0 ? " ({$data['number_map']['all']})" : "" ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo isset($data['status']) && $data['status'] === "pending" ? "active" : "" ?>" href="?status=pending">Chờ xác nhận<?php echo $data['number_map']['pending'] > 0 ? " ({$data['number_map']['pending']})" : "" ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo isset($data['status']) && $data['status'] === "toship" ? "active" : "" ?>" href="?status=toship">Chờ lấy hàng<?php echo $data['number_map']['toship'] > 0 ? " ({$data['number_map']['toship']})" : "" ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo isset($data['status']) && $data['status'] === "shipping" ? "active" : "" ?>" href="?status=shipping">Đang vận chuyển<?php echo $data['number_map']['shipping'] > 0 ? " ({$data['number_map']['shipping']})" : "" ?></a>
  </li>
   <li class="nav-item">
    <a class="nav-link <?php echo isset($data['status']) && $data['status'] === "completed" ? "active" : "" ?>" href="?status=completed">Hoàn thành<?php echo $data['number_map']['completed'] > 0 ? " ({$data['number_map']['completed']})" : "" ?></a>
  </li>
   <li class="nav-item">
    <a class="nav-link <?php echo isset($data['status']) && $data['status'] === "cancelled" ? "active" : "" ?>" href="?status=cancelled">Đã hủy<?php echo $data['number_map']['cancelled'] > 0 ? " ({$data['number_map']['cancelled']})" : "" ?></a>
  </li>
</ul>
<?php if(count($data['orders']) > 0): ?>
<div class="table-responsive">
  <table id="user-orders" class="table table-borderless mt-3">
    <thead>
      <tr>
        <th scope="col">Mã đơn hàng</th>
        <th scope="col">Trạng thái</th>
        <th scope="col">Thời gian đặt hàng</th>
        <th scope="col">Hình thức thanh toán</th>
        <th scope="col">Trạng thái thanh toán</th>
        <th scope="col">Giá trị đơn hàng</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data['orders'] as $o): ?>
        <tr>
          <td>
            <a style="color:#800019" href="<?php echo url("user/order?id={$o->id}") ?>"><?php echo "#{$o->id}" ?></a>
          </td>
          <td><?php echo $data['status_map'][$o->status] ?></td>
          <td><?php echo $o->created_at ?></td>
          <td><?php echo $o->payment_method === "cod" ? "Thanh toán khi nhận hàng" : ($o->payment_method === "vnpay" ? "Thanh toán online qua VNPAY" : "") ?></td>
          <td>
            <span class="<?php echo strpos($o->paid_status,"Đã thanh toán") !== false ? "paid" : "not-paid" ?>"><?php echo strpos($o->paid_status,"Đã thanh toán") !== false ? "Đã thanh toán" : "Chưa thanh toán" ?></span>
          </td>
          <td><?php echo number_format($o->total,0,"","."); ?>đ</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php else: ?>
<div class="mt-3">Chưa có đơn hàng nào!</div>
<?php endif; ?>
<style>
  .nav-tabs{
    flex-wrap: nowrap;
    overflow:auto
  }
  .nav-tabs .nav-item a{
    white-space:nowrap;
  }
  #user-orders thead th,
  #user-orders tbody td{
    white-space:nowrap !important
  }
  span.paid:before {
    content: url(<?php echo url("client_assets/images/check_icon.png") ?>) !important;
    margin: 0px 7px 0px 0px !important;
    position: relative !important;
    top: 3px !important;
  }

  span.not-paid:before {
    content: url(<?php echo url("client_assets/images/icon_exclamation_point.png") ?>) !important;
    margin: 0px 7px 0px 0px !important;
    position: relative !important;
    top: 3px !important;
}

</style>


