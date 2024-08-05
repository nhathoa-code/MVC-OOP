          
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->
          <!-- Footer -->
            <!-- <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer> -->
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>


    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <?php if(current_route() === "admin/product/add" || strpos(current_route(),"admin/product/edit") !== false): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script>
            var stickyElement = $('#sticky-element');
            var container = $('.container-fluid');
            $(window).scroll(function() {
                var scrollHeight = container.height() - $(window).height();
                var scrollTop = $(window).scrollTop();
                if (scrollTop < scrollHeight) {
                    stickyElement.addClass('sticky');
                    stickyElement.width($('#variation-table').width() - 20);
                } else {
                    stickyElement.removeClass('sticky');
                }
            });

            var referenceElement = $('#variation-table');
            var targetElement = $('#sticky-element');
            targetElement.width(referenceElement.width());
            $(window).resize(function() {
                targetElement.width(referenceElement.width() - 20);
            });
        </script>
    <?php endif; ?>
    <script src="<?php echo url("admin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <?php if(strpos(current_route(),"coupon") !== false): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <?php endif; ?>

    <!-- Core plugin JavaScript-->
    <script src="<?php //echo public_url("admin_assets/vendor/jquery-easing/jquery.easing.min.js") ?>"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo url("admin_assets/js/sb-admin-2.min.js") ?>"></script>

    <!-- Page level plugins -->
    <script src="<?php //echo public_url("admin/vendor/chart.js/Chart.min.js") ?>"></script>
    <!-- Page level custom scripts -->
    <script src="<?php //echo public_url("admin/js/demo/chart-area-demo.js") ?>"></script>
    <script src="<?php //echo public_url("admin/js/demo/chart-pie-demo.js") ?>"></script>
    <script src="<?php echo url("admin_assets/js/main.js") ?>"></script>
    <?php if(current_route() === "admin/product/add" ): ?>
        <script src="<?php echo url("admin_assets/js/product-add.js") ?>"></script>
    <?php endif; ?>
    <?php if(strpos(current_route(),"admin/product/edit") !== false): ?>
        <script src="<?php echo url("admin_assets/js/product-edit.js") ?>"></script>
    <?php endif; ?>
    <?php if(current_route() === "admin/size-chart/add" || strpos(current_route(),"admin/size-chart/edit") !== false): ?>
        <script type="module" src="<?php echo url("admin_assets/js/size-chart.js") ?>"></script>
    <?php endif; ?>
    <?php if(strpos(current_route(),"admin/store") !== false): ?>
        <script src="<?php echo url("admin_assets/js/store.js") ?>"></script>
    <?php endif; ?>
    <?php if(strpos(current_route(),"admin/pos/sale") !== false): ?>
        <script src="<?php echo url("admin_assets/js/pos.js") ?>"></script>
    <?php endif; ?>
</body>

</html>