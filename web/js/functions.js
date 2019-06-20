$(".form-group span").css("width", '10%');

$(".input-group").css("width", '1000px');

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

    if (confirm("Are you sure to delete this row ?") === false) {
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
        }
    });
    $(this).parents('tr')[0].remove();
});

$("#remove").click(function (e) {
    e.preventDefault();
    let table = $("#uploadResponse");
    table.empty();
    // table.parent().children()[0].remove();
});

$("#fileupload").change(function () {
    const fileExtension = ['csv'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) === -1 && $(this).val()) {
        alert("Only this formats are allowed : "+fileExtension.join(', '));
        $("#remove").click();
    }
});

$("#btnupload").click(function (e) {
    e.preventDefault();
    $("#uploadResponse").empty();
    let data = $('#fileupload').prop('files')[0];
    if (data) {
        let formdata = new FormData();
        formdata.append('upload', data);
        let url = $(this).data('url');

        $.ajax({
            type: 'POST',
            url: url,
            data: formdata,
            processData: false,
            contentType: false,
            converters: {"text json": jQuery.parseJSON},
            success: function (success) {
                let noOfErrors = countErrors(success);
                let type = (noOfErrors > 0) ? "info" : "success";
                $('#uploadResponse').append(getAlert(type, noOfErrors));

                $.each(success, function (rowNumber, rowValues) {
                    let panel = getPanel();
                    panel.find('h3').text('Row ' + rowNumber);
                    $.each(rowValues, function (column, value) {
                        column = invertColumnIdsToNames(column);
                        let body = panel.find('.panel-body');

                        body.append(getBody(column, value));
                    });
                    $('#uploadResponse').append(panel);
                });
                closeAction();
                hint();
            },
        });
    }
});

function hint() {
    $(".panel-body div input").click(function () {
        let allCheckboxs = $(this).parent().siblings().length + 1;
        let allCheckboxsChecked = $(this).parents("div.panel-body").find("input:checked").length;
        if (allCheckboxs === allCheckboxsChecked) {
            $(this).parents("div.panel-info").find("button.close").click();
        }
    });
}

function getBody(column, value) {
    let input = $("<input>").attr('type', 'checkbox');
    let span = $("<span>");
    span.text(`Column ${column} : ${value}`);
    let br = $("</br>");
    return $("<div>").append(input, span, br);
}

function invertColumnIdsToNames(column) {
    let columnName = "";
    switch (column) {
        case "1":
            columnName = "Name";
            break;
        case "2":
            columnName = "Category";
            break;
        case "3":
            columnName = "Price";
            break;
        case "4":
            columnName = "Stock";
            break;
        default:
            columnName = column;
    }
    return columnName;
}

function countErrors(obj) {
    let keys = Object.keys(obj);
    return keys.length;
}

function closeAction() {
    $(".panel-heading").find('.close').click(function (e) {
        e.preventDefault();
        $(this).parent().parent().attr('hidden', 'true');
    });
}

function getPanel() {
    let panel = $("<div>").addClass('panel panel-info');
    let heading = $("<div>").addClass('panel-heading');
    let body = $("<div>").addClass('panel-body');

    let cancel = $("<button>").attr({
        'class' : 'close',
        'type' : 'button',
        'id' : 'close'
        });
    let h3 = $("<h3>").addClass('panel-title');
    heading.append(cancel);
    heading.append(h3);

    panel.append(heading);
    panel.append(body);

    return panel;
}

function getAlert(type, noOfErrors) {
    let div = $("<div>").addClass("alert alert-"+type);
    div.attr("role", "alert");
    let text = (type === "success") ? "Great job, your csv it's ready for implementing!" : `Your csv have ${noOfErrors} errors, please review them and upload again until has no errors!`;
    let p = $("<p>").text(text);
    return div.append(p);
}