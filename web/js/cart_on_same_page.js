function createRow(product) {
    let tableId = 1;

    if( $(".idID").length ) {
        tableId = $(".idID").last().text();
        tableId++;
    }

    let row = new Row(product.id);
    row.text(tableId, product.name, product.quantity, product.totalPrice);
    $("#cart_table_body").append(row.row);
}

function closeModal() {
    $("#addToCartModalButton").attr("data-product-id", undefined);
    $("#addToCartModalButton").attr("data-is-on-table", false);
    $("#close").click();
}

function checkAlreadyExists(productId) {
    let tProductId;
    let tInputValue;

    if ($(".idID").length) {
        $(".idID").each(function (index, item) {
            let tableProductId = $(item).parent().attr("product-id");
            let tableQuantity = $(item).parent().children("td")[2].textContent;
            if (tableProductId == productId) {
                tProductId = tableProductId;
                tInputValue = tableQuantity;
            }
        });
    }
    if(tProductId) {//means is already in table
        ajaxNameAndStock(tProductId, tInputValue, "Update quantity for ", true);
    }else { //means is not in table
        ajaxNameAndStock(productId, 1, "Select quantity for ", false);
    }
}

function updateRow(data) {
    let tr = $('[product-id=' + data.id + ']')[0];
    tr.children[2].textContent = data.quantity;
    tr.children[3].textContent = data.totalPrice + " lei";
}

function cartOnSamePage(productId) {
    $("#product_table").addClass("col-md-5");
    $("#between_tables").addClass("col-md-4");
    $("#between_tables").attr("style", "display: block");
    $("#cart_table").attr("style", "display: block");
    $("#cart_table").addClass("col-md-3");
    checkAlreadyExists(productId);
}
