var is_color_variation_active = false;
var is_size_variation_active = false;
var p_images = [];
var p_images_to_delete = [];
var colors_to_delete = [];
var colors = [];
var sizes = [];
var initial = true;
var is_updating = false;

function clearPicked(categoryList) {
  return categoryList.map((item) => {
    if (item.hasOwnProperty("picked")) {
      delete item.picked;
      if (item.hasOwnProperty("children")) {
        item.children = clearPicked(item.children);
      }
    }
    return item;
  });
}

function displayCategories(categoryList, container) {
  const categoriesContainer = $(`<ul class="list-group"></ul>`);
  categoryList.forEach((category) => {
    const liElement = $(`<li class="list-group-item"></li>`);
    if (category.hasOwnProperty("children")) {
      liElement.append(`${category.cat_name} 
                          <span class="icon">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M23.5 15.5l-12-11c-.6-.6-1.5-.6-2.1 0-.2.2-.4.6-.4 1s.2.8.4 1l10.9 10-10.9 10c-.6.6-.6 1.5 0 2.1.3.3.7.4 1 .4.4 0 .7-.1 1-.4l11.9-10.9.1-.1c.3-.3.4-.7.4-1.1.1-.4 0-.8-.3-1z"></path></svg>
                          </span>`);
    } else {
      liElement.append(`${category.cat_name}`);
    }
    liElement.on("click", () => {
      categoryList = categoryList.map((item) => {
        if (item.hasOwnProperty("picked")) {
          delete item.picked;
          if (item.hasOwnProperty("children")) {
            item.children = clearPicked(item.children);
          }
        }
        return item;
      });
      if (!category.hasOwnProperty("picked")) {
        category.picked = true;
      }
      liElement.siblings().removeClass("picked");
      liElement.addClass("picked");
      liElement.parent().nextAll().remove();
      if (category.hasOwnProperty("children")) {
        displayCategories(category.children, container);
      }
      $("#picked-categories").text(buildPickedCategories(categories));
    });
    categoriesContainer.append(liElement);
    // --------------- for edit --------------//
    if (initial) {
      if (picked_categories.includes(category.id)) {
        liElement.addClass("picked");
        category.picked = true;
      }
    }
    // --------------- for edit --------------//
  });
  if (container.find("ul.list-group").length > 0) {
    container.append(`<div class="separator"></div>`);
  }
  container.append(categoriesContainer);
  // --------------- for edit --------------//
  if (initial) {
    let pickedCategory = categoryList.find((item) =>
      item.hasOwnProperty("picked")
    );

    if (pickedCategory && pickedCategory.hasOwnProperty("children")) {
      displayCategories(pickedCategory.children, container);
    }
  }
  // --------------- for edit --------------//
}

displayCategories(categories, $("#categories-container"));
initial = false;

$("#picked-categories-label").text(buildPickedCategories(categories));

function buildPickedCategories(categories, categoryNamesArr = []) {
  categories.forEach((item) => {
    if (item.hasOwnProperty("picked")) {
      categoryNamesArr.push(item.cat_name);
      if (item.hasOwnProperty("children")) {
        buildPickedCategories(item.children, categoryNamesArr);
      }
    }
  });
  return categoryNamesArr.join(" > ");
}

// ------------- set up data -------------- //

urlsToFiles(image_urls).then((files) => {
  for (let i = 0; i < files.length; i++) {
    let id = uniqueID();
    p_images.push({ id: id, file: files[i], status: "old" });
    var reader = new FileReader();
    reader.onload = function (e) {
      $("#upload-icon-wrap").before(
        `<div class="item">
            <div class="img-wrap image-container">
                <img data-id="${id}" src="${e.target.result}" alt="">
                <div class="image-delete">
                    <i data-name="${files[i].name}" data-id="${id}" class="delete-p-image fa fa-solid fa-trash"></i>
                </div>
            </div>
        </div>`
      );
    };
    reader.readAsDataURL(files[i]);
  }
});

if (
  typeof colors_sizes_from_db !== "undefined" ||
  typeof colors_from_db !== "undefined" ||
  typeof sizes_from_db !== "undefined"
) {
  if (typeof colors_sizes_from_db !== "undefined") {
    is_color_variation_active = true;
    is_size_variation_active = true;
    $("#add-size-variation").css({
      cursor: "not-allowed",
    });
    $("#add-color-variation").css({
      cursor: "not-allowed",
    });
  } else if (typeof colors_from_db !== "undefined") {
    is_color_variation_active = true;
    $("#add-color-variation").css({
      cursor: "not-allowed",
    });
  } else if (typeof sizes_from_db !== "undefined") {
    is_size_variation_active = true;
    $("#add-size-variation").css({
      cursor: "not-allowed",
    });
  }
  if (
    typeof colors_sizes_from_db !== "undefined" ||
    typeof sizes_from_db !== "undefined"
  ) {
    $("#size-variation-inputs").sortable({
      cursor: "move",
      cancel: "#add-size-input,input,.delete-size-variation",
      update: function (event, ui) {
        let sortedIDs = $(this).sortable("toArray");
        sortedIDs = sortedIDs.filter((item) => item !== "");
        sortedArray = [];
        if (is_color_variation_active) {
          for (let i = 0; i < sortedIDs.length; i++) {
            let id = sortedIDs[i].split("-")[1];
            sortedArray.push(sizes.find((item) => item.id == id));
          }
          sizes = sortedArray;
          $("#variation-table #body > div.row").each((index, item) => {
            for (let i = 0; i < sortedIDs.length; i++) {
              let correspondingElem = $(item).find(
                `div.${sortedIDs[i].replace("size", "size-cell")}`
              );
              let correspondingElemPrice = $(item).find(
                `div.${sortedIDs[i].replace("size", "size-price")}`
              );
              let correspondingElemInventory = $(item).find(
                `div.${sortedIDs[i].replace("size", "size-inventory")}`
              );
              $(item).find("div.size-cell").append(correspondingElem);
              $(item).find("div.price-cell").append(correspondingElemPrice);
              $(item)
                .find("div.inventory-cell")
                .append(correspondingElemInventory);
            }
          });
          // sort sizes in colors
          colors = colors.map((color) => {
            let sortedSizes = [];
            for (let i = 0; i < sortedIDs.length; i++) {
              let id = sortedIDs[i].split("-")[1];
              sortedSizes.push(color.sizes.find((item) => item.id == id));
            }
            color.sizes = sortedSizes;
            return color;
          });
        } else {
          let sortedSizes = [];
          for (let i = 0; i < sortedIDs.length; i++) {
            let size_id = sortedIDs[i].split("-")[1];
            sortedSizes.push(sizes.find((item) => item.id == size_id));
            let correspondingElem = $("#variation-table").find(
              `#${sortedIDs[i].replace("size", "variation-row")}`
            );
            $("#variation-table #body").append(correspondingElem);
          }
          sizes = sortedSizes;
        }
      },
    });
  }

  if (
    typeof colors_sizes_from_db !== "undefined" ||
    typeof colors_from_db !== "undefined"
  ) {
    $("#color-variation-inputs").sortable({
      cursor: "move",
      cancel: "#add-color-input,input,.delete-color-variation",
      update: function (event, ui) {
        var sortedIDs = $(this).sortable("toArray");
        sortedIDs = sortedIDs.filter((item) => item !== "");
        for (var i = 0; i < sortedIDs.length; i++) {
          var correspondingElem = $("#variation-table").find(
            `#${sortedIDs[i].replace("color", "variation-row")}`
          );
          $("#variation-table #body").append(correspondingElem);
        }
        // reorder colors
        var newOrder = [];
        $("#variation-table #body > div.row").each(function () {
          var color_id = $(this).data("color_id");
          let color = colors.find((item) => item.id === color_id);
          if (color) {
            newOrder.push(color);
          }
        });
        colors = newOrder;
      },
    });
  }
  if (
    typeof colors_sizes_from_db !== "undefined" ||
    typeof sizes_from_db !== "undefined"
  ) {
    if (typeof colors_sizes_from_db !== "undefined") {
      colors_sizes_from_db[0].sizes.forEach((item) => {
        let id = uniqueID();
        sizes.push({ id: id, value: item.size });
      });
    } else if (typeof sizes_from_db !== "undefined") {
      sizes_from_db.forEach((item) => {
        let id = uniqueID();
        sizes.push({
          id: id,
          value: item.value,
          price: item.price,
          stock: item.stock,
        });
      });
    }
    sizes.forEach((item) => {
      $("#add-size-input").parent()
        .before(`<div id="size-${item.id}" class="col-4 mb-3 size-input">
                    <div class="d-flex align-items-center">
                    <input data-id="${item.id}" value="${item.value}" style="padding: 10px;height:30px" type="text" class="form-control">
                    <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
                    </svg>
                    <svg data-id="${item.id}" class="delete-size-variation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                    </svg>
                    </div>
                </div>`);
    });
  }
  // for colors and/or sizes
  if (
    typeof colors_sizes_from_db !== "undefined" ||
    typeof colors_from_db !== "undefined"
  ) {
    async function processArray(input_array) {
      let output_array = [];
      for (const item of input_array) {
        let file_ob = await imageUrlToFile(item.color);
        let id = uniqueID();
        let gallery = [];
        let color_sizes = [];
        for (const image_url of item.gallery) {
          let id = uniqueID();
          let file_ob = await imageUrlToFile(image_url);
          gallery.push({ id: id, file: file_ob, status: "old" });
        }
        let color_ob = {
          color_id: item.id,
          path: item.color_path,
          gallery_dir: item.gallery_dir,
          id: id,
          color: file_ob,
          status: "old",
          value: item.name,
          gallery: gallery,
          gallery_images_to_delete: [],
        };
        if (
          item.hasOwnProperty("sizes") &&
          typeof colors_sizes_from_db !== "undefined"
        ) {
          item.sizes.forEach((item, index) => {
            color_sizes.push({
              ...sizes[index],
              stock: item.stock,
              price: item.price,
            });
          });
          color_ob.sizes = color_sizes;
        } else {
          color_ob.price = item.price;
          color_ob.stock = item.stock;
        }
        output_array.push(color_ob);
      }
      return output_array;
    }
    let input_array;
    if (typeof colors_sizes_from_db !== "undefined") {
      input_array = colors_sizes_from_db;
    } else if (typeof colors_from_db !== "undefined") {
      input_array = colors_from_db;
    }
    $("#overlay").css({
      display: "flex",
    });
    processArray(input_array).then((res) => {
      colors = res;
      res.forEach((item) => {
        $("#add-color-input").parent()
          .before(`<div id="color-${item.id}" class="col-4 mb-3 color-input">
                      <div class="d-flex align-items-center">
                      <input data-id="${item.id}" value="${item.value}" style="padding: 10px;height:30px" type="text" class="form-control">
                      <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
                      </svg>
                      <svg data-id="${item.id}" class="delete-color-variation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                          <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                      </svg>
                      </div>
                  </div>`);
        $("#variation-table #body").append(`<div data-color_id="${
          item.id
        }" id="variation-row-${item.id}" class="row w-100 mr-0 ml-0">
                            <div class="col-2 cell color-cell">
                                <div class="d-inline-flex flex-column align-items-center">
                                    <input data-id="${
                                      item.id
                                    }" class="color-image" style="display:none" id="color-image-${
          item.id
        }" type="file" name="color_image[]">
                                    <div class="color-upload-icon-wrap">
                                        <div style="border-radius: 4px;width:50px;height:50px;margin-right:0" data-id="${
                                          item.id
                                        }" class="color-image-upload image-upload">
                                            <div class="image-upload-icon" style="width: 25px;height:25px">
                                                <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <span style="margin-top:5px">${
                                      item.value
                                    }</span>
                                </div>
                            </div>
                            ${
                              is_size_variation_active
                                ? `<div class="col-2 cell size-cell">
                                ${item.sizes
                                  .map(
                                    (item) =>
                                      `<div class="row-cell size-cell-${item.id}">${item.value}</div>`
                                  )
                                  .join("")}
                                </div>
                                <div class="col-2 cell price-cell">
                                ${item.sizes
                                  .map(
                                    (item) => `
                                          <div class="row-cell size-price-${item.id}">
                                            <input data-size_id="${item.id}" value="${item.price}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
                                          </div>`
                                  )
                                  .join("")}
                                </div>
                                <div class="col-2 cell inventory-cell">
                                ${item.sizes
                                  .map(
                                    (item) => `
                                          <div class="row-cell size-inventory-${item.id}">
                                            <input data-size_id="${item.id}" value="${item.stock}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
                                          </div>`
                                  )
                                  .join("")}
                                </div>`
                                : `
                                    <div class="col-2 cell price-cell">
                                      <div class="row-cell">
                                          <input
                                              style="padding: 10px; height: 30px; width: 100%"
                                              value="${item.price}"
                                              type="text"
                                              class="form-control price"
                                          />
                                      </div>
                                    </div>
                                    <div class="col-2 cell inventory-cell">
                                      <div class="row-cell">
                                          <input style="padding: 10px;height:30px;width:100%" value="${item.stock}" type="text" class="form-control inventory">
                                      </div>
                                   </div>`
                            }
                            <div class="col cell color-gallery-cell">
                                <div class="d-inline-flex align-items-center">
                                    <input data-id="${
                                      item.id
                                    }" style="display:none" class="color-gallery" id="color-gallery-${
          item.id
        }" type="file" name="color_gallery[]" multiple>
                                    <div data-color_id="${
                                      item.id
                                    }" id="color-gallery-preview-${
          item.id
        }" class="color-gallery-preview">
                                       
                                        <div class="upload-icon-wrap">
                                            <div data-id="${
                                              item.id
                                            }" style="border-radius: 4px;width:50px;height:50px;margin-right:0" class="color-gallery-upload image-upload">
                                                <div class="image-upload-icon" style="width: 25px;height:25px">
                                                    <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> `);
        // set up color image
        if (item.color) {
          const reader = new FileReader();
          reader.onload = function (e) {
            $(`#color-image-${item.id}`).next(".color-upload-icon-wrap")
              .replaceWith(`<div class="image-container color-image-container" style="width:50px;height:50px">
                            <img style="width: 100%;height:100%" src="${e.target.result}" alt="">
                            <div style="display:none" class="image-delete color-image-delete">
                                <i data-id="${item.id}" class="fa fa-solid fa-trash"></i>
                            </div>
                        </div>`);
          };
          reader.readAsDataURL(item.color);
        }

        // set up color gallery
        for (let i = 0; i < item.gallery.length; i++) {
          const reader = new FileReader();
          reader.onload = function (e) {
            $(`#color-gallery-preview-${item.id} .upload-icon-wrap`).before(
              `<div class="item">
            <div class="item-wrap image-container">
                <img data-color_gallery_image_id="${item.gallery[i].id}" src="${e.target.result}" alt="">
                <div class="image-delete">
                    <i data-name="${item.gallery[i].file.name}" data-id="${item.gallery[i].id}" data-color_id="${item.id}" class="delete-color-gallery-image fa fa-solid fa-trash"></i>
                </div>
            </div>
        </div>`
            );
          };
          reader.readAsDataURL(item.gallery[i].file);
        }
        $(".color-gallery-preview").sortable({
          cursor: "move",
          update: function (event, ui) {
            let color_id = $(this).data("color_id");
            let color = colors.find((item) => item.id === color_id);
            let newOrder = [];
            $(this)
              .find("img")
              .each(function () {
                let color_gallery_image_id = $(this).data(
                  "color_gallery_image_id"
                );
                let color_gallery_image = color.gallery.find(
                  (item) => item.id === color_gallery_image_id
                );
                if (color_gallery_image) {
                  newOrder.push(color_gallery_image);
                }
              });
            color.gallery = newOrder;
          },
        });
      });
      $("#overlay").css({
        display: "none",
      });
    });
  }

  // for sizes
  if (typeof sizes_from_db !== "undefined") {
    sizes.forEach((item) => {
      $("#variation-table #body")
        .append(`<div data-size_id="${item.id}" id="variation-row-${item.id}" class="row w-100 mr-0 ml-0">
                    <div class="col-3 cell size-cell">
                      <div class="row-cell size-cell-${item.id}">${item.value}</div>
                    </div>
                    <div class="col cell price-cell">
                        <div style="flex:1" class="row-cell">
                          <input style="padding: 10px;height:30px;width:100%" value="${item.price}" type="text" class="form-control price">
                        </div>
                    </div>
                    <div class="col cell inventory-cell">
                        <div style="flex:1" class="row-cell">
                          <input style="padding: 10px;height:30px;width:100%" value="${item.stock}" type="text" class="form-control inventory">
                        </div>
                    </div>
                  </div>`);
    });
  }
}

// ------------- set up data -------------- //

function uniqueID() {
  var timestamp = new Date().getTime();
  var uniqueID = timestamp + Math.floor(Math.random() * 1000);
  return uniqueID;
}

function nhapHangLoat() {
  if (!$("#variation-table").prev("#type-all").length) {
    let content = `<div id="type-all" class="my-3 row mx-0 align-items-center">
                    <div class="mr-3">Nhập hàng loạt</div>
                    <input data-name="price" type="text" class="form-control col-3 mr-3" placeholder="Giá">
                    <input data-name="stock" type="text" class="form-control col-3 mr-3" placeholder="Kho hàng">
                    <button data-name="reset" type="button" class="btn btn-primary col-2">Nhập lại</button>
                 </div>`;
    $(content).insertBefore("#variation-table");
  }
}

$(document).on("input", "#type-all input", function (event) {
  let name = $(this).data("name");
  let number = parseInt($(this).val());
  number = isNaN(number) ? 0 : number;
  switch (name) {
    case "price":
      if (is_color_variation_active) {
        if (is_size_variation_active) {
          colors = colors.map(function (item) {
            item.sizes = item.sizes.map(function (item) {
              item.price = number;
              return item;
            });
            return item;
          });
        } else {
          colors = colors.map(function (item) {
            item.price = number;
            return item;
          });
        }
      } else if (is_size_variation_active) {
        sizes = sizes.map(function (item) {
          item.price = number;
          return item;
        });
      }
      $("#add-product-form input.price").val(isNaN(number) ? 0 : number);
      break;
    case "stock":
      if (is_color_variation_active) {
        if (is_size_variation_active) {
          colors = colors.map(function (item) {
            item.sizes = item.sizes.map(function (item) {
              item.stock = number;
              return item;
            });
            return item;
          });
        } else {
          colors = colors.map(function (item) {
            item.stock = number;
            return item;
          });
        }
      } else if (is_size_variation_active) {
        sizes = sizes.map(function (item) {
          item.stock = number;
          return item;
        });
      }
      $("#add-product-form input.inventory").val(isNaN(number) ? 0 : number);
      break;
  }
});

$(document).on("click", "#type-all button", function () {
  if (is_color_variation_active) {
    if (is_size_variation_active) {
      colors = colors.map(function (item) {
        item.sizes = item.sizes.map(function (item) {
          item.price = 0;
          item.stock = 0;
          return item;
        });
        return item;
      });
    } else {
      colors = colors.map(function (item) {
        item.price = 0;
        item.stock = 0;
        return item;
      });
    }
  } else if (is_size_variation_active) {
    sizes = sizes.map(function (item) {
      item.price = 0;
      item.stock = 0;
      return item;
    });
  }
  $("#add-product-form input.price").val(0);
  $("#add-product-form input.inventory").val(0);
  $("#add-product-form #type-all input").val(0);
});

$("#add-product-form #files-input, #add-product-form .image-upload").on(
  "click",
  function () {
    if (
      $("#add-product-form #images-row .image-upload-container").hasClass(
        "p-images-error"
      )
    ) {
      $("#add-product-form #images-row .image-upload-container").removeClass(
        "p-images-error"
      );
    }
  }
);

$(document).on(
  "focus",
  "#add-product-form input, #add-product-form textarea",
  function () {
    if ($(this).hasClass("input-error")) {
      $(this).removeClass("input-error");
      $(this).next("span.error").remove();
    }
  }
);

$("#add-product-form #categories").on("click", function () {
  if ($(this).hasClass("input-error")) {
    $(this).removeClass("input-error");
  }
});

$("#add-color-variation, #add-size-variation").on("click", function () {
  nhapHangLoat();
});

// handle product images

$("#images-preview").sortable({
  cursor: "move",
  cancel: ".image-upload-container",
  update: function (event, ui) {
    var newOrder = [];
    $("#images-preview .img-wrap img").each(function () {
      var id = $(this).data("id");
      let image = p_images.find((item) => item.id === id);
      if (image) {
        newOrder.push(image);
      }
    });
    p_images = newOrder;
  },
});

$("#files-input").on("change", function () {
  var files = this.files;
  for (let i = 0; i < files.length; i++) {
    if (!allowed_types.includes(files[i].type)) {
      continue;
    }
    let id = uniqueID();
    p_images.push({ id: id, file: files[i], status: "new" });
    var reader = new FileReader();
    reader.onload = function (e) {
      $("#upload-icon-wrap").before(
        `<div class="item">
            <div class="img-wrap image-container">
                <img data-id="${id}" src="${e.target.result}" alt="">
                <div class="image-delete">
                    <i data-id="${id}" class="delete-p-image fa fa-solid fa-trash"></i>
                </div>
            </div>
        </div>`
      );
    };
    reader.readAsDataURL(files[i]);
  }
});

$(document).on("click", ".delete-p-image", function () {
  let id = $(this).data("id");
  let name = $(this).data("name");
  let image = p_images.find((item) => item.id === id);
  if (image.status === "old") {
    p_images_to_delete.push(name);
  }
  p_images = p_images.filter((item) => item.id !== id);
  $(this).parent().parent().parent().remove();
});

// handle product images

// handle add variation

$("#add-color-variation").click(function () {
  if (is_color_variation_active) {
    return;
  }
  is_color_variation_active = true;
  let unique_id = uniqueID();
  let color = {
    id: unique_id,
    color: null,
    status: "new",
    value: "",
    gallery: [],
  };
  if (!is_size_variation_active) {
    color.price = null;
    color.stock = null;
  } else {
    sizes = sizes.map((item) => {
      delete item.price;
      delete item.stock;
      return item;
    });
    addSizes(color);
  }
  colors.push(color);
  let content = `<div id="color-variation">
                <h1 style="font-size: 1rem;">Phân loại màu sắc <svg style="width:20px;height:20px;float:right;cursor:pointer" id="close-color-variation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.85355339,1.98959236 L8.157,7.29314575 L13.4601551,1.98959236 C13.6337215,1.81602601 13.9031459,1.79674086 14.098014,1.93173691 L14.1672619,1.98959236 C14.362524,2.18485451 14.362524,2.501437 14.1672619,2.69669914 L14.1672619,2.69669914 L8.864,8.00014575 L14.1672619,13.3033009 C14.362524,13.498563 14.362524,13.8151455 14.1672619,14.0104076 C13.9719997,14.2056698 13.6554173,14.2056698 13.4601551,14.0104076 L8.157,8.70714575 L2.85355339,14.0104076 C2.67998704,14.183974 2.41056264,14.2032591 2.2156945,14.0682631 L2.14644661,14.0104076 C1.95118446,13.8151455 1.95118446,13.498563 2.14644661,13.3033009 L2.14644661,13.3033009 L7.45,8.00014575 L2.14644661,2.69669914 C1.95118446,2.501437 1.95118446,2.18485451 2.14644661,1.98959236 C2.34170876,1.79433021 2.65829124,1.79433021 2.85355339,1.98959236 Z"></path></svg></h1>
                <div id="color-variation-inputs" class="row align-items-center">
                    <div id="color-${unique_id}" class="col-4 mb-3 color-input">
                        <div class="d-flex align-items-center">
                            <input data-id="${unique_id}" name="color_names[]" style="padding: 10px;height:30px" type="text" class="form-control">
                        <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"/>
                        </svg>
                        <svg data-id="${unique_id}" class="delete-color-variation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                        </svg>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <button id="add-color-input" style="font-size:0.9rem" type="button" class="btn btn-outline">Add</button>
                    </div>
                </div>
            </div>`;
  if ($("#size-variation").length) {
    $("#size-variation").before(content);
  } else {
    $("#product-variation").append(content);
  }

  let table = `<div id="variation-table" class="row w-100 mr-0 ml-0">
                    <div id="header" class="row w-100 mr-0 ml-0">
                        <div id="color-header" class="col-2">
                            Màu sắc
                        </div>
                          ${
                            is_size_variation_active
                              ? `<div id="size-header" class="col-2">Kích cỡ</div>`
                              : ""
                          }
                        <div id="price-header" class="col-2">Giá</div>
                        <div id="inventory-header" class="col-2">
                            Kho hàng   
                        </div>
                        <div id="color-gallery-header" class="col">
                            Hình ảnh   
                        </div>
                    </div>
                    <div id="body" class="w-100 mr-0 ml-0">
                        <div data-color_id="${unique_id}" id="variation-row-${unique_id}" class="row w-100 mr-0 ml-0">
                            <div class="col-2 cell color-cell">
                                <div class="d-inline-flex flex-column align-items-center">
                                    <input data-id="${unique_id}" class="color-image" style="display:none" id="color-image-${unique_id}" type="file" name="color_image[]">
                                    <div class="color-upload-icon-wrap">
                                        <div style="border-radius: 4px;width:50px;height:50px;margin-right:0" data-id="${unique_id}" class="color-image-upload image-upload">
                                            <div class="image-upload-icon" style="width: 25px;height:25px">
                                                <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <span style="margin-top:5px"></span>
                                </div>
                            </div>
                            ${
                              is_size_variation_active
                                ? `<div class="col-2 cell size-cell">
                                ${sizes
                                  .map(
                                    (item) =>
                                      `<div class="row-cell size-cell-${item.id}">${item.value}</div>`
                                  )
                                  .join("")}
                                </div>
                                <div class="col-2 cell price-cell">
                                ${sizes
                                  .map(
                                    (item) => `
                                          <div class="row-cell size-price-${item.id}">
                                            <input data-size_id="${item.id}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
                                          </div>`
                                  )
                                  .join("")}
                                </div>
                                <div class="col-2 cell inventory-cell">
                                ${sizes
                                  .map(
                                    (item) => `
                                          <div class="row-cell size-inventory-${item.id}">
                                            <input data-size_id="${item.id}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
                                          </div>`
                                  )
                                  .join("")}
                                </div>`
                                : `
                                    <div class="col-2 cell price-cell">
                                      <div class="row-cell">
                                          <input
                                              style="padding: 10px; height: 30px; width: 100%"
                                              value="0"
                                              type="text"
                                              class="form-control price"
                                          />
                                      </div>
                                    </div>
                                    <div class="col-2 cell inventory-cell">
                                      <div class="row-cell">
                                          <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
                                      </div>
                                   </div>`
                            }
                            <div class="col cell color-gallery-cell">
                                <div class="d-inline-flex align-items-center">
                                    <input data-id="${unique_id}" style="display:none" class="color-gallery" id="color-gallery-${unique_id}" type="file" name="color_gallery[]" multiple>
                                    <div data-color_id="${unique_id}" id="color-gallery-preview-${unique_id}" class="color-gallery-preview">
                                        <div class="upload-icon-wrap">
                                            <div data-id="${unique_id}" style="border-radius: 4px;width:50px;height:50px;margin-right:0" class="color-gallery-upload image-upload">
                                                <div class="image-upload-icon" style="width: 25px;height:25px">
                                                    <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>`;
  if (is_size_variation_active && $("#variation-table").length) {
    $("#variation-table").replaceWith(table);
  } else {
    $("#product-variation").append(table);
  }
  nhapHangLoat();
  $("#color-variation-inputs").sortable({
    cursor: "move",
    cancel: "#add-color-input,input,.delete-color-variation",
    update: function (event, ui) {
      var sortedIDs = $(this).sortable("toArray");
      sortedIDs = sortedIDs.filter((item) => item !== "");
      for (var i = 0; i < sortedIDs.length; i++) {
        var correspondingElem = $("#variation-table").find(
          `#${sortedIDs[i].replace("color", "variation-row")}`
        );
        $("#variation-table #body").append(correspondingElem);
      }
      // reorder colors
      var newOrder = [];
      $("#variation-table #body > div.row").each(function () {
        var color_id = $(this).data("color_id");
        let color = colors.find((item) => item.id === color_id);
        if (color) {
          newOrder.push(color);
        }
      });
      colors = newOrder;
    },
  });
  $(this).css({
    cursor: "not-allowed",
  });
  $(".delete-color-variation").css({
    cursor: "not-allowed",
  });
  if ($("#p-price-stock-row").length) {
    $("#p-price-stock-row").remove();
  }
});

$("#add-size-variation").click(function () {
  if (is_size_variation_active) {
    return;
  }
  is_size_variation_active = true;
  let unique_id = uniqueID();
  let size = { id: unique_id, value: "" };
  if (!is_color_variation_active) {
    size.price = null;
    size.stock = null;
  }
  sizes.push(size);
  let content = `<div id="size-variation">
                <h1 style="font-size: 1rem;">Phân loại kích cỡ <svg style="width:20px;height:20px;float:right;cursor:pointer" id="close-size-variation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.85355339,1.98959236 L8.157,7.29314575 L13.4601551,1.98959236 C13.6337215,1.81602601 13.9031459,1.79674086 14.098014,1.93173691 L14.1672619,1.98959236 C14.362524,2.18485451 14.362524,2.501437 14.1672619,2.69669914 L14.1672619,2.69669914 L8.864,8.00014575 L14.1672619,13.3033009 C14.362524,13.498563 14.362524,13.8151455 14.1672619,14.0104076 C13.9719997,14.2056698 13.6554173,14.2056698 13.4601551,14.0104076 L8.157,8.70714575 L2.85355339,14.0104076 C2.67998704,14.183974 2.41056264,14.2032591 2.2156945,14.0682631 L2.14644661,14.0104076 C1.95118446,13.8151455 1.95118446,13.498563 2.14644661,13.3033009 L2.14644661,13.3033009 L7.45,8.00014575 L2.14644661,2.69669914 C1.95118446,2.501437 1.95118446,2.18485451 2.14644661,1.98959236 C2.34170876,1.79433021 2.65829124,1.79433021 2.85355339,1.98959236 Z"></path></svg></h1>
                <div id="size-variation-inputs" class="row align-items-center">
                    <div id="size-${unique_id}" class="col-4 mb-3 size-input">
                        <div class="d-flex align-items-center">
                            <input data-id="${unique_id}" name="sizes[]" style="padding: 10px;height:30px" type="text" class="form-control">
                        <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"/>
                        </svg>
                        <svg data-id="${unique_id}" class="delete-size-variation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                        </svg>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <button id="add-size-input" style="font-size:0.9rem" type="button" class="btn btn-outline">Add</button>
                    </div>
                </div>
            </div>`;
  if ($("#color-variation").length) {
    $("#color-variation").after(content);
  } else {
    $("#product-variation").append(content);
  }
  nhapHangLoat();
  $("#size-variation-inputs").sortable({
    cursor: "move",
    cancel: "#add-size-input,input,.delete-size-variation",
    update: function (event, ui) {
      let sortedIDs = $(this).sortable("toArray");
      sortedIDs = sortedIDs.filter((item) => item !== "");
      sortedArray = [];
      if (is_color_variation_active) {
        for (let i = 0; i < sortedIDs.length; i++) {
          let id = sortedIDs[i].split("-")[1];
          sortedArray.push(sizes.find((item) => item.id == id));
        }
        sizes = sortedArray;
        $("#variation-table #body > div.row").each((index, item) => {
          for (let i = 0; i < sortedIDs.length; i++) {
            let correspondingElem = $(item).find(
              `div.${sortedIDs[i].replace("size", "size-cell")}`
            );
            let correspondingElemPrice = $(item).find(
              `div.${sortedIDs[i].replace("size", "size-price")}`
            );
            let correspondingElemInventory = $(item).find(
              `div.${sortedIDs[i].replace("size", "size-inventory")}`
            );
            $(item).find("div.size-cell").append(correspondingElem);
            $(item).find("div.price-cell").append(correspondingElemPrice);
            $(item)
              .find("div.inventory-cell")
              .append(correspondingElemInventory);
          }
        });
        // sort sizes in colors
        colors = colors.map((color) => {
          let sortedSizes = [];
          for (let i = 0; i < sortedIDs.length; i++) {
            let id = sortedIDs[i].split("-")[1];
            sortedSizes.push(color.sizes.find((item) => item.id == id));
          }
          color.sizes = sortedSizes;
          return color;
        });
      } else {
        let sortedSizes = [];
        for (let i = 0; i < sortedIDs.length; i++) {
          let size_id = sortedIDs[i].split("-")[1];
          sortedSizes.push(sizes.find((item) => item.id == size_id));
          let correspondingElem = $("#variation-table").find(
            `#${sortedIDs[i].replace("size", "variation-row")}`
          );
          $("#variation-table #body").append(correspondingElem);
        }
        sizes = sortedSizes;
      }
    },
  });

  if (is_color_variation_active) {
    $("#variation-table #header #color-header").after(
      `<div id="size-header" class="col-2">Kích cỡ</div>`
    );
    $("#variation-table #body .color-cell").after(
      `<div class="col-2 cell size-cell"><div class="row-cell size-cell-${unique_id}"></div></div>`
    );
    $("#variation-table #body .price-cell")
      .html(`<div class="row-cell size-price-${unique_id}">
                <input data-size_id="${unique_id}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
              </div>`);
    $("#variation-table #body .inventory-cell")
      .html(`<div class="row-cell size-inventory-${unique_id}">
                <input data-size_id="${unique_id}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
              </div>`);
    // add sizes of colors
    colors = colors.map((color) => {
      color.sizes = [];
      sizes.forEach((size) => {
        color.sizes.push({ ...size, stock: 0, price: 0 });
      });
      delete color.price;
      delete color.stock;
      return color;
    });
  } else {
    $("#product-variation")
      .append(`<div id="variation-table" class="row w-100 mr-0 ml-0">
                  <div id="header" class="row w-100 mr-0 ml-0">
                      <div class="col-3">
                          Kích cỡ
                      </div>
                      <div class="col">
                          Giá
                      </div>
                      <div class="col">
                          Kho hàng   
                      </div>
                  </div>
                  <div id="body" class="w-100 mr-0 ml-0">
                      <div data-size_id="${unique_id}" id="variation-row-${unique_id}" class="row w-100 mr-0 ml-0">
                          <div class="col-3 cell size-cell">
                            <div class="row-cell size-cell-${unique_id}"></div>
                          </div>
                          <div class="col cell price-cell">
                              <div style="flex:1" class="row-cell">
                                <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
                              </div>
                          </div>
                          <div class="col cell inventory-cell">
                              <div style="flex:1" class="row-cell">
                                <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
                              </div>
                          </div>
                      </div>
                  </div>
              </div>`);
  }
  $(this).css({
    cursor: "not-allowed",
  });
  $(".delete-size-variation").css({
    cursor: "not-allowed",
  });
  if ($("#p-price-stock-row").length) {
    $("#p-price-stock-row").remove();
  }
});

// handle add variation

// handle delete variation

$(document).on("click", "svg.delete-color-variation", function () {
  let inputs_left = $(".color-input").length;
  if (inputs_left === 1) {
    return;
  }
  let id = $(this).data("id");
  let color = colors.find((item) => item.id === id);
  if (color && color.status === "old") {
    colors_to_delete.push({
      color_id: color.color_id,
      gallery_dir: color.gallery_dir,
      color_path: color.path,
    });
  }
  colors = colors.filter((item) => item.id !== id);
  $(this).parent().parent().remove();
  $(`#variation-row-${id}`).remove();
  if (inputs_left === 2) {
    $(".delete-color-variation").css({
      cursor: "not-allowed",
    });
  }
});

$(document).on("click", "svg.delete-size-variation", function () {
  let inputs_left = $(".size-input").length;
  if (inputs_left === 1) {
    return;
  }
  let id = $(this).data("id");
  $(this).parent().parent().remove();
  if (is_color_variation_active) {
    $(`#variation-table div.size-cell-${id}`).remove();
    $(`#variation-table div.size-price-${id}`).remove();
    $(`#variation-table div.size-inventory-${id}`).remove();
  } else {
    $(`#variation-table #body div#variation-row-${id}`).remove();
  }
  sizes = sizes.filter((item) => item.id !== id);
  // update sizes of colors
  if (is_color_variation_active) {
    colors = colors.map((color) => {
      color.sizes = color.sizes.filter((size) => size.id !== id);
      return color;
    });
  }
  if (inputs_left === 2) {
    $("svg.delete-size-variation").css({
      cursor: "not-allowed",
    });
  }
});

// handle delete variation

// handle close variation

$(document).on("click", "#close-color-variation", function () {
  $("#color-variation").remove();

  if (is_size_variation_active) {
    sizes.forEach((item) => {
      item.price = 0;
      item.stock = 0;
    });
    $("#variation-table")
      .replaceWith(`<div id="variation-table" class="row w-100 mr-0 ml-0">
                      <div id="header" class="row w-100 mr-0 ml-0">
                          <div class="col-3">
                              Kích cỡ
                          </div>
                          <div class="col">
                              Giá
                          </div>
                          <div class="col">
                              Kho hàng   
                          </div>
                      </div>
                      <div id="body" class="w-100 mr-0 ml-0">
                          ${sizes
                            .map(
                              (
                                item
                              ) => `<div data-size_id="${item.id}" id="variation-row-${item.id}" class="row w-100 mr-0 ml-0">
                                      <div class="col-3 cell size-cell">
                                          <div class="row-cell size-cell-${item.id}">${item.value}</div>
                                      </div>
                                      <div class="col cell price-cell">
                                          <div style="flex:1" class="row-cell">
                                            <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
                                          </div>
                                      </div>
                                      <div class="col cell inventory-cell">
                                          <div style="flex:1" class="row-cell">
                                            <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
                                          </div>
                                      </div>
                                    </div>`
                            )
                            .join("")}
                      </div>
                    </div>`);
  } else {
    $("#variation-table").prev("#type-all").remove();
    $("#variation-table").remove();
    $("#images-row")
      .after(`<div id="p-price-stock-row" class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Giá sản phẩm
                </div>
                <div style="align-self: flex-start;" id="p-price-container" class="col-4">
                    <input type="text" name="p_price" class="form-control">                 
                </div>
                <div style="align-self: flex-start;" class="col-6 row align-items-center">
                    <div class="col-4 input-label">
                        Kho hàng
                    </div>
                    <div id="p-price-container" class="col-8" style="padding-right:0">
                        <input type="text" value="0" name="p_stock" class="form-control">
                    </div>
                </div>
            </div>`);
  }
  is_color_variation_active = false;
  colors.forEach((color) => {
    if (color.status === "old") {
      colors_to_delete.push({
        color_id: color.color_id,
        gallery_dir: color.gallery_dir,
        color_path: color.path,
      });
    }
  });
  colors = [];
  $("#add-color-variation").css({
    cursor: "",
  });
});

$(document).on("click", "#close-size-variation", function () {
  $("#size-variation").remove();
  if (is_color_variation_active) {
    $("#size-header").remove();
    $(".size-cell").remove();
    $(".inventory-cell").replaceWith(
      `<div class="col-2 cell inventory-cell">
        <div class="row-cell">
            <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
        </div>
      </div>`
    );
    $(".price-cell").replaceWith(
      `<div class="col-2 cell price-cell">
        <div class="row-cell">
            <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
        </div>
      </div>`
    );
    colors = colors.map((color) => {
      delete color.sizes;
      color.price = 0;
      color.stock = 0;
      return color;
    });
  } else {
    $("#variation-table").prev("#type-all").remove();
    $("#variation-table").remove();
    $("#images-row")
      .after(`<div id="p-price-stock-row" class="row align-items-center input-row">
                <div class="col-2 input-label">
                    Giá sản phẩm
                </div>
                <div style="align-self: flex-start;" id="p-price-container" class="col-4">
                    <input type="text" name="p_price" class="form-control">              
                </div>
                <div style="align-self: flex-start;" class="col-6 row align-items-center">
                    <div class="col-4 input-label">
                        Kho hàng
                    </div>
                    <div id="p-price-container" class="col-8" style="padding-right:0">
                        <input type="text" value="0" name="p_stock" class="form-control">
                    </div>
                </div>
            </div>`);
  }
  is_size_variation_active = false;
  sizes = [];
  $("#add-size-variation").css({
    cursor: "",
  });
});

// handle close variation

// handle inputs change

$(document).on("input", "#color-variation input", function () {
  let id = $(this).data("id");
  let color = colors.find((item) => item.id === id);
  color.value = $(this).val();
  $(`#variation-row-${id} span`).text($(this).val());
});

$(document).on("input", "#size-variation input", function () {
  let id = $(this).data("id");
  let size = sizes.find((item) => item.id === id);
  size.value = $(this).val();
  $(`.size-cell-${id}`).text($(this).val());
  colors = colors.map((color) => {
    size = color.sizes.find((item) => item.id === id);
    size.value = $(this).val();
    return color;
  });
});

// handle inputs change

// handle add more variation

$(document).on("click", "#add-color-input", function () {
  let unique_id = uniqueID();
  let color = {
    id: unique_id,
    color: null,
    status: "new",
    value: "",
    gallery: [],
  };
  if (!is_size_variation_active) {
    color.price = null;
    color.stock = null;
  } else {
    sizes = sizes.map((item) => {
      delete item.price;
      delete item.stock;
      return item;
    });
    addSizes(color);
  }
  colors.push(color);
  $(
    this
  ).parent().before(`<div id="color-${unique_id}" class="col-4 mb-3 color-input">
                      <div class="d-flex align-items-center">
                      <input data-id="${unique_id}" style="padding: 10px;height:30px" type="text" class="form-control">
                      <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
                      </svg>
                      <svg data-id="${unique_id}" class="delete-color-variation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                          <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                      </svg>
                      </div>
                  </div>`);
  let row = `<div data-color_id="${unique_id}" id="variation-row-${unique_id}" class="row w-100 mr-0 ml-0">
                                                    <div class="col-2 cell color-cell">
                                                        <div class="d-inline-flex flex-column align-items-center">
                                                            <input data-id="${unique_id}" class="color-image" style="display:none" id="color-image-${unique_id}" type="file" name="color_image[]">
                                                            <div class="color-upload-icon-wrap">
                                                                <div style="border-radius: 4px;width:50px;height:50px;margin-right:0" data-id="${unique_id}" class="color-image-upload image-upload">
                                                                    <div class="image-upload-icon" style="width: 25px;height:25px">
                                                                        <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span style="margin-top:5px"></span>
                                                        </div>
                                                    </div>
                                                    ${
                                                      is_size_variation_active
                                                        ? `<div class="col-2 cell size-cell">
                                                        ${sizes
                                                          .map(
                                                            (item) =>
                                                              `<div class="row-cell size-cell-${item.id}">${item.value}</div>`
                                                          )
                                                          .join("")}
                                                        </div>
                                                        <div class="col-2 cell price-cell">
                                                          ${sizes
                                                            .map(
                                                              (item) =>
                                                                `<div class="row-cell size-price-${item.id}">
                                                                  <input
                                                                      data-size_id="${item.id}"
                                                                      style="padding: 10px; height: 30px; width: 100%"
                                                                      value="0"
                                                                      type="text"
                                                                      class="form-control price"
                                                                  />
                                                                </div>`
                                                            )
                                                            .join("")}
                                                        </div>
                                                        <div class="col-2 cell inventory-cell">
                                                        ${sizes
                                                          .map(
                                                            (item) => `
                                                                  <div class="row-cell size-inventory-${item.id}">
                                                                    <input data-size_id="${item.id}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
                                                                  </div>`
                                                          )
                                                          .join("")}
                                                        </div>`
                                                        : `
                                                            <div class="col-2 cell price-cell">
                                                              <div class="row-cell">
                                                                  <input
                                                                      style="padding: 10px; height: 30px; width: 100%"
                                                                      value="0"
                                                                      type="text"
                                                                      class="form-control price"
                                                                  />
                                                              </div>
                                                            </div>
                                                            <div class="col-2 cell inventory-cell">
                                                              <div class="row-cell">
                                                                  <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
                                                              </div>
                                                            </div>`
                                                    }
                                                    <div class="col cell color-gallery-cell">
                                                        <div class="d-inline-flex align-items-center">
                                                            <input data-id="${unique_id}" style="display:none" class="color-gallery" id="color-gallery-${unique_id}" type="file" name="color_gallery[]" multiple>
                                                            <div data-color_id="${unique_id}" id="color-gallery-preview-${unique_id}" class="color-gallery-preview">
                                                                <div class="upload-icon-wrap">
                                                                    <div data-id="${unique_id}" style="border-radius: 4px;width:50px;height:50px;margin-right:0" class="color-gallery-upload image-upload">
                                                                        <div class="image-upload-icon" style="width: 25px;height:25px">
                                                                            <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
  $("#variation-table #body").append(row);
  $(".delete-color-variation").css({
    cursor: "",
  });
});

$(document).on("click", "#add-size-input", function () {
  let unique_id = uniqueID();
  let size = { id: unique_id, value: "" };
  if (!is_color_variation_active) {
    size.price = null;
    size.stock = null;
  }
  sizes.push(size);
  $(
    this
  ).parent().before(`<div id="size-${unique_id}" class="col-4 mb-3 size-input">
                <div class="d-flex align-items-center">
                <input data-id="${unique_id}" style="padding: 10px;height:30px" type="text" class="form-control">
                <svg style="margin:0 10px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-move" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10M.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8"></path>
                </svg>
                <svg data-id="${unique_id}" class="delete-size-variation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                </svg>
                </div>
            </div>`);
  if (is_color_variation_active) {
    $("#variation-table #body .size-cell").append(
      `<div class="row-cell size-cell-${unique_id}"></div>`
    );
    $("#variation-table #body .price-cell").append(
      `<div class="row-cell size-price-${unique_id}">
            <input data-size_id="${unique_id}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
        </div>`
    );
    $("#variation-table #body .inventory-cell").append(
      `<div class="row-cell size-inventory-${unique_id}">
            <input data-size_id="${unique_id}" style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
        </div>`
    );
    // add sizes of colors
    colors = colors.map((color) => {
      color.sizes.push({ ...size, stock: 0, price: 0 });
      return color;
    });
  } else {
    $("#variation-table #body").append(
      `<div data-size_id="${unique_id}" id="variation-row-${unique_id}" class="row w-100 mr-0 ml-0">
          <div class="col-3 cell size-cell">
              <div class="row-cell size-cell-${unique_id}"></div>
          </div>
          <div class="col cell price-cell">
              <div style="flex:1" class="row-cell">
                  <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control price">
              </div>
          </div>
          <div class="col cell inventory-cell">
              <div style="flex:1" class="row-cell">
                  <input style="padding: 10px;height:30px;width:100%" value="0" type="text" class="form-control inventory">
              </div>
          </div>
      </div>`
    );
  }
  $(".delete-size-variation").css({
    cursor: "",
  });
});

// handle add more variation

// handle upload color image

$(document).on("click", ".color-image-upload", function () {
  let id = $(this).data("id");
  $(`#color-image-${id}`).trigger("click");
});

$(document).on("click", ".color-gallery-upload", function () {
  let id = $(this).data("id");
  $(`#color-gallery-${id}`).trigger("click");
});

$(document).on("change", ".color-image", function () {
  let color_image = this.files[0];
  if (!allowed_types.includes(color_image.type)) {
    return;
  }
  let id = $(this).data("id");
  let color = colors.find((item) => item.id === id);
  if (color.status === "old") {
    color.new_image_color = true;
  }
  color.color = color_image;
  const this2 = this;
  if (color_image) {
    const reader = new FileReader();
    reader.onload = function (e) {
      $(this2).next(".color-upload-icon-wrap")
        .replaceWith(`<div class="image-container color-image-container" style="width:50px;height:50px">
                          <img style="width: 100%;height:100%" src="${e.target.result}" alt="">
                          <div style="display:none" class="image-delete color-image-delete">
                              <i data-id="${id}" class="fa fa-solid fa-trash"></i>
                          </div>
                      </div>`);
    };
    reader.readAsDataURL(color_image);
  }
});

$(document).on("change", ".color-gallery", function () {
  let color_gallery = this.files;
  let color_id = $(this).data("id");
  let color = colors.find((item) => item.id === color_id);
  for (var i = 0; i < color_gallery.length; i++) {
    if (!allowed_types.includes(color_gallery[i].type)) {
      return;
    }
    let id = uniqueID();
    color.gallery.push({ id: id, file: color_gallery[i], status: "new" });
    var reader = new FileReader();
    reader.onload = function (e) {
      $(`#color-gallery-preview-${color_id} .upload-icon-wrap`).before(
        `<div class="item">
            <div class="item-wrap image-container">
                <img data-color_gallery_image_id="${id}" src="${e.target.result}" alt="">
                <div class="image-delete">
                    <i data-id="${id}" data-color_id="${color_id}" class="delete-color-gallery-image fa fa-solid fa-trash"></i>
                </div>
            </div>
        </div>`
      );
    };
    reader.readAsDataURL(color_gallery[i]);
  }
  $(".color-gallery-preview").sortable({
    cursor: "move",
    update: function (event, ui) {
      let color_id = $(this).data("color_id");
      let color = colors.find((item) => item.id === color_id);
      let newOrder = [];
      $(this)
        .find("img")
        .each(function () {
          let color_gallery_image_id = $(this).data("color_gallery_image_id");
          let color_gallery_image = color.gallery.find(
            (item) => item.id === color_gallery_image_id
          );
          if (color_gallery_image) {
            newOrder.push(color_gallery_image);
          }
        });
      color.gallery = newOrder;
    },
  });
});

$(document).on("click", ".color-image-delete i", function () {
  $(".color-image").val("");
  let color_id = $(this).data("id");
  let color = colors.find((item) => item.id === color_id);
  if (color) {
    color.color = null;
  }
  $(`input#color-image-${color_id}`).next(
    ".color-image-container"
  ).replaceWith(`<div class="color-upload-icon-wrap">
                    <div data-id="${color_id}" style="border-radius: 4px;width:50px;height:50px;margin-right:0" class="color-image-upload image-upload">
                        <div class="image-upload-icon" style="width: 25px;height:25px">
                            <svg viewBox="0 0 23 21" xmlns="http://www.w3.org/2000/svg"><path d="M18.5 0A1.5 1.5 0 0120 1.5V12c-.49-.07-1.01-.07-1.5 0V1.5H2v12.65l3.395-3.408a.75.75 0 01.958-.087l.104.087L7.89 12.18l3.687-5.21a.75.75 0 01.96-.086l.103.087 3.391 3.405c.81.813.433 2.28-.398 3.07A5.235 5.235 0 0014.053 18H2a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 012 0h16.5z"></path><path d="M6.5 4.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM18.5 14.25a.75.75 0 011.5 0v2.25h2.25a.75.75 0 010 1.5H20v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25z"></path></svg>
                        </div>
                    </div>
                </div>`);
});

$(document).on("click", ".delete-color-gallery-image", function () {
  let color_id = $(this).data("color_id");
  let id = $(this).data("id");
  let color = colors.find((item) => item.id === color_id);
  let color_gallery = color.gallery.find((item) => item.id === id);
  if (color_gallery.status === "old") {
    let image_name = $(this).data("name");
    color.gallery_images_to_delete.push(image_name);
  }
  color.gallery = color.gallery.filter((item) => item.id !== id);
  $(this).parent().parent().parent().remove();
});

// handle save product

$("#add-product-form").submit(function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  // start validate
  let flag = true;
  if (formData.get("p_name").length < 10) {
    $(this).find("input[name=p_name]").addClass("input-error");
    if ($(this).find("input[name=p_name]").next("span.error").length === 0) {
      $(this)
        .find("input[name=p_name]")
        .after(`<span class="error">Tên sản phẩm tối thiểu 10 ký tự</span>`);
    }
    flag = false;
  }
  if (formData.get("p_desc").length < 100) {
    $(this).find("textarea[name=p_desc]").addClass("input-error");
    if ($(this).find("textarea[name=p_desc]").next("span.error").length === 0) {
      $(this)
        .find("textarea[name=p_desc]")
        .after(`<span class="error">Mô tả tối thiểu 100 ký tự</span>`);
    }
    flag = false;
  }
  if (picked_categories.length === 0) {
    $(this).find("#categories").addClass("input-error");
    flag = false;
  }
  if (p_images.length < 1) {
    $(this)
      .find("#images-row .image-upload-container")
      .addClass("p-images-error");
    flag = false;
  }
  if (
    $(this).find("input[name=p_price]").length &&
    (parseInt(formData.get("p_price")) < 1000 ||
      formData.get("p_price") === "" ||
      isNaN(formData.get("p_price")))
  ) {
    $(this).find("input[name=p_price]").addClass("input-error");
    if ($(this).find("input[name=p_price]").next("span.error").length === 0) {
      $(this)
        .find("input[name=p_price]")
        .after(`<span class="error">Giá sản phẩm tối thiểu 1000đ</span>`);
    }
    flag = false;
  }
  if (
    $(this).find("input[name=p_stock]").length &&
    (parseInt(formData.get("p_stock")) < 0 || formData.get("p_stock") === "")
  ) {
    $(this).find("input[name=p_stock]").addClass("input-error");
    flag = false;
  }
  if (is_color_variation_active && colors.length > 0) {
    for (let i = 0; i < colors.length; i++) {
      if (!colors[i].value || colors[i].value === "") {
        $(
          `#add-product-form #color-variation input[data-id=${colors[i].id}]`
        ).addClass("input-error");
        flag = false;
      }
      if (!colors[i].color) {
        $(
          `#add-product-form #variation-table div[data-color_id=${colors[i].id}] .color-image-upload`
        ).addClass("p-images-error");
        flag = false;
      }
      if (colors[i].gallery.length < 1) {
        $(
          `#add-product-form #variation-table div[data-color_id=${colors[i].id}] .color-gallery-upload`
        ).addClass("p-images-error");
        flag = false;
      }
      if (colors[i].hasOwnProperty("sizes") && is_size_variation_active) {
        for (let y = 0; y < colors[i].sizes.length; y++) {
          if (
            !colors[i].sizes[y].hasOwnProperty("value") ||
            colors[i].sizes[y].value === ""
          ) {
            $(
              `#add-product-form #size-variation input[data-id=${colors[i].sizes[y].id}]`
            ).addClass("input-error");
            flag = false;
          }
          if (
            !colors[i].sizes[y].hasOwnProperty("price") ||
            isNaN(colors[i].sizes[y].price) ||
            colors[i].sizes[y].price < 1000
          ) {
            $(
              `#add-product-form div[data-color_id=${colors[i].id}] .size-price-${colors[i].sizes[y].id} input`
            ).addClass("input-error");
            if (
              $(
                `#add-product-form div[data-color_id=${colors[i].id}] .size-price-${colors[i].sizes[y].id} input`
              ).next("span.error").length === 0
            ) {
              $(
                `#add-product-form div[data-color_id=${colors[i].id}] .size-price-${colors[i].sizes[y].id} input`
              ).after(
                `<span style="font-size:0.7rem;padding-top:15px;margin-bottom:0" class="error">Giá sản phẩm tối thiểu 1000đ</span>`
              );
            }
            flag = false;
          }
          if (
            !colors[i].sizes[y].hasOwnProperty("stock") ||
            isNaN(colors[i].sizes[y].stock) ||
            colors[i].sizes[y].stock < 0
          ) {
            $(
              `#add-product-form div[data-color_id=${colors[i].id}] .size-inventory-${colors[i].sizes[y].id} input`
            ).addClass("input-error");
            flag = false;
          }
        }
      } else {
        if (
          !colors[i].hasOwnProperty("price") ||
          isNaN(colors[i].price) ||
          colors[i].price < 1000
        ) {
          $(
            `#add-product-form #variation-table div[data-color_id=${colors[i].id}] .price-cell input`
          ).addClass("input-error");
          if (
            $(
              `#add-product-form #variation-table div[data-color_id=${colors[i].id}] .price-cell input`
            ).next("span.error").length === 0
          ) {
            $(
              `#add-product-form #variation-table div[data-color_id=${colors[i].id}] .price-cell input`
            ).after(
              `<span style="font-size:0.7rem;padding-top:15px;margin-bottom:0" class="error">Giá sản phẩm tối thiểu 1000đ</span>`
            );
          }
          flag = false;
        }
        if (
          !colors[i].hasOwnProperty("stock") ||
          isNaN(colors[i].stock) ||
          colors[i].stock < 0
        ) {
          $(
            `#add-product-form #variation-table div[data-color_id=${colors[i].id}] .inventory-cell input`
          ).addClass("input-error");
          flag = false;
        }
      }
    }
  } else if (is_size_variation_active && sizes.length > 0) {
    for (let i = 0; i < sizes.length; i++) {
      if (!sizes[i].hasOwnProperty("value") || sizes[i].value === "") {
        $(
          `#add-product-form #size-variation input[data-id=${sizes[i].id}]`
        ).addClass("input-error");
        flag = false;
      }
      if (
        !sizes[i].hasOwnProperty("price") ||
        isNaN(sizes[i].price) ||
        sizes[i].price < 1000
      ) {
        $(
          `#add-product-form #variation-table div[data-size_id=${sizes[i].id}] .price-cell input`
        ).addClass("input-error");
        if (
          $(
            `#add-product-form #variation-table div[data-size_id=${sizes[i].id}] .price-cell input`
          ).next("span.error").length === 0
        ) {
          $(
            `#add-product-form #variation-table div[data-size_id=${sizes[i].id}] .price-cell input`
          ).after(
            `<span style="font-size:0.7rem;padding-top:15px;margin-bottom:0" class="error">Giá sản phẩm tối thiểu 1000đ</span>`
          );
        }
        flag = false;
      }
      if (
        !sizes[i].hasOwnProperty("stock") ||
        isNaN(sizes[i].stock) ||
        sizes[i].stock < 0
      ) {
        $(
          `#add-product-form #variation-table div[data-size_id=${sizes[i].id}] .inventory-cell input`
        ).addClass("input-error");
        flag = false;
      }
    }
  }
  if (!flag) {
    notif({
      msg: "Vui lòng nhập đầy đủ thông tin!",
      type: "warning",
      position: "center",
      height: "auto",
      top: 80,
      timeout: 10000,
      animation: "slide",
    });
    return;
  }
  // end validate
  if (is_updating === true) {
    return;
  }
  is_updating = true;
  $("#overlay").css({
    display: "flex",
  });
  let p_prices = [];
  for (let i = 0; i < p_images.length; i++) {
    formData.append("p_images[]", p_images[i].file);
    formData.append(`p_images_meta[${i}][status]`, p_images[i].status);
  }
  for (let i = 0; i < picked_categories.length; i++) {
    formData.append("categories[]", picked_categories[i]);
  }
  // check if there any product images to delete
  if (p_images_to_delete.length > 0) {
    for (let i = 0; i < p_images_to_delete.length; i++) {
      formData.append("p_images_to_delete[]", p_images_to_delete[i]);
    }
  }
  // check if there any colors to delete
  if (colors_to_delete.length > 0) {
    for (let i = 0; i < colors_to_delete.length; i++) {
      formData.append(
        `colors_to_delete[${i}][color_id]`,
        colors_to_delete[i].color_id
      );
      formData.append(
        `colors_to_delete[${i}][gallery_dir]`,
        colors_to_delete[i].gallery_dir
      );
      formData.append(
        `colors_to_delete[${i}][color_path]`,
        colors_to_delete[i].color_path
      );
    }
  }
  if (colors.length > 0) {
    for (let i = 0; i < colors.length; i++) {
      if (colors[i].hasOwnProperty("new_image_color")) {
        formData.append(`new_image_color_${i}`, true);
      }
      formData.append("colors[]", colors[i].color);
      formData.append(`name_of_color_${i}`, colors[i].value);
      if (colors[i].hasOwnProperty("price")) {
        formData.append(`price_of_color_${i}`, colors[i].price);
        formData.append(`stock_of_color_${i}`, colors[i].stock);
      }
      formData.append(`meta_of_color[${i}][status]`, colors[i].status);
      formData.append(`meta_of_color[${i}][path]`, colors[i].path);
      if (colors[i].status === "old") {
        formData.append(`meta_of_color[${i}][color_id]`, colors[i].color_id);
      }
      formData.append(
        `meta_of_color[${i}][gallery_dir]`,
        colors[i].gallery_dir
      );
      if (colors[i].status === "old") {
        if (colors[i].gallery_images_to_delete.length > 0) {
          for (let z = 0; z < colors[i].gallery_images_to_delete.length; z++) {
            formData.append(
              `gallery_images_to_delete_of_color_${i}[]`,
              colors[i].gallery_images_to_delete[z]
            );
          }
        }
      }
      if (colors[i].gallery.length > 0) {
        for (let y = 0; y < colors[i].gallery.length; y++) {
          formData.append(`gallery_of_color_${i}[]`, colors[i].gallery[y].file);
          if (colors[i].status === "old") {
            formData.append(
              `meta_gallery_of_color_${i}[${y}][status]`,
              colors[i].gallery[y].status
            );
          }
        }
      }
      if (colors[i].hasOwnProperty("sizes")) {
        for (let x = 0; x < colors[i].sizes.length; x++) {
          formData.append(
            `sizes_of_color_${i}[${x}][size]`,
            colors[i].sizes[x].value
          );
          formData.append(
            `sizes_of_color_${i}[${x}][stock]`,
            colors[i].sizes[x].stock
          );
          formData.append(
            `sizes_of_color_${i}[${x}][price]`,
            colors[i].sizes[x].price
          );
        }
      }
      // evaluating price
      if (colors[i].hasOwnProperty("sizes")) {
        let price = Math.max(...colors[i].sizes.map((item) => item.price));
        p_prices.push(price);
      } else {
        p_prices.push(colors[i].price);
      }
    }
  } else if (sizes.length > 0) {
    for (let i = 0; i < sizes.length; i++) {
      formData.append(`sizes[${i}][value]`, sizes[i].value);
      formData.append(`sizes[${i}][price]`, sizes[i].price);
      formData.append(`sizes[${i}][stock]`, sizes[i].stock);
      // evaluating price
      p_prices.push(sizes[i].price);
    }
  }
  if (p_prices.length > 0) {
    formData.append("p_price", Math.max(...p_prices));
  }
  $.ajax({
    type: "POST",
    url: request_url,
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      // return console.log(back);
      window.location.href = back;
    },
    error: function (xhr, status, error) {
      if (xhr.status === 401) {
        notif({
          msg: xhr.responseJSON,
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
    is_updating = false;
    $("#overlay").css({
      display: "none",
    });
  });
});

// handle save product

function addSizes(color) {
  color.sizes = [];
  sizes.forEach((size) => {
    color.sizes.push({ ...size, stock: 0, price: 0 });
  });
}

$(document).on(
  "input",
  "#add-product-form input[name=p_price],#add-product-form input[name=p_stock],#add-product-form input.inventory, #add-product-form input.price",
  function () {
    $(this).mask("0#");
    let value = $(this).val();
    if (/^0+[1-9]/.test(value)) {
      value = value.replace(/^0+/, "");
      $(this).val(value);
    }
  }
);

// handle price size input change
$(document).on("input", "input.price", function () {
  if (is_color_variation_active) {
    let color_id = $(this).parent().parent().parent().data("color_id");
    let color = colors.find((item) => item.id === color_id);
    if (is_size_variation_active) {
      let size_id = $(this).data("size_id");
      let size = color.sizes.find((item) => item.id === size_id);
      size.price = parseInt($(this).val());
    } else {
      color.price = parseInt($(this).val());
    }
  } else if (is_size_variation_active) {
    let size_id = $(this).parent().parent().parent().data("size_id");
    let size = sizes.find((item) => item.id === size_id);
    size.price = parseInt($(this).val());
  }
});

// handle inventory size input change
$(document).on("input", "input.inventory", function () {
  if (is_color_variation_active) {
    let color_id = $(this).parent().parent().parent().data("color_id");
    let color = colors.find((item) => item.id === color_id);
    if (is_size_variation_active) {
      let size_id = $(this).data("size_id");
      let size = color.sizes.find((item) => item.id === size_id);
      size.stock = parseInt($(this).val());
    } else {
      color.stock = parseInt($(this).val());
    }
  } else if (is_size_variation_active) {
    let size_id = $(this).parent().parent().parent().data("size_id");
    let size = sizes.find((item) => item.id === size_id);
    size.stock = parseInt($(this).val());
  }
});

// handle delete product

$("#delete-product").click(function () {
  let answer = confirm("Bạn thực sự muốn xóa sản phẩm này?");
  if (answer) {
    $.ajax({
      type: "POST",
      url: delete_url,
      data: {
        csrf_token: csrf_token,
      },
      success: function (response) {
        window.location.href = back_url;
      },
      error: function (xhr, status, error) {
        if (xhr.status === 401) {
          notif({
            msg: xhr.responseJSON,
            type: "warning",
            position: "center",
            height: "auto",
            top: 80,
            timeout: 5000,
            animation: "slide",
          });
        }
      },
    });
  }
});

// handle categories pick
$("#categories-confirm").click(function () {
  picked_categories = [];
  let category_list = [...categories];
  while (category_list) {
    let category = category_list.find((item) => item.hasOwnProperty("picked"));
    if (category) {
      picked_categories.push(category.id);
      if (category.hasOwnProperty("children")) {
        category_list = category.children;
      } else {
        category_list = null;
      }
    } else {
      break;
    }
  }
  $("#add-product-form #categories").html(
    `<span id="picked-categories-label">${buildPickedCategories(
      categories
    )}</span>`
  );
  $("#categoriesModal").modal("hide");
});
