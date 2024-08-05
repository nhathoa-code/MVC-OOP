<?php include_once APP_PATH . "/application/Views/admin/header.php" ?>
 
<div class="heading"></div>

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
        width:50%;
        margin:0 auto;
        padding-top:100px;
    }
</style>
<div id="not-found">
    <div class="title-page">Không tìm thấy trang cần xem</div>
    <div class="des">Trang bạn đang tìm kiếm không tồn tại.</div>
</div>

<?php include_once APP_PATH . "/application/Views/admin/footer.php" ?>