<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
<h1 class="heading">Thêm bảng kích cỡ</h1>
<div>
    <label>Tên bảng kích cỡ</label>
    <input type="text" value="<?php echo $size_chart->name; ?>" name="size_chart_name" placeholder="nhập tên bảng kích cỡ" class="form-control">
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
    
<script>
    const save_size_chart_url = "<?php echo url("admin/size-chart/update/$size_chart->id"); ?>";
    const chart_edit = true;
    var is_uploading = false;
    var is_size_active = true;
    var sizes = [];
    var measurements = [];
    sizes = JSON.parse(<?php echo json_encode($size_chart->chart,true) ?>);
    measurements = sizes[0].measurements.map((item)=> ({id:item.id,name:item.name}));
</script>
<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>