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
    ReactDOM.unmountComponentAtNode(document.querySelector("div#uploadResponse"));
    $("#uploadResponse").empty();
});

$("#fileupload").change(function () {
    const fileExtension = ['csv'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) === -1 && $(this).val()) {
        alert("Only this formats are allowed : "+fileExtension.join(', '));
        $("#remove").click();
    }
});

$("#btnvalidation").click(function (e) {
    e.preventDefault();
    ReactDOM.unmountComponentAtNode(document.querySelector("div#uploadResponse"));
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
            success: function (data) {
                let noOfErrors = countErrors(data);
                let type = (noOfErrors > 0) ? "danger" : "success";
                $('#uploadResponse').append(getAlert(type, noOfErrors));



                $.each(data, function (rowNumber, rowValues) {
                    let panel = getPanel();
                    panel.find('h3').text('Row ' + rowNumber);
                    $.each(rowValues, function (column, value) {
                        column = invertColumnIdsToNames(column);
                        let body = panel.find('.panel-body');

                        body.append(getBody(column, value));
                    });
                    $('#uploadResponse').append(panel);
                });
                if (noOfErrors > 0) {
                    hint();
                }
            },
        });
    }
});

function timeout() {
    $('span.click-me').css("display", "none");
    $('button[data-color="red"]').css('display', 'none');
    $('button[data-color="black"]').css('display', 'inline-block');
}

function hint() {
    const spanContainer = document.querySelector('#errors');
    const allPanels = parseInt(spanContainer.textContent);

    $(".panel-body div input").click(function () {
        let allCheckboxs = $(this).parents("div.panel-danger").find("div.panel-body div").length;
        let allCheckboxsChecked = $(this).parents("div.panel-danger").find("div.panel-body input:checked").length;
        if (allCheckboxs === allCheckboxsChecked) {
            setTimeout(timeout, 10000);
            $(this).parents("div.panel-danger").find("div div span:first").css("display", 'inline');
            $(this).parents("div.panel-danger").find("button[data-color='black']").css("display", "none");
            $(this).parents("div.panel-danger").find("button[data-color='red']").css("display", "inline-block");
        }
    });
    $(".panel-heading div button.close").click(function () {
        $(this).parents("div.panel-danger").attr('hidden', 'true');
        ReactDOM.render(spanContainer.textContent - 1, spanContainer);
        success(allPanels);
    });
}

function success(allPanels) {
    let panelHidden = $("#uploadResponse").find("div.panel-danger:hidden").length;
    if (allPanels === panelHidden) {
        ReactDOM.render(React.createElement(UploadSuccessButton), document.querySelector("#uploadResponse"));
    }
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

function getPanel() {
    let panel = $("<div>").addClass('panel panel-danger');
    let heading = $("<div>").addClass('panel-heading');
    let body = $("<div>").addClass('panel-body');
    let span = $("<span>").css('display', 'none').addClass('click-me').text("Click me for better view->");
    let cancel_black = $("<button>").attr({
        'type' : 'button',
        'class' : 'close',
        'data-color' : 'black'
    }).css('display', 'inline-block');
    let cancel_red = $("<button>").attr({
        'type' : 'button',
        'class' : 'close',
        'data-color' : 'red'
    }).css('display', 'none');
    let h3 = $("<h3>").addClass('panel-title');
    let div = $("<div>");
    div.append(cancel_black);
    div.append(cancel_red);
    div.append(span);

    heading.append(div);
    heading.append(h3);

    panel.append(heading);
    panel.append(body);

    return panel;
}

function getAlert(type, noOfErrors) {
    let div = $("<div>").addClass("alert alert-"+type).attr("role", "alert");

    let success = $("<p>").text("Great job, your csv it's ready for upload!");
    let errorSpan = $("<span>").attr("id", "errors").text(noOfErrors);
    let error = $("<p>").attr("id", "errorrParag").append("Your csv have ", errorSpan, " errors, please review them and upload again until has no errors!");
    let p = (type === "success") ? success : error;
    return div.append(p);
}