<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhật Hòa</title>
    <link rel="icon" href="<?php echo url("client_assets/images/v.svg") ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo url("client_assets/css/bootstrap.min.css"); ?>">
    <script src="<?php echo url("client_assets/js/bootstrap.bundle.min.js") ?>"></script>
    <link rel="stylesheet" href="<?php echo url("client_assets/css/style.css"); ?>">
    <script src="<?php echo url("client_assets/js/jquery-3.7.1.min.js") ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js"></script>
    <script>
        const csrf_token = "<?php echo csrf_token() ?>";
    </script>
    <?php if(current_route() === "checkout"): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js"></script>
        <script src="<?php echo url("client_assets/js/notifIt.js") ?>"></script>
        <link rel="stylesheet" href="<?php echo url("client_assets/css/notifIt.css"); ?>">    
    <?php endif; ?>
    <?php if(current_route() === "user/member"): ?>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>  
    <?php endif; ?>
    <?php if(strpos(current_route(),"product/detail") !== false): ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo url("client_assets/css/photoswipe.css") ?>"/>
    <?php endif; ?>
    <?php if(strpos(current_route(),"collection") !== false): ?>
        <style>
            .price-check label::before {
                background-image: url(<?php echo url("client_assets/images/multi-select.svg") ?>);
            }
            @media screen and (max-width: 992px){
                #header {
                    box-shadow: none;
                }
            }
        </style>
    <?php endif; ?>
</head>
<body>
    <nav id="header" class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="<?php echo url("/") ?>">
                <img style="width: 80px;" src="<?php echo url("client_assets/images/logo.png")  ?>" alt="">
            </a>
            <div class="offcanvas offcanvas-start" id="navbarSupportedContent">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
                        <a class="navbar-brand" href="<?php echo url("/") ?>">
                            <img style="width: 100px;" src="<?php echo url("client_assets/images/logo.png")  ?>" alt="">
                        </a>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">    
                    <ul id="header-menu" class="navbar-nav me-auto mb-2 mb-lg-0 ps-lg-5">
                        <?php 
                            use NhatHoa\App\Models\Category;
                            $categories = Category::all(whereNull:array("parent_id"));
                            foreach($categories as $cat):
                        ?>
                            <li class="main-menu-item nav-item">
                                <a class="nav-link" href="<?php echo url("collection/{$cat->cat_slug}") ?>"><?php echo $cat->cat_name ?></a>
                                <div class="submenu">
                                    <ul class="submenu-content d-flex">
                                        <?php foreach($cat->getChildren() as $cat_1): ?>
                                        <li>
                                            <a href="<?php echo url("collection/{$cat->cat_slug}/{$cat_1->cat_slug}") ?>"><?php echo $cat_1->cat_name ?></a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end">
                <div id="header-actions">
                    <div class="top-search" style="padding-top:3px">
                        <svg onclick="open_search()" style="cursor:pointer" width="24" height="25" viewBox="0 0 24 25" fill="none">
                        <path d="M11.1111 17.7222C15.0385 17.7222 18.2222 14.5385 18.2222 10.6111C18.2222 6.68375 15.0385 3.5 11.1111 3.5C7.18375 3.5 4 6.68375 4 10.6111C4 14.5385 7.18375 17.7222 11.1111 17.7222Z" stroke="#25282B" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M20.0002 19.5L16.1335 15.6334" stroke="#25282B" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div style="padding-bottom:3px">
                        <a href="<?php echo url("order/track") ?>">
                            <svg width="22" height="22" viewBox="0 0 25 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.86066 22.17C10.5207 22.54 11.3907 22.75 12.3007 22.75C13.2107 22.75 14.0807 22.54 14.7407 22.16C15.1007 21.96 15.2307 21.5 15.0307 21.14C14.8307 20.78 14.3707 20.65 14.0107 20.85C13.7482 20.9978 13.4139 21.101 13.0508 21.1596V12.991L21.0358 8.36572C21.1161 8.63829 21.1606 8.90804 21.1606 9.15997V12.82C21.1606 13.23 21.5006 13.57 21.9106 13.57C22.3206 13.57 22.6606 13.23 22.6606 12.82V9.15997C22.6606 7.49997 21.5206 5.57001 20.0706 4.77001L14.7307 1.80999C13.3607 1.04999 11.2207 1.04999 9.86066 1.80999L4.52066 4.77001C3.07066 5.58001 1.93066 7.49997 1.93066 9.15997V14.82C1.93066 16.48 3.07066 18.41 4.52066 19.21L9.86066 22.17ZM20.3561 7.01894L12.3006 11.6796L4.25189 7.02183C4.54363 6.62759 4.89279 6.29481 5.26065 6.09002L10.6006 3.13C11.5106 2.62 13.1007 2.62 14.0107 3.13L19.3506 6.09002C19.7152 6.29127 20.0638 6.62372 20.3561 7.01894ZM3.57364 8.36918L11.5508 12.9856V21.157C11.1926 21.098 10.8622 20.9956 10.6006 20.85L5.26065 17.89C4.30065 17.36 3.45065 15.92 3.45065 14.82V9.15997C3.45065 8.90874 3.49447 8.6403 3.57364 8.36918ZM19.5008 22.1498C17.3208 22.1498 15.5508 20.3798 15.5508 18.1998C15.5508 16.0198 17.3208 14.2498 19.5008 14.2498C21.6808 14.2498 23.4508 16.0198 23.4508 18.1998C23.4508 19.0209 23.1997 19.7838 22.7701 20.4159C22.7911 20.4327 22.8113 20.4508 22.8307 20.4702L23.8307 21.4702C24.1207 21.7602 24.1207 22.2402 23.8307 22.5302C23.6807 22.6802 23.4907 22.7502 23.3007 22.7502C23.1107 22.7502 22.9207 22.6802 22.7707 22.5302L21.7707 21.5302C21.7513 21.5108 21.7332 21.4905 21.7164 21.4695C21.0844 21.8988 20.3216 22.1498 19.5008 22.1498ZM19.5008 15.7498C18.1508 15.7498 17.0508 16.8498 17.0508 18.1998C17.0508 19.5498 18.1508 20.6498 19.5008 20.6498C20.8508 20.6498 21.9508 19.5498 21.9508 18.1998C21.9508 16.8498 20.8508 15.7498 19.5008 15.7498Z" fill="#292D32"></path></svg>
                        </a>
                    </div>
                    <div id="search-001" class="search-container">
                        <div class="search-wapper">
                            <div class="cus-cotent-001">
                            <div class="search-header">
                                <button onclick="close_search()" class="button-close-search">Đóng</button>
                            </div>
                            <div class="search-main">
                                <div class="search-section flex flex-nowrap justify-center">
                                <form
                                    action="<?php echo url("search") ?>"
                                    class="minisearch"
                                >
                                    <div class="search-form">
                                    <div class="field">
                                        <div class="field-control">
                                        <input
                                            placeholder="Bạn muốn tìm gì hôm nay ?"
                                            maxlength="128"
                                            name="keyword"
                                            class="keyword"
                                        />
                                        </div>
                                        <div class="action">
                                        <button <?php echo isMobileDevice() ? "style='margin-right:10px'" : "" ?> type="submit" class="action-search">
                                            <svg
                                            width="24"
                                            height="25"
                                            viewBox="0 0 24 25"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                            >
                                            <path
                                                d="M17 17.5L21 21.5M3 11.5C3 13.6217 3.84285 15.6566 5.34315 17.1569C6.84344 18.6571 8.87827 19.5 11 19.5C13.1217 19.5 15.1566 18.6571 16.6569 17.1569C18.1571 15.6566 19 13.6217 19 11.5C19 9.37827 18.1571 7.34344 16.6569 5.84315C15.1566 4.34285 13.1217 3.5 11 3.5C8.87827 3.5 6.84344 4.34285 5.34315 5.84315C3.84285 7.34344 3 9.37827 3 11.5Z"
                                                stroke="#25282B"
                                                stroke-width="1.5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            ></path>
                                            </svg>
                                        </button>
                                        </div>
                                    </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div id="wishlist-header"> 
                        <?php
                            $wl_count = 0;
                            use NhatHoa\Framework\Facades\Auth;
                            if(Auth::check()){
                                $wl_count = Auth::user()->countWishList();
                            }
                        ?>
                        <a href="<?php echo url("user/wishlist"); ?>" aria-label="0">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.5332 6.93144L10.7688 6.14476C8.97322 4.29941 5.68169 4.93586 4.49325 7.25588C3.93592 8.34745 3.8097 9.92258 4.82836 11.9341C5.80969 13.8702 7.85056 16.1884 11.5332 18.7147C15.2158 16.1884 17.2558 13.8702 18.2389 11.9341C19.2567 9.92169 19.1323 8.34745 18.5732 7.25677C17.3847 4.93675 14.0932 4.29852 12.2976 6.14387L11.5332 6.93144ZM11.5332 20C-4.82224 9.19279 6.49768 0.757159 11.3465 5.21942C11.4096 5.27809 11.4728 5.33853 11.5332 5.40165C11.5936 5.33853 11.6559 5.2772 11.7208 5.22031C16.5687 0.755381 27.8886 9.19101 11.5341 20H11.5332Z" fill="#25282B"></path>
                            </svg><?php if(login()): ?><span data-number="<?php echo $wl_count; ?>" class="number"><?php echo $wl_count; ?></span><?php endif; ?>
                        </a>
                    </div>
                    <div id="user-header" class="position-relative">
                        <div class="open-mini-login"> 
                            <svg style="cursor:pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.9998 12C13.0606 12 14.078 11.5786 14.8282 10.8284C15.5783 10.0783 15.9998 9.06086 15.9998 8C15.9998 6.93913 15.5783 5.92172 14.8282 5.17157C14.078 4.42143 13.0606 4 11.9998 4C10.9389 4 9.92147 4.42143 9.17133 5.17157C8.42118 5.92172 7.99975 6.93913 7.99975 8C7.99975 9.06086 8.42118 10.0783 9.17133 10.8284C9.92147 11.5786 10.9389 12 11.9998 12ZM14.6664 8C14.6664 8.35026 14.5974 8.6971 14.4634 9.0207C14.3294 9.3443 14.1329 9.63834 13.8852 9.88601C13.6375 10.1337 13.3435 10.3302 13.0199 10.4642C12.6963 10.5982 12.3495 10.6672 11.9992 10.6672C11.6489 10.6672 11.3021 10.5982 10.9785 10.4642C10.6549 10.3302 10.3609 10.1337 10.1132 9.88601C9.86551 9.63834 9.66905 9.3443 9.53501 9.0207C9.40097 8.6971 9.33198 8.35026 9.33198 8C9.33198 7.29276 9.61293 6.61448 10.113 6.11438C10.6131 5.61428 11.2914 5.33333 11.9986 5.33333C12.7059 5.33333 13.3842 5.61428 13.8843 6.11438C14.3844 6.61448 14.6664 7.29276 14.6664 8ZM19.9998 18.6667C19.9998 20 18.6664 20 18.6664 20H5.33309C5.33309 20 3.99976 20 3.99976 18.6667C3.99976 17.3333 5.33309 13.3333 11.9998 13.3333C18.6664 13.3333 19.9998 17.3333 19.9998 18.6667ZM18.6664 18.6611C18.6653 18.3333 18.4609 17.3467 17.5564 16.4422C16.6886 15.5733 15.052 14.6667 11.9998 14.6667C8.94642 14.6667 7.31087 15.5733 6.44198 16.4422C5.53864 17.3467 5.33531 18.3333 5.33309 18.6611H18.6664Z" fill="#25282B"></path>
                            </svg>
                            <ul id="mini-user-header" class="position-absolute bg-white">
                                <?php if(login()): ?>
                                    <li>
                                        <a href="<?php echo url("user/member") ?>">Thông tin thành viên</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo url("user/orders") ?>">Lịch sử mua hàng</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo url("auth/logout") ?>">Đăng xuất</a>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a href="<?php echo url("auth/login"); ?>">Đăng nhập</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo url("auth/registry"); ?>">Đăng ký</a>
                                    </li>
                                <?php endif; ?>   
                            </ul>
                        </div>
                    </div>
                    <div id="cart-header" class="position-relative">
                        <a href="<?php echo url("cart"); ?>" aria-label="0">
                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.47803 9.03125V8.25492C8.47803 6.45418 9.95882 4.68544 11.7996 4.51737C13.9921 4.30929 15.8411 5.99799 15.8411 8.10286V9.20732" stroke="#25282B" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M9.70483 20.5H14.6135C17.9024 20.5 18.4914 19.2115 18.6632 17.6428L19.2768 12.8408C19.4977 10.888 18.925 9.29535 15.4316 9.29535H8.88671C5.39335 9.29535 4.82067 10.888 5.04156 12.8408L5.65515 17.6428C5.82696 19.2115 6.416 20.5 9.70483 20.5Z" stroke="#25282B" stroke-width="1.25" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M15.0188 12.4967H15.0262" stroke="#25282B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M9.29128 12.4967H9.29863" stroke="#25282B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg><span class="number">
                                <?php
                                    use NhatHoa\App\Services\CartService;
                                    $cartService = new CartService();
                                    echo $cartService->countTotalItems();
                                ?>
                            </span>
                        </a>
                        <div id="mini-cart" class="position-absolute">
                            <div class="d-flex flex-column h-100 p-2">
                                <div id="mini-cart-items" class="flex-grow-1">
                                    <?php if($cartService->countTotalItems() !== 0): ?>
                                        <?php foreach(array_reverse($cartService->getItems()) as $key => $item): ?>
                                            <div class="row mini-cart-item mx-0 pb-3 cart-item-<?php echo $key ?>" style="font-size: 14px;">
                                                <div class="col-4 ps-0">
                                                    <img src="<?php echo $item['p_image'] ?>" alt="">
                                                </div>
                                                <div class="col-8 ps-0">
                                                    <div class="name"><?php echo $item['p_name'] ?></div>
                                                    <div class="variation d-flex">
                                                        <?php if(isset($item['color_id'])): ?>
                                                        <div class="color d-flex align-items-center">
                                                            <img src="<?php echo url($item['color_image']) ?>" alt="">
                                                            <span class="ms-1"><?php echo $item['color_name'] ?></span>
                                                        </div>
                                                        <?php if(isset($item['color_id']) && isset($item['size'])): ?>
                                                        <div class="vr mx-1 mx-1"></div>
                                                        <?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php if(isset($item['size'])): ?>
                                                        <div class="size"><?php echo $item['size'] ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="price">Giá: <span style="font-weight:600"><?php echo number_format($item['price'],0,"",".") ?></span></div>
                                                    <div class="quantity">Số lượng: <span style="font-weight:600"><?php echo $item['quantity'] ?></span></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>    
                                    <?php else: ?>
                                        <div id="empty-title" style="padding: 30px;">Bạn không có sản phẩm nào trong giỏ hàng của bạn.</div>
                                    <?php endif; ?>
                                </div>
                                <?php if($cartService->countTotalItems() !== 0):   ?>
                                <div id="mini-cart-subtotal" class="mt-2">
                                    <div style="font-weight:600" class="d-flex">
                                        <div class="flex-grow-1">Tạm tính</div>
                                        <div class="mini-number"><?php echo number_format($cartService->countSubtotal(),0,"",".") ?> VND</div>
                                    </div>
                                    <div style="gap:10px" class="row mx-0">
                                        <button class="btn btn-secondary w-50 mt-2 col" onclick="window.location.href='<?php echo url('cart'); ?>'">Xem giỏ hàng</button>
                                        <button class="btn btn-outline w-50 mt-2 col" onclick="window.location.href='<?php echo url('checkout'); ?>'">Thanh toán</button>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>