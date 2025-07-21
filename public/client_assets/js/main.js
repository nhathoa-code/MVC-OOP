var color_name;
var color_image;
var color_ID;
var p_image;
var size;
var price;
var minicart_timeout;
var adding_to_cart = false;

if (typeof main !== "undefined") {
  $("section#banner #banner-slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    dots: true,
    prevArrow: `<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
  </svg></button>`,
    nextArrow: `<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi        bi-chevron-right" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
    </svg></button>`,
  });
}

if (typeof product_detail !== "undefined") {
  $("#add-to-cart, #buy-now").click(function () {
    if (adding_to_cart === true) {
      return;
    }
    if (must_pick_size && !size) {
      notif({
        msg: "Vui lòng chọn size",
        type: "warning",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 5000,
        animation: "slide",
      });
      return;
    }
    adding_to_cart = true;
    const this2 = this;
    $(
      this
    ).html(`<div class="cls_loader ${$(this).attr("id") === "buy-now" ? "bounce-white" : ""}" style="display: flex">
                  <div class="sk-three-bounce">
                    <div class="sk-child sk-bounce1"></div>
                    <div class="sk-child sk-bounce2"></div>
                    <div class="sk-child sk-bounce3"></div>
                  </div>
                </div>
              `);
    const formData = new FormData();
    formData.append("csrf_token", csrf_token);
    if (size) {
      formData.append("size", size);
    }
    if (color_ID) {
      formData.append("color_id", color_ID);
      formData.append("color_name", color_name);
      formData.append("color_image", color_image);
    }
    formData.append("price", price);
    formData.append("p_id", p_id);
    formData.append("p_name", p_name);
    formData.append("p_image", p_image);
    if ($(this).attr("id") === "buy-now") {
      formData.append("buy_now", true);
    }
    $.ajax({
      type: "POST",
      url: add_to_cart_url,
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        adding_to_cart = false;
        if (res.hasOwnProperty("redirect")) {
          window.location.href = res.redirect;
          return;
        }
        if ($("#empty-title").length) {
          $("#empty-title").remove();
        }
        if (!$("#mini-cart-subtotal").length) {
          $("#mini-cart-items")
            .after(` <div id="mini-cart-subtotal" class="mt-2">
                      <div style="font-weight:600" class="d-flex">
                          <div class="flex-grow-1">Tạm tính</div>
                          <div class="mini-number">${
                            new Intl.NumberFormat({ style: "currency" }).format(
                              res.subtotal
                            ) + "đ"
                          }</div>
                      </div>
                      <div style="gap:10px" class="row mx-0">
                          <button class="btn btn-secondary w-50 mt-2 col" onclick="window.location.href='${cart_url}'">Xem giỏ hàng</button>
                          <button class="btn btn-outline w-50 mt-2 col" onclick="window.location.href='${checkout_url}'">Thanh toán</button>
                      </div>
                  </div>`);
        }
        if (res.hasOwnProperty("item")) {
          $("#mini-cart-items")
            .prepend(`<div class="row mini-cart-item mx-0 pb-3 cart-item-${
            res.item.id
          }" style="font-size: 14px;">
                <div class="col-4 ps-0">
                    <img src="${res.item.p_image}" alt="">
                </div>
                <div class="col-8 ps-0">
                    <div class="name">${res.item.p_name}</div>
                    <div class="variation d-flex">
                        ${
                          res.item.color_id
                            ? `<div class="color d-flex align-items-center">
                            <img src="${res.item.color_image}" alt="">
                            <span class="ms-1">${res.item.color_name}</span>
                        </div>
                        ${
                          res.item.size
                            ? '<div class="vr mx-1 mx-1"></div>'
                            : ""
                        }`
                            : ""
                        }
                        ${
                          res.item.size
                            ? `<div class="size">${res.item.size}</div>`
                            : ""
                        }
                    </div>
                    <div class="price">Giá: <span style="font-weight:600">${new Intl.NumberFormat(
                      { style: "currency" }
                    ).format(res.item.price)}</span></div>
                    <div class="quantity">Số lượng: <span style="font-weight:600">${
                      res.item.quantity
                    }</span></div>
                </div>
            </div>`);
        } else {
          $(`#mini-cart-items .cart-item-${res.index} .quantity span`).text(
            res.quantity
          );
        }
        $("#cart-header .number").text(res.totalItems);
        $("#cart-header #mini-cart-subtotal .mini-number").text(
          new Intl.NumberFormat({ style: "currency" }).format(res.subtotal) +
            "đ"
        );
        $("#cart-header #mini-cart").css({
          display: "block",
        });
        if (minicart_timeout) {
          clearTimeout(minicart_timeout);
        }
        minicart_timeout = setTimeout(function () {
          $("#cart-header #mini-cart").css({
            display: "none",
          });
        }, 5000);
      },
      error: function (xhr, status, error) {
        console.log(xhr);
        adding_to_cart = false;
        if ($(this2).attr("id") === "buy-now") {
          $(this2).text(`Mua ngay`);
        } else {
          $(this2).text(`Thêm vào giỏ hàng`);
        }
        notif({
          msg: xhr.responseJSON,
          type: "warning",
          position: "center",
          height: "auto",
          top: 80,
          timeout: 5000,
          animation: "slide",
        });
      },
    }).always(function () {
      adding_to_cart = false;
      if ($(this2).attr("id") === "buy-now") {
        $(this2).text(`Mua ngay`);
      } else {
        $(this2).text(`Thêm vào giỏ hàng`);
      }
    });
  });

  $(".recent-viewed-products").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: false,
    prevArrow: `<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
  </svg></button>`,
    nextArrow: `<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi        bi-chevron-right" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
    </svg></button>`,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
        },
      },
    ],
  });

  $("select[name=province]").change(function () {
    let province_id = $(this).val();
    let districts = provinces.find((item) => item.id == province_id).districts;
    let str = "";
    districts.forEach((item) => {
      str += `<option value="${item.id}">${item.name}</option>`;
    });
    $("select[name=district]").html(
      `<option disabled selected>Quận/huyện</option>`
    );
    $("select[name=district]").append(str);
  });

  $("select[name=color]").change(function () {
    var selectedOption = $(this).find("option:selected");
    var image = selectedOption.data("image");
    $("img#color-image").attr("src", image);
  });
  var searching_stores = false;
  $("#search-stores").click(function () {
    if (searching_stores) {
      return;
    }
    searching_stores = true;
    $("#list-store").html(`<div class="cls_loader mt-5" style="display: flex">
                                <div class="sk-three-bounce">
                                    <div class="sk-child sk-bounce1"></div>
                                    <div class="sk-child sk-bounce2"></div>
                                    <div class="sk-child sk-bounce3"></div>
                                </div>
                          </div>`);
    $(".note-result").css({
      display: "none",
    });
    let color = $("select[name=color]").val();
    let size = $("select[name=size]").val();
    let province = $("select[name=province]").val();
    let district = $("select[name=district]").val();
    let product = p_id;
    $.ajax({
      type: "GET",
      url: search_stores_url,
      data: {
        color,
        size,
        province,
        district,
        product,
      },
      success: function (response) {
        let str = "";
        if (response.length > 0) {
          $(".note-result").css({
            display: "block",
          });
        } else {
          $(".note-result").css({
            display: "none",
          });
          str = "<div class='mt-3'>Không tìm thấy cửa hàng nào!</div>";
        }
        response.forEach((item) => {
          str += `<div class="store-item instock position-relative"> 
                    <div class="title">${item.name}</div>
                    <div class="address">${item.address}, ${item.district}, ${item.province}</div>
                    <div class="time-open">Giờ hoạt động &nbsp;<span>08:30 - 21:30</span></div>
                    <div class="number">Còn <span id="fqtylabel-1057">${item.stock}</span> sản phẩm</div>
                    <a href="${item.coordinates}">
                      <svg class="store-location" width="20" height="20" viewBox="0 0 384 512">
                          <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
                      </svg>
                    </a>
                  </div>`;
        });
        $("#totalstore").text(response.length);
        $("#list-store").html(str);
      },
      error: function (xhr, status, error) {
        console.log(xhr);
      },
    }).always(function () {
      searching_stores = false;
    });
  });

  var is_evaluated = false;
  $("#stores").click(function () {
    $("#looking-stores").css({
      display: "flex",
    });
    let store_content_height = $("#stores-content").height();
    let store_content_head_height = $("#store-content-head").height();
    $("#list-store").css(
      "height",
      store_content_height -
        store_content_head_height -
        (is_evaluated ? 30 : 50)
    );
    if (!is_evaluated) {
      is_evaluated = true;
    }
  });

  $("#close-looking-stores").click(function () {
    $("#looking-stores").css({
      display: "none",
    });
  });
}

$(".delete-cart-item").click(function () {
  const this2 = this;
  const formData = new FormData();
  formData.append("csrf_token", csrf_token);
  $.ajax({
    type: "POST",
    url: delete_cart_item_url + "/" + $(this).data("index"),
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      $(`.cart-item-${res.index}`).remove();
      $("#cart-header .number").text(res.totalItems);
      $(".cart-content #cart-number").text(res.totalItems);
      $("#cart-subtotal #number").text(
        new Intl.NumberFormat({ style: "currency" }).format(res.subtotal) +
          " VND"
      );
      if (res.totalItems === 0) {
        $(".cart-content")
          .html(`<div style="padding-top:50px" class="text-center">
                    <div style="font-weight: 600;font-size:20px;margin-top:20px">Chưa có sản phẩm trong giỏ hàng</div>
                    <a class="btn btn-secondary mt-2" href="${res.home_url}">Tiếp tục mua sắm</a>
                </div>`);
        $("#mini-cart-items").html(
          `<div style="padding: 30px;">Bạn không có sản phẩm nào trong giỏ hàng của bạn.</div>`
        );
        $("#mini-cart-subtotal").remove();
      }
    },
    error: function (xhr, status, error) {
      console.log(xhr);
    },
  });
});

$(".toggle-qty").click(function () {
  let sign = $(this).data("sign");
  let index = $(this).data("index");
  let current_value = Number(
    $(`#cart-table #cart-body .cart-qty-${index}`).val()
  );
  if (current_value === 1 && sign === "-") {
    if (!confirm("Bạn có muốn xóa sản phẩm này khỏi giỏ hàng không?")) {
      return;
    }
  }
  const formData = new FormData();
  formData.append("csrf_token", csrf_token);
  formData.append("index", index);
  formData.append("sign", sign);
  $.ajax({
    type: "POST",
    url: update_cart_item_url,
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      $(`#cart-table #cart-body .cart-qty-${res.index}`).val(res.quantity);
      $(`#mini-cart .cart-qty-${res.index}`).text(res.quantity);
      $("#cart-subtotal #number").text(
        new Intl.NumberFormat({ style: "currency" }).format(res.subtotal) + "đ"
      );
      $(`#mini-cart .mini-number`).text(
        new Intl.NumberFormat({ style: "currency" }).format(res.subtotal) + "đ"
      );
      $("#cart-header .number").text(res.totalItems);
      $(".cart-content #cart-number").text(res.totalItems);
      if (res.totalItems === 0) {
        $(".cart-content")
          .html(`<div style="padding-top:50px" class="text-center">
                    <div style="font-weight: 600;font-size:20px;margin-top:20px">Chưa có sản phẩm trong giỏ hàng</div>
                    <a class="btn btn-secondary mt-2" href="${res.home_url}">Tiếp tục mua sắm</a>
                </div>`);
        $("#mini-cart-items").html(
          `<div style="padding: 30px;">Bạn không có sản phẩm nào trong giỏ hàng của bạn.</div>`
        );
        $("#mini-cart-subtotal").remove();
      }
      if (current_value === 1 && sign === "-") {
        $(`.cart-item-${res.index}`).remove();
      } else {
        $(`.cart-item-${res.index}`)
          .find(".cart-item-total")
          .text(
            new Intl.NumberFormat({ style: "currency" }).format(
              res.price * res.quantity
            ) + "đ"
          );
      }
    },
    error: function (xhr, status, error) {
      notif({
        msg: xhr.responseJSON,
        type: "warning",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 5000,
        animation: "slide",
      });
    },
  });
});

if (typeof collection !== "undefined") {
  let loading = false;
  $(document).on("click", ".view-more", function () {
    if (loading === true) {
      return;
    }
    loading = true;
    const this2 = this;
    $(this).html(`<div class="cls_loader" style="display: flex">
                    <div class="sk-three-bounce">
                      <div class="sk-child sk-bounce1"></div>
                      <div class="sk-child sk-bounce2"></div>
                      <div class="sk-child sk-bounce3"></div>
                    </div>
                  </div>
                `);
    let page = Number($(this).data("currentpage")) + 1;
    $(this).data("currentpage", page);
    const currentUrl = window.location.href;
    const urlObj = new URL(currentUrl);
    const params = new URLSearchParams(urlObj.search);
    params.set("page", page);
    urlObj.search = decodeURIComponent(params.toString());
    history.pushState({ path: urlObj.toString() }, "", urlObj.toString());
    $.ajax({
      type: "GET",
      url: urlObj.toString(),
      processData: false,
      contentType: false,
      success: function (response) {
        loading = false;
        let left_products = total_products - page * limit;
        if (left_products <= 0) {
          $(this2).remove();
        } else {
          $(this2).html(
            `Xem thêm <span class="viewmore-totalitem">${
              left_products > limit ? limit : left_products
            }</span> sản phẩm`
          );
        }
        $("#displayed_products").text(
          $("#displayed_products").data("displayed-products") +
            response.collection.length
        );
        $("#displayed_products").data(
          "displayed-products",
          $("#displayed_products").data("displayed-products") +
            response.collection.length
        );
        $(".collection").append(buildProduct(response.collection));
      },
      error: function (xhr, status, error) {
        console.log(xhr);
      },
    }).always(function () {
      loading = false;
    });
  });

  var filtering = false;
  $("#filter-form, #filter-form-mobile").on("submit", function (e) {
    e.preventDefault();
    if (filtering == true) return;
    const form = $(this);
    const submitButton = $(this).find("button[type='submit']");
    submitButton.html(`<div class="cls_loader bounce-white" style="display: flex">
                  <div class="sk-three-bounce">
                    <div class="sk-child sk-bounce1"></div>
                    <div class="sk-child sk-bounce2"></div>
                    <div class="sk-child sk-bounce3"></div>
                  </div>
                </div>
              `);
    const checkedInputs = $(this).find('input[type="checkbox"]:checked');
    const params = new URLSearchParams();
    checkedInputs.each(function () {
      params.append($(this).attr("name"), $(this).val());
    });
    const query_string = decodeURIComponent(params.toString());
    const query_url = collection_url + "?" + query_string;
    history.pushState({ path: query_url }, "", query_url);
    filtering = true;
    $(".view-more").data("currentpage", 1);
    $.ajax({
      type: "GET",
      url: query_url,
      processData: false,
      contentType: false,
      success: function (response) {
        // return console.log(response);
        displayed_products = response.collection.length;
        total_products = response.number_of_products;
        total_pages = response.total_pages;
        $(".collection").html(buildProduct(response.collection));
        if (total_pages > 1) {
          $(".group-pagging")
            .replaceWith(`<div class="group-pagging flex-column mt-5">
                        <div style="font-size: 13px;color:#8a8a8a;margin-bottom:5px">hiển thị <span data-displayed-products="${displayed_products}" id="displayed_products">${displayed_products}</span> trong <span id="total_products">${total_products}</span> sản phẩm</div>
                        <button class="view-more cate-view-more" data-currentpage="1">
                            Xem thêm <span class="viewmore-totalitem">${
                              total_products - limit > limit
                                ? limit
                                : total_products - limit
                            }</span> sản phẩm
                        </button>
                      </div>`);
        } else {
          if (displayed_products === 0) {
            $(".group-pagging")
              .replaceWith(`<div class="group-pagging flex-column mt-5">
                        <div style="font-size: 13px;color:#8a8a8a;margin-bottom:5px">Không tìm thấy sản phẩm!</div>
                    </div>`);
          } else {
            $(".group-pagging")
              .replaceWith(`<div class="group-pagging flex-column mt-5">
                        <div style="font-size: 13px;color:#8a8a8a;margin-bottom:5px">hiển thị <span data-displayed-products="${displayed_products}" id="displayed_products">${displayed_products}</span> trong <span id="total_products">${total_products}</span> sản phẩm</div>
                    </div>`);
          }
        }
        window.scrollTo(0, 0);
      },
      error: function (xhr) {
        console.log(xhr);
      },
    }).always(function () {
      filtering = false;
      submitButton.html("Lọc");
      if (form.attr("id") === "filter-form-mobile") {
        $("#slideDiv").toggleClass("slide-in slide-out");
        $(".filter-mobile #number-check").text(
          $('#filter-form-mobile input[type="checkbox"]:checked').length
        );
      }
    });
  });
  $("#filter-mobile").on("click", function () {
    $("#slideDiv").toggleClass("slide-in slide-out");
  });
  $("#close-filter-mobile").on("click", function () {
    $("#slideDiv").toggleClass("slide-in slide-out");
  });
  $("#navbarSupportedContent").on("hidden.bs.offcanvas", function () {
    $(".filter-mobile").css({
      zIndex: 20,
    });
  });
  $("#navbarSupportedContent").on("show.bs.offcanvas", function () {
    $(".filter-mobile").css({
      zIndex: 0,
    });
  });

  $(document).on("change", "#filter-form input[type=checkbox]", function () {
    let index = $(this).index("#filter-form input[type=checkbox]");
    let mobileCheckbox = $("#filter-form-mobile input[type=checkbox]").eq(
      index
    );
    mobileCheckbox.prop("checked", $(this).prop("checked"));
  });

  // Checkbox change event for mobile form
  $(document).on(
    "change",
    "#filter-form-mobile input[type=checkbox]",
    function () {
      let index = $(this).index("#filter-form-mobile input[type=checkbox]");
      let pcCheckbox = $("#filter-form input[type=checkbox]").eq(index);
      pcCheckbox.prop("checked", $(this).prop("checked"));
    }
  );
}

$(".toggle-collapse").click(function () {
  if (!$(this).hasClass("collapsed")) {
    $(this).find("svg")
      .replaceWith(`<svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" d="M4.855 7.496a.6.6 0 0 1 .85 0l6.775 6.776 6.775-6.776a.6.6 0 0 1 .85.85l-7.2 7.2a.6.6 0 0 1-.85 0l-7.2-7.2a.6.6 0 0 1 0-.85Z" clip-rule="evenodd"></path>
  </svg>`);
  } else {
    $(this).find("svg")
      .replaceWith(`<svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M12.056 7.496a.6.6 0 0 1 .85 0l7.2 7.2a.6.6 0 1 1-.85.85L12.48 8.768l-6.775 6.776a.6.6 0 1 1-.85-.85l7.2-7.2Z" clip-rule="evenodd"></path>
</svg>`);
  }
});

async function get_services(shop_id, from_district, to_district) {
  let res = await axios.get(
    "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services",
    {
      params: {
        shop_id,
        from_district,
        to_district,
      },
      headers: {
        "Content-Type": "application/json",
        Token: "7625ae34-2bac-11ee-b1d4-92b443b7a897",
      },
    }
  );
  return res.data.data;
}
async function calc_shipping_fee(district_id, ward_code) {
  $("#overlay").css({
    display: "flex",
  });
  let services = await get_services(125296, 1458, district_id);
  let service = services.find((item) => item.service_type_id === 2);
  if (service) {
    axios
      .post(
        "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee",
        {
          from_district_id: 1458,
          from_ward_code: "21906",
          service_id: service.service_id,
          service_type_id: service.service_type_id,
          to_district_id: district_id,
          to_ward_code: `${ward_code}`,
          height: 1,
          length: 1,
          weight: 1,
          width: 1,
          insurance_value: subtotal,
          cod_failed_amount: 0,
          coupon: null,
        },
        {
          headers: {
            "Content-Type": "application/json",
            Token: "7625ae34-2bac-11ee-b1d4-92b443b7a897",
            ShopId: 125296,
          },
        }
      )
      .then((response) => {
        $("#overlay").css({
          display: "none",
        });
        shipping_fee = response.data.data.total;
        $(".price_total").text(
          new Intl.NumberFormat({ style: "currency" }).format(
            subtotal + shipping_fee - coupon_amount
          ) + "đ"
        );
        $("#delivery_fee").text(
          new Intl.NumberFormat({ style: "currency" }).format(shipping_fee) +
            "đ"
        );
      })
      .catch((err) => {
        console.log(err);
        shipping_fee = 0;
      });
  }
}

if (typeof check_out !== "undefined") {
  var district_id;
  var ward_code;
  var coupon;
  var coupon_amount = 0;
  var shipping_fee = 0;
  var point = 0;

  if (district_id && ward_code) {
    calc_shipping_fee(district_id, ward_code);
  }
  var address_id;
  var temp_id;
  $(".address-item input[type=radio]").on("click", function () {
    temp_id = $(this).data("id");
  });
  $("#address-confirm").on("click", function () {
    $("#listAddressModal").modal("hide");
    if (address_id == temp_id || !temp_id) {
      return;
    }
    address_id = temp_id;
    let address = addresses.find((item) => item.id == address_id);
    if (address) {
      $(".price_total").text(
        new Intl.NumberFormat({ style: "currency" }).format(
          subtotal + shipping_fee - coupon_amount
        ) + "đ"
      );
      address = address.meta_value;
      if (!$("#checkout-form input[name=province]").length) {
        $("#checkout-form").append(
          `<input type="hidden" name="province" value="${address.province}" />`
        );
      } else {
        $("#checkout-form input[name=province]").val(address.province);
      }
      if (!$("#checkout-form input[name=district]").length) {
        $("#checkout-form").append(
          `<input type="hidden" name="district" value="${address.district}" />`
        );
      } else {
        $("#checkout-form input[name=district]").val(address.district);
      }
      if (!$("#checkout-form input[name=ward]").length) {
        $("#checkout-form").append(
          `<input type="hidden" name="ward" value="${address.ward}" />`
        );
      } else {
        $("#checkout-form input[name=ward]").val(address.ward);
      }
      if (!$("#checkout-form input[name=address]").length) {
        $("#checkout-form").append(
          `<input type="hidden" name="address" value="${address.address}" />`
        );
      } else {
        $("#checkout-form input[name=address]").val(address.address);
      }
      if (!$("#checkout-form input[name=name]").length) {
        $("#checkout-form").append(
          `<input type="hidden" name="name" value="${address.name}" />`
        );
      } else {
        $("#checkout-form input[name=name]").val(address.name);
      }
      if (!$("#checkout-form input[name=phone]").length) {
        $("#checkout-form").append(
          `<input type="hidden" name="phone" value="${address.phone}" />`
        );
      } else {
        $("#checkout-form input[name=phone]").val(address.phone);
      }
    } else {
      return;
    }
    $(
      "#user-address"
    ).html(`<div class="d-flex justify-content-between align-items-center" style="font-weight: 500;">${address.name}${address.hasOwnProperty("default") ? `<span style="font-size:13px;font-weight:normal;color:#800019">Mặc định</span>` : ""}</div>
                                    <div style="font-size: 14px;">
                                        Địa chỉ: ${
                                          address.address
                                        }, ${address.ward}, ${address.district}, ${address.province}
                                    </div>
                                    <div style="font-size: 14px;">
                                        Số điện thoại: ${address.phone}
                                    </div>`);
    if (!$("#address-toggle").length) {
      $("#user-address").after(`<div id="address-toggle" class="text-end">
                                    <a style="font-size:15px" href="${add_address_url}">Thêm địa chỉ mới</a> hoặc <a data-bs-toggle="modal" data-bs-target="#listAddressModal" style="font-size:15px" href="javascript:void(0)">Chọn địa chỉ khác</a>
                                </div>`);
    }
    calc_shipping_fee(
      parseInt(address.district_id),
      parseInt(address.ward_code)
    );
  });
  var error_coupon_time_out;
  $("#apply-coupon").on("click", function () {
    let coupon_code = $("#checkout-form input[name=coupon_code]").val();
    if (coupon && coupon === coupon_code) {
      return;
    }
    if (coupon_code === "") {
      return;
    }
    $.ajax({
      type: "POST",
      url: apply_coupon_url,
      data: {
        coupon_code: coupon_code,
        csrf_token: $("form#checkout-form input[name=csrf_token]").val(),
      },
      success: function (response) {
        coupon_amount = response.amount;
        coupon = response.coupon_code;
        $("#checkout-form").append(
          `<input type="hidden" name="coupon" value="${coupon}" />`
        );
        $("#checkout-form .info_title.coupon").append(
          ` <span onclick="removeCoupon(this)" style="font-size: 12px;color:#800019;cursor:pointer">(VNHCP111) xóa</span>`
        );
        $("#checkout-form #coupon").text(
          "-" +
            new Intl.NumberFormat({ style: "currency" }).format(coupon_amount) +
            "đ"
        );
        $(".price_total").text(
          new Intl.NumberFormat({ style: "currency" }).format(
            subtotal + shipping_fee - coupon_amount - point * 10000
          ) + "đ"
        );
        notif({
          msg: response.message,
          type: "success",
          position: "center",
          height: "auto",
          top: 80,
          timeout: 5000,
          animation: "slide",
        });
      },
      error: function (xhr) {
        console.log(xhr);
        $("#checkout-form input[name=coupon]").remove();
        $("#checkout-form #coupon-error").text(xhr.responseJSON);
        $("#checkout-form .coupon").find("span").remove();
        coupon = null;
        coupon_amount = 0;
        $("#checkout-form #coupon").text("--");
        $(".price_total").text(
          new Intl.NumberFormat({ style: "currency" }).format(
            subtotal + shipping_fee - coupon_amount - point * 10000
          ) + "đ"
        );
        if (error_coupon_time_out) {
          clearTimeout(error_coupon_time_out);
        }
        error_coupon_time_out = setTimeout(() => {
          $("#checkout-form #coupon-error").text("");
        }, 5000);
      },
    });
  });
  function removeCoupon(element) {
    $(element).remove();
    $("#checkout-form input[name=coupon]").remove();
    coupon = null;
    coupon_amount = 0;
    $("#checkout-form #coupon").text("--");
    $(".price_total").text(
      new Intl.NumberFormat({ style: "currency" }).format(
        subtotal + shipping_fee - coupon_amount - point * 10000
      ) + "đ"
    );
  }
  function removeVpoint(element) {
    $(element).parent().parent().remove();
    $("#checkout-form input[name=v_point]").remove();
    point = 0;
    $(".price_total").text(
      new Intl.NumberFormat({ style: "currency" }).format(
        subtotal + shipping_fee - coupon_amount - point * 10000
      ) + "đ"
    );
  }
  var error_point_time_out;
  $("#apply-point").on("click", function () {
    if (point > 0) {
      $("#checkout-form #point-error").text(
        "Vui lòng bỏ áp dụng v-point trước"
      );
      if (error_point_time_out) {
        clearTimeout(error_point_time_out);
      }
      error_point_time_out = setTimeout(() => {
        $("#checkout-form #point-error").text("");
      }, 5000);
      return;
    }
    let point_input = $("#checkout-form input[name=v-point]").val();
    if (isNaN(point_input) || point_input === "") {
      return;
    }
    $.ajax({
      type: "POST",
      url: apply_point_url,
      data: {
        point: point_input,
        csrf_token: csrf_token,
      },
      success: function (response) {
        point = response.point;
        if (!$("#checkout-form input[name=v_point]").length) {
          $("#checkout-form").append(
            `<input type="hidden" name="v_point" value="${point}" />`
          );
        } else {
          $("#checkout-form input[name=v_point]").val(point);
        }
        $(".coupon-row").after(`<div class="row-info">
                                    <span class="info_title">V point - <span onclick="removeVpoint(this)" style="font-size: 12px;color:#800019;cursor:pointer">xóa</span></span>
                                    <span style="font-size:14px">${
                                      "-" +
                                      new Intl.NumberFormat({
                                        style: "currency",
                                      }).format(point * 10000) +
                                      "đ"
                                    }</span>
                                </div>`);
        $(".price_total").text(
          new Intl.NumberFormat({ style: "currency" }).format(
            subtotal + shipping_fee - coupon_amount - point * 10000 < 0
              ? 0
              : subtotal + shipping_fee - coupon_amount - point * 10000
          ) + "đ"
        );
        notif({
          msg: response.message,
          type: "success",
          position: "center",
          height: "auto",
          top: 80,
          timeout: 5000,
          animation: "slide",
        });
      },
      error: function (xhr) {
        console.log(xhr);
        if (xhr.responseJSON.hasOwnProperty("errors")) {
          const errors_arr = [];
          const errors = xhr.responseJSON.errors;
          for (let item in errors) {
            for (let Item in errors[item]) {
              errors_arr.push(errors[item][Item]);
            }
          }
          $("#checkout-form #point-error").text(errors_arr.join("\n"));
        } else {
          $("#checkout-form #point-error").text(xhr.responseJSON);
        }
        if (error_point_time_out) {
          clearTimeout(error_point_time_out);
        }
        error_point_time_out = setTimeout(() => {
          $("#checkout-form #point-error").text("");
        }, 5000);
      },
    });
  });

  $("#checkout-form").submit(function (e) {
    e.preventDefault();
    $("#overlay").css({
      display: "flex",
    });
    if (shipping_fee && shipping_fee > 0) {
      $(this).append(
        `<input name="shipping_fee" value="${shipping_fee}" type="hidden" class="form-control">`
      );
    }
    $(this).unbind("submit");
    $(this).submit();
  });
}

$("#cart-header").on({
  mouseenter: function () {
    if (minicart_timeout) {
      clearTimeout(minicart_timeout);
    }
    $(this).find("#mini-cart").css({
      display: "block",
    });
  },
  mouseleave: function () {
    $(this).find("#mini-cart").css({
      display: "none",
    });
  },
});

$(document).on("click", "#add-wl", function () {
  const this2 = this;
  $.ajax({
    type: "post",
    url: add_to_wl_url,
    data: {
      p_id: p_id,
      csrf_token: csrf_token,
    },
    success: function (response) {
      $(this2)
        .replaceWith(`<svg class="remove-wl" data-p_id="${p_id}" style="margin-bottom: 5px;cursor:pointer" width="24" height="24" viewBox="0 0 32 32" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.6526 5.94266C22.5995 -1.35734 39.9656 11.416 15.6526 27.84C-8.66048 11.4173 8.70691 -1.35734 15.6526 5.94266Z" fill="#52575C"> </path>
                      </svg>`);
      let current_number = $("#wishlist-header span.number").data("number");
      $("#wishlist-header span.number").data("number", current_number + 1);
      $("#wishlist-header span.number").text(current_number + 1);
      notif({
        msg: response,
        type: "success",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 5000,
        animation: "slide",
      });
    },
    error: function (xhr, status, error) {
      notif({
        msg: xhr.responseJSON,
        type: "warning",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 7000,
        animation: "slide",
      });
    },
  });
});

$(document).on("click", ".remove-wl", function () {
  let p_id = $(this).data("p_id");
  let this2 = this;
  $.ajax({
    type: "post",
    url: remove_from_wl_url,
    data: {
      p_id: p_id,
      csrf_token: csrf_token,
    },
    success: function (response) {
      if (typeof product_detail === "undefined") {
        $(this2).parent().parent().remove();
      } else {
        $(this2)
          .replaceWith(`<svg id="add-wl" style="margin-bottom: 5px;cursor:pointer" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M11.5332 6.93144L10.7688 6.14476C8.97322 4.29941 5.68169 4.93586 4.49325 7.25588C3.93592 8.34745 3.8097 9.92258 4.82836 11.9341C5.80969 13.8702 7.85056 16.1884 11.5332 18.7147C15.2158 16.1884 17.2558 13.8702 18.2389 11.9341C19.2567 9.92169 19.1323 8.34745 18.5732 7.25677C17.3847 4.93675 14.0932 4.29852 12.2976 6.14387L11.5332 6.93144ZM11.5332 20C-4.82224 9.19279 6.49768 0.757159 11.3465 5.21942C11.4096 5.27809 11.4728 5.33853 11.5332 5.40165C11.5936 5.33853 11.6559 5.2772 11.7208 5.22031C16.5687 0.755381 27.8886 9.19101 11.5341 20H11.5332Z" fill="#25282B"></path>
                    </svg>`);
      }
      let current_number = $("#wishlist-header span.number").data("number");
      $("#wishlist-header span.number").data("number", current_number - 1);
      $("#wishlist-header span.number").text(current_number - 1);
      notif({
        msg: response,
        type: "success",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 7000,
        animation: "slide",
      });
    },
    error: function (xhr, status, error) {
      notif({
        msg: xhr.responseJSON,
        type: "warning",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 7000,
        animation: "slide",
      });
    },
  });
});

$(".search-container").on("click", function (event) {
  if ($(event.target).hasClass("search-container")) {
    $("html").removeClass("search-open");
  }
});

if (typeof search !== "undefined") {
  let loading = false;
  $(".view-more-search").click(function () {
    if (loading === true) {
      return;
    }
    loading = true;
    const this2 = this;
    $(this).html(`<div class="cls_loader" style="display: flex">
                    <div class="sk-three-bounce">
                      <div class="sk-child sk-bounce1"></div>
                      <div class="sk-child sk-bounce2"></div>
                      <div class="sk-child sk-bounce3"></div>
                    </div>
                  </div>
                `);
    let page = Number($(this).data("currentpage")) + 1;
    $(this).data("currentpage", page);
    const currentUrl = window.location.href;
    const urlObj = new URL(currentUrl);
    const params = new URLSearchParams(urlObj.search);
    params.set("page", page);
    urlObj.search = decodeURIComponent(params.toString());
    history.pushState({ path: urlObj.toString() }, "", urlObj.toString());
    $.ajax({
      type: "GET",
      url: urlObj.toString(),
      success: function (response) {
        loading = false;
        let left_products = total_products - page * limit;
        if (left_products <= 0) {
          $(this2).remove();
        } else {
          $(this2).html(
            `Xem thêm <span class="viewmore-totalitem">${
              left_products > limit ? limit : left_products
            }</span> sản phẩm`
          );
        }
        $("#displayed_products").text(
          $("#displayed_products").data("displayed-products") +
            response.collection.length
        );
        $("#displayed_products").data(
          "displayed-products",
          $("#displayed_products").data("displayed-products") +
            response.collection.length
        );
        $(".collection").append(buildProduct(response.collection));
      },
      error: function (xhr, status, error) {
        console.log(xhr);
      },
    });
  });
}

function open_search() {
  $("html").addClass("search-open");
  $(".filter-mobile").css({
    zIndex: 0,
  });
}

function close_search() {
  $("html").removeClass("search-open");
  $(".filter-mobile").css({
    zIndex: 20,
  });
}

$(document).on("click", "div.eye-icon-wrap", function () {
  if ($(this).prev().attr("type") === "text") {
    $(this).prev().attr("type", "password");
    $(this)
      .html(`<svg width="20" height="20" fill="currentColor" class="bi bi-eye-fill eye-icon position-absolute" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>`);
  } else {
    $(this).prev().attr("type", "text");
    $(this)
      .html(`<svg width="20" height="20" fill="currentColor" class="bi bi-eye eye-icon position-absolute" viewBox="0 0 16 16">
  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
</svg>`);
  }
});

$("#close-chart").click(function () {
  $("#sizeChartModal").css({
    display: "none",
  });
  $(".slick-slider").css({
    zIndex: "0",
  });
});

$("#size-chart-open").click(function () {
  $("#sizeChartModal").css({
    display: "flex",
  });
  $(".slick-slider").css({
    zIndex: "-1",
  });
});

function buildProduct(products) {
  let str = "";
  products.forEach((item) => {
    str += `<div class="col">
                    <div class="collection-item">
                        <a href="${item.product_url}">
                            <img loading="lazy" src="${
                              item.thumbnail
                            }" width="188" height="188">
                            <div class="title">${item.p_name}</div>
                        </a>
                        <div class="price">
                          <div>
                            <span class="num">${new Intl.NumberFormat({
                              style: "currency",
                            }).format(
                              item.p_price
                            )}</span><span class="currency">đ</span>
                          </div>
                          <ul class="colors">`;
    if (item.colors.length <= 3) {
      item.colors.forEach((item) => {
        str += `
              <li>
                <span>
                  <img src="${item.color_image}" alt="">
                </span>
              </li>`;
      });
    } else {
      let break_index = 0;
      for (let i = 0; i < item.colors.length; i++) {
        let Item = item.colors[i];
        str += `
                <li>
                    <span>
                      <img src="${Item.color_image}" alt="">
                    </span>`;
        if (break_index === 2) {
          str += `<span class="num-colors">+${item.colors.length - 3}</span>`;
        }
        str += "</li>";
        if (break_index === 2) {
          break;
        }
        break_index++;
      }
    }
    str += `</ul></div></div></div>`;
  });
  return str;
}
