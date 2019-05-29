function addToCart(productId) {
    loadDoc(productId, returnProduct);
}

function setModal(name, stock) {
    $("#labelQuantity").text(name);
    $("#quantity").attr("max", stock);
}

function closeModal() {
    $("#close").click();
}


function loadDoc(id, cfunction) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            cfunction(this);
        }
    };
    xhttp.open("GET", "ajax?id=" + id, true);
    xhttp.send();
}

function returnProduct(aaa) {
    let Product = new Object();

    jsonProduct = JSON.parse(aaa.responseText);

    Product.id = jsonProduct.id;
    Product.name = jsonProduct.name;
    Product.price = jsonProduct.price;
    Product.stock = jsonProduct.stock; //for future
    Product.department = jsonProduct.department;//for future

    setModal(Product.name, Product.stock);

    $("#addToCartModalButton").click(function () {
            quantity = $("#quantity").val();
            console.log(quantity);
            createRow(Product.id, Product.name, Product.price, quantity);
            closeModal();
    });

    delete Product;

    // freezeProduct();
}

function createRow(id, name, price, quantity) {
    var tdId = $("<td></td>").text(id);   // Create with jQuery
    var tdName = $("<td></td>").text(name);   // Create with jQuery
    var tdQuantity = $("<td></td>").text(quantity);   // Create with jQuery
    var tdTotalPrice = $("<td></td>").text(price * quantity);   // Create with jQuery

    var tr = $("<tr></tr>");
    tr.append(tdId)// Create with DOM
    tr.append(tdName)// Create with DOM
    tr.append(tdQuantity)// Create with DOM
    tr.append(tdTotalPrice)// Create with DOM
    $("#cart_table_body").append(tr);      // Append the new elements
}
