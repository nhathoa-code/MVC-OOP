import { uniqueID } from "./helper.js";

if (typeof chart_edit !== "undefined") {
  // loading sizes
  let size_inputs = "";
  let rows = "";
  sizes.forEach((item) => {
    size_inputs += `
      <div id="size-${item.id}" class="col-2 mb-3 size-input">
        <div class="d-flex align-items-center">
        <input data-id="${item.id}" value="${item.name}" style="padding: 10px;height:30px" type="text" class="form-control">
        <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
        </svg>
        <svg data-id="${item.id}" class="delete-size-variation" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
        </svg>
        </div>
      </div>
    `;
    rows += `
      <div id="size-row-${item.id}" data-size_id="${item.id}" class="d-flex p-2">
        <div style="width:150px;"><span>${item.name}</span></div>
    `;
    item.measurements.forEach((item) => {
      rows += `<div style="width: 150px;">
                <input data-measurement_id="${item.id}" value="${item.value}" style="width: 100%;" type="text" class="form-control measurement">
            </div>`;
    });
    rows += "</div>";
  });
  $("#size-variation-inputs").prepend(size_inputs);
  $("#size-chart").append(`
        <div id="header" class="d-flex p-2">
            <div style="width:150px;font-weight:bold">Kích thước</div>
        </div>
    `);

  // loading measurements
  let measurement_inputs = "";
  measurements.forEach((item) => {
    $("#size-chart #header").append(`
        <div id="measurement-header-${item.id}" style="width: 150px;"><span>${item.name}<span></div>
    `);
    measurement_inputs += `
        <div id="measurement-${item.id}" class="col-2 mb-3 size-input">
          <div class="d-flex align-items-center">
          <input data-id="${item.id}" value="${item.name}" style="padding: 10px;height:30px" type="text" class="form-control">
          <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
          </svg>
          <svg data-id="${item.id}" class="delete-measurement-variation" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
              <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
          </svg>
          </div>
        </div>
    `;
  });
  $("#measurement-variation-inputs").prepend(measurement_inputs);
  $("#size-chart").append(rows);
}

$("#size-variation-inputs").sortable({
  cursor: "move",
  cancel: "#add-size-input,input,.delete-size-variation",
  update: function (event, ui) {
    var sortedIDs = $(this).sortable("toArray");
    sortedIDs = sortedIDs.filter((item) => item !== "");
    for (var i = 0; i < sortedIDs.length; i++) {
      var correspondingElem = $("#variation-table #size-chart").find(
        `#${sortedIDs[i].replace("-", "-row-")}`
      );
      $("#variation-table #size-chart").append(correspondingElem);
    }
    // // reorder sizes
    var newOrder = [];
    sortedIDs.forEach((item) => {});
    $(`#variation-table #size-chart > div[data-size_id]`).each(function () {
      var size_id = $(this).data("size_id");
      let size = sizes.find((item) => item.id === size_id);
      if (size) {
        newOrder.push(size);
      }
    });
    sizes = newOrder;
  },
});

$("#measurement-variation-inputs").sortable({
  cursor: "move",
  cancel: "#add-measurement-input,input,.delete-measurement-variation",
  update: function (event, ui) {
    var sortedIDs = $(this).sortable("toArray");
    sortedIDs = sortedIDs.filter((item) => item !== "");
    let sortedMeasurements = [];
    for (let i = 0; i < sortedIDs.length; i++) {
      let id = sortedIDs[i].split("-")[1];
      sortedMeasurements.push(measurements.find((item) => item.id == id));
      let headerElement = $("#size-chart #header").find(
        `div#measurement-header-${id}`
      );
      $(`#size-chart #header`).append(headerElement);
    }
    measurements = sortedMeasurements;
    $("#variation-table #size-chart > div[data-size_id]").each(
      (index, item) => {
        for (let i = 0; i < sortedIDs.length; i++) {
          let id = sortedIDs[i].split("-")[1];
          let correspondingElem = $(item).find(
            `div:has(input[data-measurement_id=${id}])`
          );
          $(item).append(correspondingElem);
        }
      }
    );
    // sort measurements in sizes
    sizes = sizes.map((size) => {
      let sortedMeasurements = [];
      for (let i = 0; i < sortedIDs.length; i++) {
        let id = sortedIDs[i].split("-")[1];
        sortedMeasurements.push(
          size.measurements.find((item) => item.id == id)
        );
      }
      size.measurements = sortedMeasurements;
      return size;
    });
  },
});

$(document).on("click", "#add-size-input", function () {
  let unique_id = uniqueID();
  let size = { id: unique_id, name: "", measurements: [] };
  if (measurements.length > 0) {
    addMeasurements(size);
  }
  sizes.push(size);
  $(
    this
  ).parent().before(`<div id="size-${unique_id}" class="col-2 mb-3 size-input">
                        <div class="d-flex align-items-center">
                        <input data-id="${unique_id}" style="padding: 10px;height:30px" type="text" class="form-control">
                        <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
                        </svg>
                        <svg data-id="${unique_id}" class="delete-size-variation" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                        </svg>
                        </div>
                    </div>`);
  let row = `
            <div id="size-row-${unique_id}" data-size_id="${unique_id}" class="d-flex p-2">
                <div style="width:150px;">
                    <span></span>
                </div>
            
    `;
  measurements.forEach((item) => {
    row += `<div style="width: 150px;">
                <input data-measurement_id="${item.id}" style="width: 100%;" type="text" class="form-control measurement">
            </div>`;
  });
  row += "</div>";
  if (!is_size_active) {
    $("#size-chart").append(`
        <div id="header" class="d-flex p-2">
            <div style="width:150px;font-weight:bold">Kích thước</div>
        </div>
    `);
    is_size_active = true;
  }
  $("#size-chart").append(row);
  if ($("#add-measurement-input").hasClass("cursor-disabled")) {
    $("#add-measurement-input").removeClass("cursor-disabled");
  }
});

$(document).on("click", "#add-measurement-input", function () {
  if (sizes.length === 0) {
    return;
  }
  let unique_id = uniqueID();
  let measurement = { id: unique_id, name: "" };
  measurements.push(measurement);
  addToSizes(measurement);
  $(
    this
  ).parent().before(`<div id="measurement-${unique_id}" class="col-2 mb-3 size-input">
                <div class="d-flex align-items-center">
                <input data-id="${unique_id}" style="padding: 10px;height:30px" type="text" class="form-control">
                <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
                </svg>
                <svg data-id="${unique_id}" class="delete-measurement-variation" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                </svg>
                </div>
            </div>`);

  $("#size-chart #header").append(`
        <div id="measurement-header-${unique_id}" style="width: 150px;"><span><span></div>
    `);
  sizes.forEach((item) => {
    $(`#size-row-${item.id}`).append(`
            <div style="width: 150px;">
                <input data-measurement_id="${unique_id}" style="width: 100%;" type="text" class="form-control measurement">
            </div>
        `);
  });
});

// handle inputs change
$(document).on("input", "#size-variation input", function () {
  let id = $(this).data("id");
  let size = sizes.find((item) => item.id === id);
  size.name = $(this).val();
  $(`#size-row-${id} span`).text($(this).val());
});

$(document).on("input", "#measurement-variation input", function () {
  let id = $(this).data("id");
  let measurement = measurements.find((item) => item.id === id);
  measurement.name = $(this).val();
  $(`#measurement-header-${id} span`).text($(this).val());
  sizes = sizes.map((size) => {
    let measurement = size.measurements.find((item) => item.id === id);
    measurement.name = $(this).val();
    return size;
  });
});

// handle measurement input change
$(document).on("input", "input.measurement", function () {
  let size_id = $(this).parent().parent().data("size_id");
  let size = sizes.find((item) => item.id === size_id);
  let measurement_id = $(this).data("measurement_id");
  let measurement = size.measurements.find(
    (item) => item.id === measurement_id
  );
  measurement.value = $(this).val();
});

// handle delete variation
$(document).on("click", "svg.delete-size-variation", function () {
  if (sizes.length === 1) {
    return;
  }
  let id = $(this).data("id");
  sizes = sizes.filter((item) => item.id !== id);
  $(this).parent().parent().remove();
  $(`#size-row-${id}`).remove();
});

$(document).on("click", "svg.delete-measurement-variation", function () {
  if (measurements.length === 1) {
    return;
  }
  let id = $(this).data("id");
  measurements = measurements.filter((item) => item.id !== id);
  sizes = sizes.map((item) => {
    item.measurements = item.measurements.filter((item) => item.id !== id);
    return item;
  });
  $(this).parent().parent().remove();
  $(`#size-chart #header div#measurement-header-${id}`).remove();
  sizes.forEach((item) => {
    $(`#size-row-${item.id} input[data-measurement_id=${id}]`)
      .parent()
      .remove();
  });
});

function addMeasurements(size) {
  measurements.forEach((item) => {
    size.measurements.push({
      id: item.id,
      name: item.name,
      value: "",
    });
  });
}

function addToSizes(measurement) {
  sizes = sizes.map((item) => {
    item.measurements.push({
      id: measurement.id,
      name: "",
      value: "",
    });
    return item;
  });
}

$("#save-chart").click(function () {
  if (is_uploading) {
    return;
  }
  if (sizes.length === 0 || measurements.length === 0) {
    return;
  }
  let validated = true;
  for (let i = 0; i < sizes.length; i++) {
    if (sizes[i].name === "") {
      validated = false;
      break;
    }
    for (let y = 0; y < sizes[i].measurements.length; y++) {
      let measurement = sizes[i].measurements[y];
      if (measurement.name === "" || measurement.value === "") {
        validated = false;
        break;
      }
    }
  }
  if (
    !$("input[name=size_chart_name]").length ||
    $("input[name=size_chart_name]").val() === ""
  ) {
    validated = false;
  }
  if (!validated) {
    notif({
      msg: "Vui lòng nhập đầy đủ thông tin!",
      type: "warning",
      position: "center",
      height: "auto",
      top: 80,
      timeout: 5000,
      animation: "slide",
    });
    return;
  }
  $("#overlay").css({
    display: "flex",
  });
  is_uploading = true;
  const formData = new FormData();
  formData.append("name", $("input[name=size_chart_name]").val());
  formData.append("size-chart", JSON.stringify(sizes));
  formData.append("csrf_token", csrf_token);
  $.ajax({
    type: "POST",
    url: save_size_chart_url,
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      if (response.hasOwnProperty("back")) {
        return (window.location.href = response.back);
      }
      if (response.hasOwnProperty("message")) {
        notif({
          msg: response.message,
          type: "success",
          position: "center",
          height: "auto",
          top: 80,
          timeout: 5000,
          animation: "slide",
        });
      }
    },
    error: function (xhr, status, error) {
      if (xhr.responseJSON?.errors?.name?.unique) {
        notif({
          msg: "Tên bảng kích cỡ đã tồn tại, vui lòng chọn tên khác",
          type: "warning",
          position: "center",
          height: "auto",
          top: 80,
          timeout: 5000,
          animation: "slide",
        });
      }
    },
  }).always(() => {
    $("#overlay").css({
      display: "none",
    });
    is_uploading = false;
  });
});
