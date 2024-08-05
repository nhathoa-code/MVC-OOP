<div style="font-size: 14px;" class="mb-2">
    Hạng thành viên: <span class="user-rank"><?php echo $data['user_rank'] ?? "member" ?></span>
</div>
<div style="font-size: 14px;">
    Điểm tích lũy: <span class="user-point"><?php echo $data['user_point'] ?? 0 ?></span> điểm
</div>
<div style="font-size: 14px;">
    Tổng tiêu dùng: <span class="user-point"><?php echo number_format($data['user_total_spend'] ?? 0,0,"",".") ?>đ</span>
</div>
<div class="mt-3" id="tabs">
  <ul class="row">
    <li class="col m-0"><a class="w-100" href="#tab1">Member</a></li>
    <li class="col m-0"><a class="w-100" href="#tab2">Silver</a></li>
    <li class="col m-0"><a class="w-100" href="#tab3">Gold</a></li>
    <li class="col m-0"><a class="w-100" href="#tab4">Platinum</a></li>
    <li class="col m-0"><a class="w-100" href="#tab5">Diamond</a></li>
  </ul>
  <div id="tab1">
    <ul class="loyalty-policy-content">
        <li>Tổng giá trị chi tiêu tích lũy <strong>dưới </strong><strong>10 </strong><strong>triệu (đồng)</strong></li>
        <li>Tích điểm: <strong>200.000 VNĐ = 1 ĐIỂM</strong></li>
        <li>Sử dụng điểm: Trừ trực tiếp vào đơn hàng (<em>1 điểm = 10.000đ</em>)</li>
    </ul>
  </div>
  <div id="tab2">
    <ul class="loyalty-policy-content">
        <li>Tổng giá trị chi tiêu tích lũy <strong>từ</strong><strong> 10 </strong><strong>triệu</strong><strong> – </strong><strong>Dưới</strong><strong> 20 </strong><strong>triệu</strong><strong> (đồng)</strong></li>
        <li>Tích điểm: <strong>175.000 VNĐ = 1 ĐIỂM</strong></li>
        <li>Sử dụng điểm: Trừ trực tiếp vào đơn hàng (<em>1 điểm = 10.000đ</em>)</li>
    </ul>
  </div>
  <div id="tab3">
    <ul class="loyalty-policy-content">
        <li>Tổng giá trị chi tiêu tích lũy <strong>từ</strong><strong> 20 </strong><strong>triệu</strong><strong> – </strong><strong>Dưới</strong><strong> 35 </strong><strong>triệu</strong><strong> (đồng)</strong></li>
        <li>Tích điểm: <strong>150.000 VNĐ = 1 ĐIỂM</strong></li>
        <li>Sử dụng điểm: Trừ trực tiếp vào đơn hàng (<em>1 điểm = 10.000đ</em>)</li>
    </ul>
  </div>
  <div id="tab4">
    <ul class="loyalty-policy-content">
        <li>Tổng giá trị chi tiêu tích lũy <strong>từ</strong><strong> 35 </strong><strong>triệu</strong><strong> – </strong><strong>Dưới</strong><strong> 50 </strong><strong>triệu</strong><strong> (đồng)</strong></li>
        <li>Tích điểm: <strong>125.000 VNĐ = 1 ĐIỂM</strong></li>
        <li>Sử dụng điểm: Trừ trực tiếp vào đơn hàng (<em>1 điểm = 10.000đ</em>)</li>
    </ul>
  </div>
  <div id="tab5">
    <ul class="loyalty-policy-content">
        <li>Tổng giá trị chi tiêu tích lũy <strong>từ</strong><strong> 50 </strong><strong>triệu</strong><strong> (đồng)</strong></li>
        <li>Tích điểm: <strong>100.000 VNĐ = 1 ĐIỂM</strong></li>
        <li>Sử dụng điểm: Trừ trực tiếp vào đơn hàng (<em>1 điểm = 10.000đ</em>)</li>
    </ul>
  </div>
</div>

<div style="font-size: 14px;" class="mt-3">
    Lịch sử điểm
</div>
<table class="table point-history">
  <thead>
    <tr>
      <th>Ngày</th>
      <th>Điểm</th>
      <th class="text-end">Đơn hàng</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($data['point_history'] as $item): ?>
    <tr>
      <td><?php echo $item->created_at ?></td>
      <td class="<?php echo $item->action == 1 ? "plus-point" : "minus-point" ?>"><?php echo $item->action == 1 ? "+" : "-" ?> <?php echo $item->point ?></td>
      <td class="text-end">
        <a style="color:#8c002c" href="<?php echo url("user/order?id={$item->order_id}") ?>">#<?php echo $item->order_id ?></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>