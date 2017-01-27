

function productsUpdate()
{
//    $('#myForm').submit(function() {
        // get all the inputs into an array.
        var $inputs = $j('#editData :input');

        // not sure if you wanted this, but I thought I'd add it.
        // get an associative array of just the values.
        var values = {};
        $inputs.each(function() {
            values[this.name] = $j(this).val();
        });
        console.log(values);
//alert('test');
//return null;
//    });
/*
editData.action = this.massUpdateProducts;
    console.log(editData.action);
    editData.submit();*/
}







function specifyRelatedProducts() 
{
    var productids = window.prompt("Enter the products ID's you would like to relate the currently selected products to.\n"
        +"For example: Suppose you selected Y and Z.  If you enter 'A,B' here,\n"
        +" Y will be related to A and B, Z will be related to A and B.\n"
        +"Separate multiple product IDs (NOT SKUs!) by a comma as shown in the example above.", "<Enter product IDs>");
    if (productids == "" || productids == null) 
    {
        return null;
    }
    if (!window.confirm("Are you sure you would like to one-way relate selected grid products to products ("+ productids +")")) 
    {
        return null;
    }
    return productids;
}

function specifyRelatedEachOther()
{
    if (!window.confirm("Are you sure you would like to make the selected products related to each other?")) 
    {
        return null;
    }
    return true;
}

function specifyRelatedClean()
{
    if (!window.confirm("Are you sure you would like to remove all related products of products selected list?")) 
    {
        return null;
    }
    return true;
}








function chooseWhatToCrossSellTo() 
{
    var productids = window.prompt("Enter the id's for products you would like to add as cross-sell to the currently selected products.\n"
        +"For example: Suppose you selected Y and Z.  If you enter 'A,B' here,\n"
        +"X will be cross-sold to A and B, Z will be cross-sold with A and with B.\n"
        +"Separate multiple product IDs (NOT SKUs!) by a comma as shown in the example above.", "<Enter product IDs>");
    if (productids == "" || productids == null) 
    {
        return null
    }
    if (!window.confirm("Are you sure you'd like to one-way cross-sell products ("+ productids +") to selected grid products?")) 
    {
        return null
    }
    return productids;
}

function specifyCrossSellEachOther()
{
    if (!window.confirm("Are you sure you would like ot make the selected products cross-sell each other?"))
    {
        return null;
    }
    return true;
}

function specifyCrossSellClean()
{
    if (!window.confirm("Are you sure you would like to remove these product's cross sell links?"))
    {
        return null;
    }
    return true;
}






function chooseWhatToUpSellTo() 
{
    var productids = window.prompt("Enter the id's for products you would like to add as up-sells to the currently selected products.\n"
        +"For example: Suppose you selected Y and Z.  If you enter 'A,B' here,\n"
        +"A and B will be up-sells of Y, A and B will be up-sells of Z.\n"
        +"Separate multiple product ids (NOT SKUs!) by a comma as shown in the example above.", "<Enter product IDs>");
    if (productids == "" || productids == null) 
    {
        return null
    }
    if (!window.confirm("Are you sure you would like add products ("+ productids +") to selected grid products up-sell?")) 
    {
        return null
    }
    return productids;
}

function specifyUpSellClean()
{
    if (!window.confirm("Are you sure you would like to remove any up-sells for selected product(s)?"))
    {
        return null;
    }
    return true;
}
