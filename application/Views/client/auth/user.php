<?php include(APP_PATH . "/application/Views/client/header.php") ?>
    <section id="content" class="user-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-3 border-end d-none d-lg-block">
                    <div id="user-name">Welcome, <?php echo getUser()->name; ?></div>
                    <ul id="user-options">
                        <li>
                            <a <?php echo $part === "member" ? "class='active'" : "" ?> href="<?php echo url("user/member") ?>">Thành viên</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "profile" ? "class='active'" : "" ?> href="<?php echo url("user/profile") ?>">Thông tin cá nhân</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "orders" || $part === "order" ? "class='active'" : "" ?> href="<?php echo url("user/orders") ?>">Lịch sử mua hàng</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "coupons" ? "class='active'" : "" ?> href="<?php echo url("user/coupons") ?>">Mã giảm giá</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "addresses" || $part === "address/add" || $part === "address/edit" ? "class='active'" : "" ?> href="<?php echo url("user/addresses") ?>">Sổ địa chỉ</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "wishlist" ? "class='active'" : "" ?> href="<?php echo url("user/wishlist") ?>">Danh sách yêu thích</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "change_password" ? "class='active'" : "" ?> href="<?php echo url("user/change_password") ?>">Đổi mật khẩu</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a style="color:#CC2A18" href="<?php echo url("auth/logout") ?>">Đăng xuất</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-lg-9 ps-lg-5">
                    <?php 
                        switch($part){
                            case "member":
                                include_once(VIEW_PATH . "/client/auth/parts/member.php");
                                break;
                            case "profile":
                                include_once(VIEW_PATH . "/client/auth/parts/profile.php");
                                break;
                            case "orders":
                                include_once(VIEW_PATH . "/client/auth/parts/orders_history.php");
                                break;
                            case "order":
                                include_once(VIEW_PATH . "/client/auth/parts/order.php");
                                break;
                            case "coupons":
                                include_once(VIEW_PATH . "/client/auth/parts/coupons.php");
                                break;
                            case "addresses":
                                include_once(VIEW_PATH . "/client/auth/parts/addresses.php");
                                break;
                            case "wishlist":
                                include_once(VIEW_PATH . "/client/auth/parts/wishlist.php");
                                break;
                            case "change_password":
                                include_once(VIEW_PATH . "/client/auth/parts/change_password.php");
                                break;
                            case "address/add":
                                include_once(VIEW_PATH . "/client/auth/parts/address_add.php");
                                break;
                            case "address/edit":
                                include_once(VIEW_PATH . "/client/auth/parts/address_edit.php");
                                break;
                        }
                    ?>
                </div>
                <div class="col-12 col-lg-3 border-end d-block d-lg-none mt-5">
                    <div id="user-name">Welcome, <?php echo getUser()->name; ?></div>
                    <ul id="user-options">
                        <li>
                            <a <?php echo $part === "member" ? "class='active'" : "" ?> href="<?php echo url("user/member") ?>">Thành viên</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "profile" ? "class='active'" : "" ?> href="<?php echo url("user/profile") ?>">Thông tin cá nhân</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "orders" || $part === "order" ? "class='active'" : "" ?> href="<?php echo url("user/orders") ?>">Lịch sử mua hàng</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "coupons" ? "class='active'" : "" ?> href="<?php echo url("user/coupons") ?>">Mã giảm giá</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "addresses" || $part === "address/add" || $part === "address/edit" ? "class='active'" : "" ?> href="<?php echo url("user/addresses") ?>">Sổ địa chỉ</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "wishlist" ? "class='active'" : "" ?> href="<?php echo url("user/wishlist") ?>">Danh sách yêu thích</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a <?php echo $part === "change_password" ? "class='active'" : "" ?> href="<?php echo url("user/change_password") ?>">Đổi mật khẩu</a>
                            <svg width="18" height="18" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </li>
                        <li>
                            <a style="color:#CC2A18" href="<?php echo url("auth/logout") ?>">Đăng xuất</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
        <!-- <div id="user-content" class="main-content mt-4">
          
        </div> -->
    <?php if($part === "profile" || $part === "address/add" || $part === "address/edit"): ?>
        <?php if($part === "address/edit"): ?>
        <script>
            const load_provinces = true;
        </script>
        <?php endif; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js" integrity="sha512-TjBzDQIDnc6pWyeM1bhMnDxtWH0QpOXMcVooglXrali/Tj7W569/wd4E8EDjk1CwOAOPSJon1VfcEt1BI4xIrA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="<?php echo url("client_assets/js/provinces.js") ?>"></script>
    <?php endif; ?>
    <?php if($part === "member"): ?>
        <script>
            $(document).ready(function () {
                $("#tabs").tabs();
            });
        </script>
    <?php endif; ?>
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>