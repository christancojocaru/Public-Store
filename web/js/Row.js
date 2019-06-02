class Row {
    constructor(productId) {
        this.tr = $("<tr></tr>").attr("product-id", productId);
        this.tdId = $("<td></td>");
        this.tdName = $("<td></td>");
        this.tdQuantity = $("<td></td>");
        this.tdTotalPrice = $("<td></td>");
        this.appendRow();
        this.attr();
    }

    appendRow() {
        this.tr.append(this.tdId);
        this.tr.append(this.tdName);
        this.tr.append(this.tdQuantity);
        this.tr.append(this.tdTotalPrice);
    }

    attr() {
        this.tdId.attr("class", "idID");
        this.tdQuantity.attr("class", "quantity");
    }

    text(id, name, quantity, price) {
        this.tdId.text(id);
        this.tdName.text(name);
        this.tdQuantity.text(quantity);
        this.tdTotalPrice.text(price + " lei");
    }

    get row() {
        return this.tr;
    }
}