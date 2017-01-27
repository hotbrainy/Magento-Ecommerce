$j(document).ready(function() {
    var growler = Growler ? new Growler() : {};

    var addToWishlist = function(url){
        growler.warn("Adding product to wishlist",5);
        var ajaxAddWishlist = $j.ajax({
            url: url,
            type: 'GET',
            dataType: "html"
        });
        ajaxAddWishlist.done(function (r) {
            r = JSON.parse(r);
            if (r.status != 'success') {
                growler.error(r.msg,5);
            }else{
                growler.info(r.msg,5);
            }
        });
    };

    $j('body').on('click','.link-wishlist',function(e){
        e.preventDefault();
        addToWishlist(e.currentTarget.href.replace('/add/','/ajaxAdd/'));
    });
});