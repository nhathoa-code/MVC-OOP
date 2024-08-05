<ul style="position:sticky;top:0;height:100vh" class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <div id="sidebar" style="height:100%;overflow-y:auto">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo url("/") ?>">
            <div class="mx-3">VNH</div>
        </a>

        <li class="nav-item <?php echo current_route() === "admin" ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin") ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Thống kê</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"category") !== false ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/category"); ?>">
                <i class="fas fa-folder"></i>
                <span>Danh mục</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"admin/product") !== false ? "active" : "" ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProducts"
                aria-expanded="true" aria-controls="collapseProducts">
                <i class="fas fa-shirt"></i>
                <span>Sản phẩm</span>
            </a>
            <div id="collapseProducts" class="collapse <?php echo strpos(current_route(),"admin/product") !== false ? "show" : "" ?>" aria-labelledby="headingUtilities"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo strpos(current_route(),"admin/product") !== false && current_route() !== "admin/product/add" ? "active" : "" ?>" href="<?php echo url("admin/product") ?>">Danh sách sản phẩm</a>
                    <a class="collapse-item <?php echo current_route() === "admin/product/add" ? "active" : "" ?>" href="<?php echo url("admin/product/add") ?>">Thêm sản phẩm</a>
                </div>
            </div>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"coupon") !== false ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/coupon"); ?>">
                <i class="fas fa-ticket"></i>
                <span>Mã giảm giá</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"order") !== false ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/order"); ?>">
                <i class="fas fa-receipt"></i>
                <span>Đơn hàng</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"user") !== false ? "active" : "" ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers"
                aria-expanded="true" aria-controls="collapseUsers">
                <i class="fas fa-solid fa-user"></i>
                <span>Người dùng</span>
            </a>
            <div id="collapseUsers" class="collapse <?php echo strpos(current_route(),"user") !== false ? "show" : "" ?>" aria-labelledby="headingUtilities"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo current_route() === "admin/user" ? "active" : "" ?>" href="<?php echo url("admin/user") ?>">Danh sách người dùng</a>
                    <a class="collapse-item <?php echo current_route() === "admin/user/add" ? "active" : "" ?>" href="<?php echo url("admin/user/add") ?>">Thêm người dùng</a>
                </div>
            </div>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"store") !== false ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/store"); ?>">
                <i class="fas fa-folder"></i>
                <span>Cửa hàng</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"location") !== false ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/location/province"); ?>">
                <i class="fas fa-folder"></i>
                <span>Tỉnh/thành</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"size-chart") !== false ? "active" : "" ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSizeCharts"
                aria-expanded="true" aria-controls="collapseSizeCharts">
                <i class="fa-solid fa-chart-simple"></i>
                <span>Bảng kích cỡ</span>
            </a>
            <div id="collapseSizeCharts" class="collapse <?php echo strpos(current_route(),"size-chart") !== false ? "show" : "" ?>" aria-labelledby="headingUtilities"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo current_route() === "admin/size-chart" ? "active" : "" ?>" href="<?php echo url("admin/size-chart") ?>">Danh sách bảng kích cỡ</a>
                    <a class="collapse-item <?php echo current_route() === "admin/size-chart/add" ? "active" : "" ?>" href="<?php echo url("admin/size-chart/add") ?>">Thêm bảng kích cỡ</a>
                </div>
            </div>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"pos/sale") !== false ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/pos/sale"); ?>">
                <i class="fas fa-folder"></i>
                <span>POS</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"invoice") !== false ? "active" : "" ?>">
            <a class="nav-link" href="<?php echo url("admin/pos/invoice"); ?>">
                <i class="fas fa-folder"></i>
                <span>Hóa đơn</span>
            </a>
        </li>

        <li class="nav-item <?php echo strpos(current_route(),"customers") !== false ? "active" : "" ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomers"
                aria-expanded="true" aria-controls="collapseSizeCharts">
                <i class="fas fa-solid fa-user"></i>
                <span>Khách hàng</span>
            </a>
            <div id="collapseCustomers" class="collapse <?php echo strpos(current_route(),"customer") !== false ? "show" : "" ?>" aria-labelledby="headingUtilities"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo current_route() === "admin/customer" ? "active" : "" ?>" href="<?php echo url("admin/customer") ?>">Danh sách</a>
                    <a class="collapse-item <?php echo current_route() === "admin/customer/add" ? "active" : "" ?>" href="<?php echo url("admin/customer/add") ?>">Thêm khách hàng</a>
                </div>
            </div>
        </li>

        <li class="nav-item <?php echo (strpos(current_route(),"admin/role") !== false || strpos(current_route(),"admin/permission") !== false) ? "active" : "" ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRoles"
                aria-expanded="true" aria-controls="collapseSizeCharts">
                <i class="fa-solid fa-chart-simple"></i>
                <span>Phân quyền</span>
            </a>
            <div id="collapseRoles" class="collapse <?php echo strpos(current_route(),"role") !== false || strpos(current_route(),"permission") !== false ? "show" : "" ?>" aria-labelledby="headingUtilities"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo strpos(current_route(),"admin/role/list") !== false ? "active" : "" ?>" href="<?php echo url("admin/role/list") ?>">Danh sách vai trò</a>
                    <a class="collapse-item <?php echo strpos(current_route(),"admin/role/add") !== false ? "active" : "" ?>" href="<?php echo url("admin/role/add") ?>">Thêm vai trò</a>
                    <a class="collapse-item <?php echo strpos(current_route(),"admin/permission") !== false ? "active" : "" ?>" href="<?php echo url("admin/permission-group") ?>">Quyền</a>
                </div>
            </div>
        </li>
    </div>
</ul>