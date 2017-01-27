function jqUpdateSize() {
    var height = jQuery(window).height();
    var sqheight = jQuery('.toppart-outer').height();
    //console.log (height ,sqheight)
    if (height > sqheight) {
        jQuery('.top_part').css('height', height);
    }
};
jQuery(document).ready(jqUpdateSize); // When the page first loads
jQuery(window).resize(jqUpdateSize); // When the browser changes size

jQuery(window).load(function() {
    jQuery("#header").sticky({
        topSpacing: 0
    });

    //add calss to body
    var windowWidth = jQuery('body').width();
    if(windowWidth > 1000) {
        jQuery('body').addClass('full-width');   
    }
    
    //make cart block to sticky
    var bottomSpaceHeight = jQuery('.footer_wrap').outerHeight() + jQuery('.bottom_wrap').outerHeight() + 80;
    jQuery("#onstepcheckout-static-content").sticky({
        topSpacing: 110,
        bottomSpacing: bottomSpaceHeight
    });
});

jQuery(document).ready(function() {
    jQuery(".cart-hold").on("click",".open-click",function() {
        jQuery("#mobile-nav").slideToggle();
    });
    jQuery(".close-click").click(function() {
        jQuery("#mobile-nav").slideToggle();
    });


    jQuery(document).on('click', '.close-cart', function() {
        jQuery("#mini-cart-wrapper-regular").hide(200);
        jQuery("html").removeClass("has-overlay");
        jQuery(".mini-cart-overlay").animate({'opacity':0.0},200,"swing",function(){
            jQuery(this).hide();
        });
    });
    jQuery(document).on('click', '.mini-cart-overlay', function() {
        jQuery(".close-cart").trigger("click");
    });
    jQuery(document).on('click', '.openMiniCart', function() {
        jQuery("#mini-cart-wrapper-regular").show(200);
        jQuery(".mini-cart-overlay").animate({'opacity':0.9},200).show();
        jQuery("html").addClass("has-overlay");
    });



    jQuery("#accordian h3").click(function() {
        //slide up all the link lists
        jQuery("#accordian ul ul").slideUp();
        //slide down the link list below the h3 clicked - only if its closed
        if (!jQuery(this).next().is(":visible")) {
            jQuery(this).next().slideDown();
        }
    })


    jQuery("#catdrop").click(function() {
        jQuery("#catdropcont").slideToggle("slow");
        if (jQuery("#catdrop").hasClass("active")) {
            jQuery("#catdrop").removeClass("active")
        } else {
            jQuery("#catdrop").addClass("active")
        }
    });

    jQuery("#accordian ul.category-menu>li").click(function() {
        if (!jQuery(this).hasClass("active-parent")) {
            jQuery("#accordian ul.category-menu>li:not(" + jQuery(this).index() + ")").removeClass("active-parent");
        }
        jQuery(this).toggleClass("active-parent");
        //jQuery(this).find('ul').mCustomScrollbar();
    });

    // for amaxon-kindle option
    jQuery('#amazon-kindle-option').click(function() {
        if(jQuery(this).is(':checked')) {
            jQuery('#checkout-step-ebookdelivery').slideDown();
            jQuery('#amazon-kindle-option-error').remove();
        } else {
            jQuery('#checkout-step-ebookdelivery').slideUp();
        }
    }).trigger("click");
    
    jQuery('#id_create_account').click(function() {
        jQuery('#amazon-kindle-option').prop('disabled', false);   
        jQuery('#amazon-kindle-option-error').remove();
    });    
    
    
});
jQuery(window).on("load", function() {
    jQuery("#accordian ul.category-menu>li ul").each(function() {
        jQuery(this).mCustomScrollbar();
    });
});