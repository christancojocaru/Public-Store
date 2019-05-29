function addToCart(productId)
{
    checkAlreadyExists(productId);
}

function createRow(product) {
    let tableId = 1;

    if( $(".idID").length ) {
        tableId = $(".idID").last().text();
        tableId++;
    }

    let tdId = $("<td></td>").text(tableId);
    tdId.attr("class", "idID");
    let tdName = $("<td></td>").text(product.name);
    let tdQuantity = $("<td></td>").text(product.quantity);
    tdQuantity.attr("class", "quantity");
    let tdTotalPrice = $("<td></td>").text(product.totalPrice + " lei");

    let tr = $("<tr></tr>")
    tr.attr("product-id", product.id);
    tr.append(tdId);
    tr.append(tdName);
    tr.append(tdQuantity);
    tr.append(tdTotalPrice);
    $("#cart_table_body").append(tr);
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
        let data = {'product_id': tProductId};
        $.ajax({
            type: "POST",
            url: "nameAndStock",
            data: data,
            success: function (success) {
                $("#strongQuantity").text(" " + success.name);
                $("#labelQuantity").text("Update quantity for ");
                $("#quantity").attr("max", success.stock);
                $("#quantity").val(tInputValue);
                $("#quantity").prop("disabled", false);
                $("#addToCartModalButton").prop("disabled", false);
                $("#addToCartModalButton").attr("data-product-id", productId);
                $("#addToCartModalButton").attr("data-is-on-table", true);

            },
            error: function (error) {
                if (error.status === 500) {
                    $("#modalHeader").text("Internal Server Error, Please try again");
                }
            },
            dataType: "json"
        });
    }else { //means is not in table
        let data = {'product_id': productId};
        $.ajax({
            type: "POST",
            url: "nameAndStock",
            data: data,
            success: function(success){
                $("#strongQuantity").text(" " + success.name);
                $("#labelQuantity").text("Select quantity for ");
                $("#quantity").attr("max", success.stock);
                $("#quantity").val(1);
                $("#quantity").prop("disabled", false);
                $("#addToCartModalButton").prop("disabled", false);
                $("#addToCartModalButton").attr("data-product-id", productId);
                $("#addToCartModalButton").attr("data-is-on-table", false);
            },
            error: function (error){
                if (error.status == 500) {
                    $("#modalHeader").text("Internal Server Error, Please try again");
                }
            },
            dataType: "json"
        });
    }
}

function updateRow(data) {
    let tr = $('[product-id=' + data.id + ']')[0];
    tr.children[2].textContent = data.quantity;
    tr.children[3].textContent = data.totalPrice + " lei";
}

$("#addToCartModalButton").click(function () {
    let data = {
        'product_id': $(this).attr('data-product-id'),
        'quantity': $("#quantity").val()
    };

    $.ajax({
        type: "POST",
        url: "addtoCart",
        data: data,
        success: function (product) {
            let addToCart = $("#addToCartModalButton");
            if ( addToCart.attr('data-is-on-table') === "true") {
                updateRow(product);
            }else {
                createRow(product);
            }
            closeModal();
        },
        dataType: "json"
    });
});