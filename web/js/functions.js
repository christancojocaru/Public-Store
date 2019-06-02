function addToCart(productId) {
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
            $("#addToCartModalButton").attr("data-product-id", productId);
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
        url: "cart/new",
        data: data,
        success: function (success) {
            window.alert(success);
            $("#close").click();
        },
        error: function (error) {
            window.alert(error.responseText);
        }
    });
});
