var products = [];
var store_id = null;
var customer_id = null;
var cart = [];
$("select[name=selected-store]").change(function () {
  $("#overlay").css({
    display: "flex",
  });
  store_id = $(this).val();
  let store_name = $(this).find("option:selected").data("store-name");
  $(".store-container").prev("form#search-products").remove();
  cart = [];
  let result = constructCartItem();
  $(".table.cart tbody").html(result.str);
  $(".cart-total").remove();
  $(".create-bill-btn").remove();
  $.ajax({
    type: "GET",
    url: base_url + `admin/pos/store/${store_id}/products`,
    success: function (response) {
      products = response;
      $(".store-container").before(`<form id="search-products">
                <input class="form-control mb-3" type="text" placeholder="Tìm sản phẩm theo tên">
            </form>`);
      $(".store-container").html(constructStoreItems(products));
      if ($(".heading").find("span").length) {
        $(".heading").find("span").html(` | ${store_name}`);
      } else {
        $(".heading").append(`<span> | ${store_name}</span>`);
      }
    },
    error: function (xhr, status, error) {
      console.log(xhr);
    },
  }).always(function () {
    is_uploading = false;
    $("#overlay").css({
      display: "none",
    });
  });
});

function pickProduct(product) {
  $(".variant-popup")
    .html(
      `<p style="text-align:center;">${product.p_name}</p>
        ${
          product.hasOwnProperty("colors_sizes")
            ? `<div>
                    <ul class="product-variant mb-2 colors">
                        ${product.colors_sizes
                          .map(function (item) {
                            return `<li data-variant-type="color" data-color-id="${
                              item.id
                            }" data-color-name="${item.color_name}" onclick='pickVariant(${JSON.stringify(product)},${item.id},this)' class="color-item">${item.color_name}</li>`;
                          })
                          .join("")}
                    </ul>
                </div>`
            : product.hasOwnProperty("colors")
            ? `<div>
                    <ul class="product-variant mb-2 colors">
                        ${product.colors
                          .map(function (item) {
                            return `<li data-variant-type="color" data-color-id="${
                              item.id
                            }" data-color-name="${item.color_name}" onclick='pickVariant(${JSON.stringify(product)},${item.id},this)' class="color-item${product.stock_in_store.find((Item) => Item.color_id === item.id).stock <= 0 ? " disabled" : ""}">${item.color_name}</li>`;
                          })
                          .join("")}
                    </ul>
                </div>`
            : product.hasOwnProperty("sizes")
            ? `<div>
                    <ul class="product-variant mb-2 sizes">
                        ${product.sizes
                          .map(function (item) {
                            return `<li data-variant-type="size" data-color-id="${
                              item.id
                            }" data-size="${item.size}" onclick='pickVariant(${JSON.stringify(product)},${item.id},this)' class="size-item${product.stock_in_store.find((Item) => Item.size === item.size).stock <= 0 ? " disabled" : ""}">${item.size}</li>`;
                          })
                          .join("")}
                    </ul>
                </div>`
            : ""
        }
        ${
          product.hasOwnProperty("colors_sizes")
            ? `<div>
            <ul class="product-variant sizes">
                ${product.colors_sizes[0].sizes
                  .map(function (item) {
                    return `<li data-variant-type="size" onclick='pickVariant(${JSON.stringify(
                      product
                    )},"${item.size}",this)' class="size-item" data-size="${item.size}">${item.size}</li>`;
                  })
                  .join("")}
            </ul>
        </div>`
            : ""
        }
        <div style="text-align:right;margin-top:10px">
            <button onclick='confirm(this)' data-product='${JSON.stringify(
              product
            )}' class="btn btn-primary">Xác nhận</button>
            <button onclick="document.getElementsByClassName('variant-popup')[0].style.display = 'none'" class="btn btn-secondary">Bỏ</button>
        </div>`
    )
    .css({ display: "block" });
}

function pickVariant(product, color, element) {
  let variant_type = $(element).data("variant-type");
  if ($(element).hasClass("active")) {
    return;
  }
  if (variant_type == "color") {
    $(".product-variant.colors .color-item").removeClass("active");
  } else if (variant_type == "size") {
    $(".product-variant.sizes .size-item").removeClass("active");
  }
  $(element).addClass("active");
  let stocks = products.find((item) => item.id === product.id).stock_in_store;
  if (product.hasOwnProperty("colors_sizes")) {
    if (variant_type == "color") {
      $(".product-variant.sizes .size-item").each((index, item) => {
        let stock = stocks.find(
          (Item) =>
            Item.product_id == product.id &&
            Item.color_id == color &&
            Item.size == $(item).data("size")
        );
        if (stock.stock <= 0) {
          $(item).addClass("disabled");
          if ($(item).hasClass("active")) {
            $(item).removeClass("active");
          }
        } else {
          $(item).removeClass("disabled");
        }
      });
    } else if (variant_type == "size") {
      $(".product-variant.colors .color-item").each((index, item) => {
        let stock = stocks.find(
          (Item) =>
            Item.product_id == product.id &&
            Item.color_id == $(item).data("color-id") &&
            Item.size == $(element).data("size")
        );
        if (stock.stock <= 0) {
          if ($(item).hasClass("active")) {
            $(item).removeClass("active");
          }
          $(item).addClass("disabled");
        } else {
          $(item).removeClass("disabled");
        }
      });
    }
  }
}

function confirm(element) {
  let product = $(element).data("product");
  let color_id =
    $(".product-variant.colors .color-item.active").data("color-id") ?? null;
  let color_name =
    $(".product-variant.colors .color-item.active").data("color-name") ?? null;
  let size = $(".product-variant.sizes .size-item.active").data("size") ?? null;
  let price;
  let stock;
  let prod = products.find((item) => item.id == product.id);
  if (prod) {
    if (prod.hasOwnProperty("colors_sizes")) {
      if (!color_id || !size) {
        return alert("Vui lòng chọn màu sắc và kích cỡ !");
      }
      let color = prod.colors_sizes.find((item) => item.id == color_id);
      price = color.sizes.find((item) => item.size == size).price;
      stock = prod.stock_in_store.find(
        (Item) =>
          Item.product_id == product.id &&
          Item.color_id == color_id &&
          Item.size == size
      );
    } else if (prod.hasOwnProperty("colors")) {
      if (!color_id) {
        return alert("Vui lòng chọn màu sắc !");
      }
      let color = prod.colors.find((item) => item.id == color_id);
      price = color.price;
      stock = prod.stock_in_store.find(
        (Item) => Item.product_id == product.id && Item.color_id == color_id
      );
    } else if (prod.hasOwnProperty("sizes")) {
      if (!size) {
        return alert("Vui lòng chọn kích cỡ !");
      }
      let Size = prod.sizes.find((item) => item.size == size);
      price = Size.price;
      stock = prod.stock_in_store.find(
        (Item) => Item.product_id == product.id && Item.size == size
      );
    } else {
      price = prod.p_price;
      stock = prod.stock_in_store.find((Item) => Item.product_id == product.id);
    }
  }
  let ob = {
    product_id: product.id,
    product_name: product.p_name,
    color_id: color_id,
    color_name: color_name,
    size: size,
    price: price,
    quantity: 1,
    stock: stock.stock,
  };
  let item = cart.find(
    (item) =>
      item.product_id == ob.product_id &&
      item.color_id == ob.color_id &&
      item.size == ob.size
  );
  if (item) {
    item.quantity += 1;
  } else {
    cart.push(ob);
  }
  let result = constructCartItem();
  $(".table.cart tbody").html(result.str);
  if (!$(".table-wrapper").next().filter(".cart-total").length) {
    $(".table-wrapper")
      .after(`<div class="d-flex justify-content-between cart-total mb-2">
                <div style="font-weight:600">Tổng tiền</div>
                <div class="mr-2 total">${new Intl.NumberFormat({
                  style: "currency",
                }).format(result.total)}đ</div>
            </div>`);
    $(".cart-total").after(` <div>
                <button class="btn btn-primary w-100 create-bill-btn">Xuất hóa đơn</button>
            </div>`);
  } else {
    $(".cart-total .total").text(
      `${new Intl.NumberFormat({
        style: "currency",
      }).format(result.total)}đ`
    );
  }
  $(".variant-popup").css({ display: "none" });
  makeEqualHeight();
}

function updateQuantity(input, product_id, color, size) {
  let quantity = $(input).val();
  if (Number(quantity) === 0) {
    return deleteItem(product_id, color, size);
  }
  let prod = cart.find(
    (item) =>
      item.product_id == product_id &&
      item.color_id == color &&
      item.size == size
  );
  prod.quantity = Number(quantity);
  let result = constructCartItem();
  $(".table.cart tbody").html(result.str);
  $(".cart-total .total").text(
    `${new Intl.NumberFormat({
      style: "currency",
    }).format(result.total)}đ`
  );
}

function deleteItem(product_id, color, size) {
  cart = cart.filter((item) => {
    if (
      item.product_id == product_id &&
      item.color_id == color &&
      item.size == size
    ) {
      return false;
    }
    return true;
  });
  let result = constructCartItem();
  $(".table.cart tbody").html(result.str);
  if (cart.length === 0) {
    $(".cart-total").remove();
    $(".create-bill-btn").remove();
  } else {
    $(".cart-total .total").text(
      `${new Intl.NumberFormat({
        style: "currency",
      }).format(result.total)}đ`
    );
  }
  makeEqualHeight();
}

function constructCartItem() {
  let str = "";
  let total = 0;
  cart.forEach((item) => {
    let variant = "";
    if (item.color_name) {
      variant += item.color_name;
      if (item.size) {
        variant += " - " + item.size;
      }
    } else if (item.size) {
      variant += item.size;
    }
    total += item.price * item.quantity;
    str += `<tr>
                    <td style="font-size:13px">
                        <div>${item.product_name}</div>
                        <div>${variant} | <span>Còn <span style="color:#af2522;font-weight:600">${
      item.stock
    }</span> sản phẩm</span></div>
                    </td>
                    <td>${new Intl.NumberFormat({
                      style: "currency",
                    }).format(item.price)}đ</td>
                    <td style="text-align:center">
                        <input onchange="updateQuantity(this,'${
                          item.product_id
                        }',${item.color_id},${
      item.size ? "'" + item.size + "'" : item.size
    })" style="width:50px" type="number" value="${item.quantity}" />
                    </td>
                    <td style="text-align:right;position:relative">${new Intl.NumberFormat(
                      {
                        style: "currency",
                      }
                    ).format(item.price * item.quantity)}đ
                      <i onclick="deleteItem('${item.product_id}',${
      item.color_id
    },${
      item.size ? "'" + item.size + "'" : item.size
    })" class="fas fa-trash-can"></i>
                    </td>
                </tr>`;
  });
  return { str, total };
}

function constructStoreItems(products) {
  let str = "";
  products.forEach((element) => {
    str += `<div class="col mb-3">
                  <div style="cursor:pointer" class="collection-item" onclick='pickProduct(${JSON.stringify(
                    element
                  )})'>
                      <img width="187.5" height="187.5" loading="lazy" src="${
                        element.images[0]
                      }" style="display: inline;">
                      <div style="font-size:13px;margin-top:5px">${
                        element.p_name
                      }</div>
                      <div class="price">
                          <div>
                              <span class="num">${new Intl.NumberFormat({
                                style: "currency",
                              }).format(
                                element.p_price
                              )}</span><span class="currency">đ</span>
                          </div>
                      </div>
                  </div>
                </div>`;
  });
  return str;
}

var search_timeout = null;

$(document).on("keyup", "form#search-products input", function () {
  if (search_timeout) {
    clearTimeout(search_timeout);
  }
  search_timeout = setTimeout(() => {
    let key_word = $(this).val();
    if (key_word.length === 0) {
      $(".store-container").html(constructStoreItems(products));
      return;
    }
    let searched_products = products.filter((item) => {
      return item.p_name.toLowerCase().includes(key_word.toLowerCase());
    });
    if (searched_products.length > 0) {
      if (!$(".store-container").hasClass("row-cols-4")) {
        $(".store-container").addClass("row-cols-4");
      }
      $(".store-container").html(constructStoreItems(searched_products));
    } else {
      $(".store-container").removeClass("row-cols-4");
      $(".store-container").html(`<div>Không tìm thấy sản phẩm nào !</div>`);
    }
  }, 300);
});

var is_uploading = false;
$(document).on("click", ".create-bill-btn", function () {
  if (is_uploading) {
    return;
  }
  is_uploading = true;
  $("#overlay").css({
    display: "flex",
  });
  const formData = new FormData();
  let total = 0;
  formData.append("store", store_id);
  if (!customer_id) {
    formData.append("customer_name", $("input#customer-name").val());
    formData.append("customer_phone", $("input#customer-phone").val());
  } else {
    formData.append("customer", customer_id);
  }
  formData.append("employee", employee_id);
  cart.forEach((item, index) => {
    formData.append("products[]", item.product_id);
    formData.append("prices[]", item.price);
    formData.append("quantities[]", item.quantity);
    if (item.color_id) {
      formData.append(`colors[${index}]`, item.color_id);
      formData.append(`color_names[${index}]`, item.color_name);
    }
    if (item.size) {
      formData.append(`sizes[${index}]`, item.size);
    }
    formData.append("product_names[]", item.product_name);
    total += item.price * item.quantity;
  });
  formData.append("total_amount", total);
  formData.append("csrf_token", csrf_token);
  $.ajax({
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    url: base_url + `admin/pos/sale/create`,
    success: function (response) {
      cart.forEach((cart_item) => {
        let product = products.find(
          (product_item) => product_item.id == cart_item.product_id
        );
        let stock = product.stock_in_store.find((stock_item) => {
          return (
            stock_item.color_id == cart_item.color_id &&
            stock_item.size == cart_item.size
          );
        });
        stock.quantity -= cart_item.quantity;
      });
      cart = [];
      let result = constructCartItem();
      $(".table.cart tbody").html(result.str);
      $(".cart-total").remove();
      $(".create-bill-btn").remove();
      $("input#customer-phone").val("");
      $("#customer-info").html("");
      $(".store-container").css("height", 300);
      const screenWidth = window.screen.width;
      const screenHeight = window.screen.height;
      const left = (screenWidth - 600) / 2;
      window.open(
        response.bill,
        "Invoice",
        `width=600,height=${screenHeight},left=${left},top=0`
      );
    },
    error: function (xhr, status, error) {
      console.log(xhr);
      let res = xhr.responseJSON;
      let msg = "";
      if (res.hasOwnProperty("errors")) {
        if (res.errors?.customer_phone?.regex) {
          msg = "Số điện thoại không hợp lệ";
        } else {
          msg = "Vui lòng nhập thông tin khách hàng";
        }
      } else if (res.hasOwnProperty("message")) {
        msg = res.message;
      }
      notif({
        msg: msg,
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

$("#get-customer").click(function () {
  $("#overlay").css({
    display: "flex",
  });
  $.ajax({
    type: "GET",
    data: {
      phone: $("input#customer-phone").val(),
    },
    url: base_url + `admin/customer/fetch`,
    success: function (response) {
      customer_id = response.customer.id;
      $("#customer-info").html(`<div>
                    Khách hàng: <span style="font-weight:600">${response.customer.name}</span>
                </div>`);
    },
    error: function (xhr, status) {
      let res = xhr.responseJSON;
      let msg = "";
      if (res.hasOwnProperty("errors")) {
        if (res.errors.phone.required) {
          msg = "Vui lòng nhập số điện thoại";
        } else {
          msg = "Số điện thoại không hợp lệ";
        }
        if (msg.length != "") {
          notif({
            msg: msg,
            type: "warning",
            position: "center",
            height: "auto",
            top: 80,
            timeout: 5000,
            animation: "slide",
          });
        }
      }
      if (xhr.status == 404) {
        customer_id = null;
        $("#customer-info").html(`<div>
                  <input type="text" id="customer-name" placeholder="Nhập tên khách hàng" class="form-control">
                </div>`);
      }
    },
  }).always(function () {
    is_uploading = false;
    $("#overlay").css({
      display: "none",
    });
  });
});

function makeEqualHeight() {
  let left_height = $("#left-column").height();
  $(".store-container").css("height", left_height - 70);
}
