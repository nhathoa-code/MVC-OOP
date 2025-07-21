
    <footer>
        <div class="container-fluid">
            <div>
                © <span>Vòng Nhật Hòa</span>
            </div>
        </div>
    </footer>
    <script src="<?php echo url("client_assets/js/main.js") ?>"></script>
    <?php if(current_route() === "checkout"): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.5/axios.min.js"></script>
        <script src="<?php echo url("client_assets/js/provinces.js") ?>"></script>
    <?php endif; ?>
    <?php if(strpos(current_route(),"product/detail") !== false): ?>
        <script type="module">
            import PhotoSwipeLightbox from "<?php echo url("client_assets/js/photoswipe-lightbox.esm.js") ?>";
            const lightbox = new PhotoSwipeLightbox({
                gallery: '.photoswipe',
                children: 'a',
                wheelToZoom: true,
                pswpModule: () => import("<?php echo url("client_assets/js/photoswipe.esm.js") ?>")
            });
            lightbox.init();
        </script>
    <?php endif; ?>
    <script src="<?php echo url("client_assets/js/jquery.smartmenus.js") ?>"></script>
    <script>
        $(function() {
            $('#main-menu').smartmenus();
        });
    </script>
</body>
</html>


