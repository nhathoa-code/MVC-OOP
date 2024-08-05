<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<h1 class="heading">Thêm bảng kích cỡ</h1>
<div>
    <label>Tên bảng kích cỡ</label>
    <input type="text" name="size_chart_name" placeholder="nhập tên bảng kích cỡ" class="form-control">
</div>

<div id="add-product-form">
    <div id="size-variation">
        <h1 style="font-size: 1rem;">Phân loại kích cỡ</h1>
        <div id="size-variation-inputs" class="row align-items-center ui-sortable">
            <div class="col-4 mb-3 ui-sortable-handle">
                <button id="add-size-input" style="font-size:0.9rem" type="button" class="btn btn-outline">Add</button>
            </div>
        </div>
    </div>
    <div id="measurement-variation">
        <h1 style="font-size: 1rem;">Phân loại đặc tính</h1>
        <div id="measurement-variation-inputs" class="row align-items-center ui-sortable">
            <div class="col-4 mb-3 ui-sortable-handle">
                <button id="add-measurement-input" style="font-size:0.9rem" type="button" class="btn btn-outline cursor-disabled">Add</button>
            </div>
        </div>
    </div>
    <div style="overflow-x: auto;" id="variation-table" class="row w-100 mr-0 ml-0">
        <div id="size-chart">
        </div>              
    </div>
    <div class="d-flex justify-content-end">
        <button id="save-chart" class="btn btn-primary py-2 col-3">Lưu</button>
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
    const save_size_chart_url = "<?php echo url("admin/size-chart/add"); ?>";
    var is_uploading = false;
    var is_size_active = false;
    var sizes = [];
    var measurements = [];
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>