const host = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data";
axios.defaults.headers.common["Token"] = "7625ae34-2bac-11ee-b1d4-92b443b7a897";
axios.defaults.headers.common["Content-Type"] = "application/json";

var callAPI = (api) => {
  return axios.get(api).then((response) => {
    renderData(response.data.data.reverse(), "province");
  });
};

callAPI(host + "/province");

var callApiDistrict = (api) => {
  return axios.get(api).then((response) => {
    renderData(response.data.data.reverse(), "district");
  });
};

var callApiWard = (api) => {
  return axios.get(api).then((response) => {
    renderData(response.data.data.reverse(), "ward");
  });
};

var renderData = (array, select) => {
  let row = `<option disabled selected value="">${
    select === "district"
      ? "Chọn Quận/Huyện"
      : select === "ward"
      ? "Chọn Phường/Xã"
      : "Chọn Tỉnh/Thành"
  }</option>`;
  array.forEach((element) => {
    row += `<option data-id="${
      select === "province"
        ? element.ProvinceID
        : select === "district"
        ? element.DistrictID
        : element.WardCode
    }" value="${
      select === "province"
        ? element.ProvinceName
        : select === "district"
        ? element.DistrictName
        : element.WardName
    }">${
      select === "province"
        ? element.ProvinceName
        : select === "district"
        ? element.DistrictName
        : element.WardName
    }</option>`;
  });
  $("#" + select).html(row);
};

$("#province").change(() => {
  if (typeof check_out !== "undefined") {
    district_id = $("#district").find(":selected").data("id");
  }
  if (!$("form input[name=province_id]").length) {
    $("form").append(
      `<input name="province_id" value="${$("#province")
        .find(":selected")
        .data("id")}" type="hidden">`
    );
  } else {
    $("form input[name=province_id]").val(
      $("#province").find(":selected").data("id")
    );
  }
  $("#district").html(
    '<option disabled selected value="">Chọn Quận/Huyện</option>'
  );
  $("#ward").html('<option disabled selected value="">Chọn Phường/Xã</option>');
  callApiDistrict(
    host +
      "/district?province_id=" +
      $("#province").find(":selected").data("id")
  );
});

$("#district").change(() => {
  district_id = Number($("#district").find(":selected").data("id"));
  $("#ward").html('<option disabled selected value="">Chọn Phường/Xã</option>');
  if (!$("form input[name=district_id]").length) {
    $("form").append(
      `<input name="district_id" value="${$("#district")
        .find(":selected")
        .data("id")}" type="hidden">`
    );
  } else {
    $("form input[name=district_id]").val(
      $("#district").find(":selected").data("id")
    );
  }
  callApiWard(
    host + "/ward?district_id=" + $("#district").find(":selected").data("id")
  );
});

$("#ward").change(() => {
  ward_code = $("#ward").find(":selected").data("id");
  if (!$("form input[name=ward_code]").length) {
    $("form").append(
      `<input name="ward_code" value="${$("#ward")
        .find(":selected")
        .data("id")}" type="hidden">`
    );
  } else {
    $("form input[name=ward_code]").val(
      $("#ward").find(":selected").data("id")
    );
  }

  if (typeof check_out !== "undefined") {
    if (district_id && ward_code) {
      calc_shipping_fee(district_id, ward_code);
    }
  }
});

if (typeof load_provinces !== "undefined") {
  let district_id;
  let province_id;
  let ward_code;
  axios.defaults.headers.common["Token"] =
    "7625ae34-2bac-11ee-b1d4-92b443b7a897";
  axios.defaults.headers.common["Content-Type"] = "application/json";
  axios
    .get(
      "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province"
    )
    .then((response) => {
      let row = "";
      let provinces = response.data.data.reverse();
      for (let i = 0; i < provinces.length; i++) {
        row += `<option ${
          provinces[i].ProvinceID == add.province_id ? "selected" : ""
        } data-id="${provinces[i].ProvinceID}" value="${
          provinces[i].ProvinceName
        }">${provinces[i].ProvinceName}</option>`;
        if (provinces[i].ProvinceID == add.province_id) {
          province_id = provinces[i].ProvinceID;
          if (!$("form input[name=province_id]").length) {
            $("form").append(
              `<input name="province_id" value="${province_id}" type="hidden">`
            );
          } else {
            $("form input[name=province_id]").val(province_id);
          }
        }
      }
      $("#province").html(row);
      if (province_id) {
        axios
          .get(
            `https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district?province_id=${province_id}`
          )
          .then((response) => {
            row = "";
            let districts = response.data.data.reverse();
            for (let i = 0; i < districts.length; i++) {
              row += `<option ${
                districts[i].DistrictID == add.district_id ? "selected" : ""
              } data-id="${districts[i].DistrictID}" value="${
                districts[i].DistrictName
              }">${districts[i].DistrictName}</option>`;
              if (districts[i].DistrictID == add.district_id) {
                district_id = districts[i].DistrictID;
                if (!$("form input[name=district_id]").length) {
                  $("form").append(
                    `<input name="district_id" value="${district_id}" type="hidden">`
                  );
                } else {
                  $("form input[name=district_id]").val(district_id);
                }
              }
            }
            $("#district").html(row);
            if (district_id) {
              axios
                .get(
                  `https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id=${district_id}`
                )
                .then((response) => {
                  row = "";
                  let wards = response.data.data.reverse();
                  for (let i = 0; i < wards.length; i++) {
                    row += `<option ${
                      wards[i].WardCode == add.ward_code ? "selected" : ""
                    } data-id="${wards[i].WardCode}" value="${
                      wards[i].WardName
                    }">${wards[i].WardName}</option>`;
                    if (wards[i].WardCode == add.ward_code) {
                      ward_code = wards[i].WardCode;
                      if (!$("form input[name=ward_code]").length) {
                        $("form").append(
                          `<input name="ward_code" value="${ward_code}" type="hidden">`
                        );
                      } else {
                        $("#edit-address-form input[name=ward_code]").val(
                          ward_code
                        );
                      }
                    }
                  }
                  $("#ward").html(row);
                });
            }
          });
      }
      $("#addAddressModal").modal("show");
    });

  $(".btn-removeaddress").click(function () {
    let id = $(this).data("id");
  });
}
