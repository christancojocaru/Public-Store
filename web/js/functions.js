function addToCart(productId)
{
    cartOnSamePage(productId);
}

function ajaxNameAndStock(productId, quantityInputValue = 1, labelText, isOnTable) {
    let data = {'product_id': productId};
    $.ajax({
        type: "POST",
        url: "nameAndStock",
        data: data,
        success: function(success){
            $("#strongQuantity").text(" " + success.name);
            $("#labelQuantity").text(labelText);
            $("#quantity").attr("max", success.stock);
            $("#quantity").val(quantityInputValue);
            $("#quantity").prop("disabled", false);
            $("#addToCartModalButton").prop("disabled", false);
            $("#addToCartModalButton").attr("data-product-id", productId);
            $("#addToCartModalButton").attr("data-is-on-table", isOnTable);
        },
        error: function (error){
            if (error.status == 500) {
                $("#modalHeader").text("Internal Server Error, Please try again");
            }
        },
        dataType: "json"
    });
}

$("#addToCartModalButton").click(function () {
    let quantity = $("#quantity").val();
    if (!$.isNumeric(quantity)) {
        window.alert("Please insert a number!");
        $("#quantity").attr('type', 'number');
        return;
    }
    let data = {
        'product_id': $(this).attr('data-product-id'),
        'quantity': quantity
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
        error: function (error) {
            window.alert(error.responseText);
        },
        dataType: "json"
    });
});
