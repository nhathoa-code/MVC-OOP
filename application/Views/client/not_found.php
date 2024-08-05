<?php include(APP_PATH . "/application/Views/client/header.php") ?>
<section id="content">
    <div class="container-fluid" style="height: 100%;">
        <style>
            .title-page{
                font-size: 1.625rem;
                line-height: 2rem;
                letter-spacing: .0125rem;
                margin-bottom: 1rem;
            }        
            .des{
                font-size: .875rem;
                line-height: 1.375rem;
                letter-spacing: .0063rem;
                color: #5C5C5C;
                margin-bottom: 1rem;
            }
            #not-found{
                display:flex;
                flex-direction:column;
                width:30%;
                margin:0 auto;
                padding-top:100px;
            }
        </style>
        <div id="not-found">
            <div class="title-page">Không tìm thấy trang cần xem</div>
            <div class="des">Trang bạn đang tìm kiếm không tồn tại.</div>
            <a href="/" class="btn btn-link">Trở về trang chủ</a>
        </div>
           
    </div>
</section> 
      
<?php include(APP_PATH . "/application/Views/client/footer.php") ?>