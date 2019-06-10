$("#addToCart").click(function (e) {
    e.preventDefault();

    let price = $("#_price").val();
    let id = $("#_id").val();
    let data = {
        'id': id,
        'price': price
    };

    $.ajax({
        type: "POST",
        url: 'addToCart',
        data: data,
        success: function (success) {
            window.alert(success);
        },
    });
});


$("#cart_table").on('click', '.delete', function (e) {
    e.preventDefault();

    if (confirm("Are you sure to delete this row ?") == false) {
        return;
    }

    let cartId = $(this).data("id");
    let data = {'id' : cartId};
    $.ajax({
        type: 'POST',
        url: 'deleteCart',
        data: data,
        error: function () {
            window.alert("Not Found!");
            return;
        }
    });
    $(this).parents('tr')[0].remove();
});

$(".form-group span").css("width", '10%');

$(".input-group").css("width", '1000px');

