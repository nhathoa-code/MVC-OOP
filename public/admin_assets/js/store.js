var finding_product = false;
$("form#find-product").submit(function (event) {
  event.preventDefault();
  if (finding_product) {
    return;
  }
  const formData = new FormData(this);
  const product_id = formData.get("product_id");
  finding_product = true;
  $("#overlay").css({
    display: "flex",
  });
  var select_lemenet = $(this);
  $.ajax({
    type: "GET",
    url: formData.get("url") + "/" + product_id,
    success: function (response) {
      const product = response;
      select_lemenet.find("option:selected").attr("disabled", true);
      $("#variation-table #body").append(`
            <div class="w-100 row-${product.id}">
                <div class="w-100 row mr-0 ml-0">
                    <div class="col-4 border-right border-bottom d-flex flex-column">
                        <div class="col cell color-gallery-cell">
                            <div>
                                ${product.p_name}
                                <br>
                                <span style="font-size: 0.85rem;">(${
                                  product.id
                                })</span>
                                <div>
                                  <svg class="remove-product" data-id="${
                                    product.id
                                  }" style="cursor:pointer" width="20px" height="20px" viewBox="0 0 16 16" version="1.1">
                                    <rect width="16" height="16" id="icon-bound" fill="none" />
                                    <polygon points="14.707,2.707 13.293,1.293 8,6.586 2.707,1.293 1.293,2.707 6.586,8 1.293,13.293 2.707,14.707 8,9.414 
                                        13.293,14.707 14.707,13.293 9.414,8 "/>
                                  </svg>
                                  </div>
                                <input type="hidden" name="products[]" value="${
                                  product.id
                                }">
                            </div>
                        </div>
                    </div>
                    <div class="col-8 px-0">
                        ${(
                          product.colors_sizes ??
                          product.colors ??
                          product.sizes ?? [1]
                        )
                          .map((color) => {
                            return `
                            <div class="row w-100 mr-0 ml-0 border-bottom${
                              !product.colors_sizes &&
                              !product.colors &&
                              !product.sizes
                                ? " h-100"
                                : ""
                            }">
                                ${
                                  product.colors_sizes || product.colors
                                    ? ` <div class="col-3 cell color-cell">
                                            <div class="d-inline-flex flex-column align-items-center">
                                                <div class="color-upload-icon-wrap">
                                                    <img style="width:30px" src="${
                                                      base_url +
                                                      color.color_image
                                                    }" alt="">
                                                </div>
                                                <span style="margin-top: 5px">${
                                                  color.color_name
                                                }</span>
                                                <input type="hidden" name="colors_of_product_${
                                                  product.id
                                                }[${color.id}]" value="${
                                        color.id
                                      }">
                                            </div>
                                        </div>`
                                    : `<div class="col-3 cell color-cell"></div>`
                                }
                                <div class="col-3 cell size-cell">
                                    ${(
                                      color.sizes ??
                                      (product.sizes ? [color] : [])
                                    )
                                      .map((size, index) => {
                                        return `
                                            <div class="row-cell${
                                              product.sizes ||
                                              (product.colors_sizes &&
                                                index ===
                                                  product.colors_sizes.length -
                                                    1)
                                                ? ` border-bottom-0`
                                                : ""
                                            }">${size.size}</div>
                                            <input type="hidden" name="${
                                              product.colors_sizes
                                                ? `sizes_of_color_${color.id}`
                                                : product.sizes
                                                ? `sizes_of_product_${product.id}`
                                                : ""
                                            }[]" value="${size.size}">    
                                        `;
                                      })
                                      .join("")}
                                </div>
                                <div class="col-3 cell inventory-cell">
                                    ${(color.sizes ?? [1])
                                      .map((size) => {
                                        return ` 
                                        <div class="row-cell">
                                            <input
                                                style="padding: 10px; height: 30px; width: 100%"
                                                name="${
                                                  product.colors_sizes ||
                                                  product.colors
                                                    ? `stock_of_product_${
                                                        product.id
                                                      }_color_${color.id}${
                                                        color.sizes
                                                          ? `_${size.size.replaceAll(
                                                              ".",
                                                              "*"
                                                            )}`
                                                          : ""
                                                      }"`
                                                    : product.sizes
                                                    ? `stock_of_product_${
                                                        product.id
                                                      }_size_${color.size.replaceAll(
                                                        ".",
                                                        "*"
                                                      )}`
                                                    : `stock_of_product_${product.id}`
                                                }"
                                                value="1"
                                                type="text"
                                                class="form-control inventory"
                                            />
                                        </div>`;
                                      })
                                      .join("")}
                                </div>
                                <div class="col-3 cell price-cell">
                                    ${(color.sizes ?? [color])
                                      .map((size) => {
                                        return ` 
                                        <div class="row-cell">
                                            <input
                                                style="padding: 10px; height: 30px; width: 100%"
                                                name="${
                                                  product.colors_sizes ||
                                                  product.colors
                                                    ? `price_of_product_${
                                                        product.id
                                                      }_color_${color.id}${
                                                        color.sizes
                                                          ? `_${size.size.replaceAll(
                                                              ".",
                                                              "*"
                                                            )}`
                                                          : ""
                                                      }"`
                                                    : product.sizes
                                                    ? `price_of_product_${
                                                        product.id
                                                      }_size_${color.size.replaceAll(
                                                        ".",
                                                        "*"
                                                      )}`
                                                    : `price_of_product_${product.id}`
                                                }"
                                                value="${
                                                  product.colors_sizes
                                                    ? product.colors_sizes
                                                        .find(
                                                          (item) =>
                                                            item.id == color.id
                                                        )
                                                        .sizes.find(
                                                          (item) =>
                                                            item.size ==
                                                            size.size
                                                        ).price
                                                    : product.colors
                                                    ? product.colors.find(
                                                        (item) =>
                                                          item.id == color.id
                                                      ).price
                                                    : product.sizes
                                                    ? product.sizes.find(
                                                        (item) =>
                                                          item.size == size.size
                                                      ).price
                                                    : product.p_price
                                                }"
                                                type="text"
                                                class="form-control inventory"
                                            />
                                        </div>`;
                                      })
                                      .join("")}
                                </div>
                            </div>  
                            `;
                          })
                          .join("")}
                    </div>
                </div>
            </div>
      `);
    },
    error: function () {
      notif({
        msg: "Có lỗi xảy ra, vui lòng kiểm tra và thử lại",
        type: "warning",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 5000,
        animation: "slide",
      });
    },
  }).always(function () {
    finding_product = false;
    $("#overlay").css({
      display: "none",
    });
  });
});

$(document).on("click", "svg.remove-product", function () {
  let product_id = $(this).data("id");
  $("#variation-table #body").find(`div.row-${product_id}`).remove();
  $("select[name=product_id]")
    .find(`option#product-${product_id}`)
    .attr("disabled", false);
});

$("select[name=province_id]").change(function () {
  let province_id = $(this).val();
  let districts = provinces.find((item) => item.id == province_id).districts;
  let str = "";
  districts.forEach((item) => {
    str += `<option value="${item.id}">${item.name}</option>`;
  });
  $("select[name=district_id]").html(
    `<option disabled selected>Quận/huyện</option>`
  );
  $("select[name=district_id]").append(str);
});

var is_uploading = false;
$("form#inventory").on("submit", function (event) {
  event.preventDefault();
  if (is_uploading) {
    return;
  }
  is_uploading = true;
  var formData = $(this).serialize();
  $("#overlay").css({
    display: "flex",
  });
  $.ajax({
    url: inventory_url,
    type: "POST",
    data: formData,
    success: function (response) {
      window.window.location.href = response.back_url;
    },
    error: function (err) {
      console.log(err);
      notif({
        msg: "Có lỗi xảy ra, vui lòng kiểm tra và thử lại",
        type: "warning",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 5000,
        animation: "slide",
      });
    },
  }).always(function () {
    is_uploading = false;
    $("#overlay").css({
      display: "none",
    });
  });
});

$("form#inventory-update").on("submit", function (event) {
  event.preventDefault();
  if (is_uploading) {
    return;
  }
  is_uploading = true;
  var formData = $(this).serialize();
  $("#overlay").css({
    display: "flex",
  });
  $.ajax({
    url: inventory_update_url,
    type: "POST",
    data: formData,
    success: function (response) {
      window.window.location.href = response.back_url;
    },
    error: function (xhr) {
      console.log(xhr);
      notif({
        msg: "Có lỗi xảy ra, vui lòng kiểm tra và thử lại",
        type: "warning",
        position: "center",
        height: "auto",
        top: 80,
        timeout: 5000,
        animation: "slide",
      });
    },
  }).always(function () {
    is_uploading = false;
    $("#overlay").css({
      display: "none",
    });
  });
});
