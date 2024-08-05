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
<div class="heading">Tạo hóa đơn bán hàng</div>
<div class="row">
    <div class="col-12 row" style="position: relative;">
        <div class="col-6">
            <div id="left-column">
                <form method="GET">
                    <div class="mb-3 d-flex">
                        <select name="selected-store" id="product_select" class="form-control">
                            <option disabled selected value="">Chọn cửa hàng</option>
                            <?php foreach($stores as $store): ?>
                                <option data-store-name="<?php echo $store->name; ?>" value="<?php echo $store->id ?>"><?php echo $store->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="url" value="<?php echo url("admin/store/findproduct"); ?>">
                    </div>
                </form>
                <script>
                    $(document).ready(function() {
                        $('select#product_select').select2();
                    });
                    const base_url = "<?php echo url("/") ?>";
                </script>
                <p class="mb-2 bg-white rounded p-2">Thông tin khách hàng</p>
                <div class="row mb-2">
                    <div class="col-8">
                        <input type="text" id="customer-phone" placeholder="Số điện thoại" class="form-control">
                    </div>
                    <div class="col-4">
                        <button id="get-customer" class="btn btn-primary w-100">Nhập</button>
                    </div>
                </div>
                <div id="customer-info" class="mb-2">
                    <!-- <div>
                        Tên khách hàng: <span style="font-weight:600">Khách hàng</span>
                    </div> -->
                </div>
                <p class="mb-2 bg-white rounded p-2 ml-0">Chi tiết hóa đơn</p>
                <div class="table-responsive table-wrapper">
                    <table class="table cart table-borderless table-striped-columns">
                        <thead>
                            <tr>
                                <th style="width:40%" scope="col">Sản phẩm</th>
                                <th style="width:20%" scope="col">Giá</th>
                                <th style="width:20%" scope="col">Số lượng</th>
                                <th style="width:20%;text-align:right" scope="col">Tổng</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="right-column" class="col-6 bg-white pt-3 pb-0 rounded">
            <div class="store-container row row-cols-4 ml-0">
                Vui lòng chọn cửa hàng !
            </div>
        </div>
        <div class="variant-popup rounded p-2 rounded bg-white">
        </div>
    </div>
</div> 
<div id="overlay" class="overlay">
    <div class="spinner">
        <svg
            width="44"
            height="44"
            viewBox="0 0 24 24"
            >
                <g>
                    <rect width="1.5" height="5" x="11" y="1" fill="currentColor" opacity=".14" />
                    <rect
                    width="1.5"
                    height="5"
                    x="11"
                    y="1"
                    fill="currentColor"
                    opacity=".29"
                    transform="rotate(30 12 12)"
                    />
                    <rect
                    width="1.5"
                    height="5"
                    x="11"
                    y="1"
                    fill="currentColor"
                    opacity=".43"
                    transform="rotate(60 12 12)"
                    />
                    <rect
                    width="1.5"
                    height="5"
                    x="11"
                    y="1"
                    fill="currentColor"
                    opacity=".57"
                    transform="rotate(90 12 12)"
                    />
                    <rect
                    width="1.5"
                    height="5"
                    x="11"
                    y="1"
                    fill="currentColor"
                    opacity=".71"
                    transform="rotate(120 12 12)"
                    />
                    <rect
                    width="1.5"
                    height="5"
                    x="11"
                    y="1"
                    fill="currentColor"
                    opacity=".86"
                    transform="rotate(150 12 12)"
                    />
                    <rect
                    width="1.5"
                    height="5"
                    x="11"
                    y="1"
                    fill="currentColor"
                    transform="rotate(180 12 12)"
                    />
                    <animateTransform
                    attributeName="transform"
                    calcMode="discrete"
                    dur="0.75s"
                    repeatCount="indefinite"
                    type="rotate"
                    values="0 12 12;30 12 12;60 12 12;90 12 12;120 12 12;150 12 12;180 12 12;210 12 12;240 12 12;270 12 12;300 12 12;330 12 12;360 12 12"
                    />
                </g>
        </svg>
    </div>
</div>     
<script>
    const employee_id = <?php echo getUser("admin")->id; ?>;
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>