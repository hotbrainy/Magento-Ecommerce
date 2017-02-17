function amshopby_slider_ui_update_values( prefix, values ,slider ) {
    var parent     = slider.parents('ol');
    var sliderFrom = parent.find('#' + prefix + '-from');
    var sliderTo   = parent.find('#' + prefix + '-to');

    if(sliderFrom && sliderTo){
        sliderFrom.html(values[0])
        sliderTo.html(values[1])
    }

    sliderFrom = parent.find('#' + prefix + '-from-slider');
    sliderTo   = parent.find('#' + prefix + '-to-slider');
    if(sliderFrom && sliderTo){
        sliderFrom.html(values[0])
        sliderTo.html(values[1])
    }
}

function amshopby_slider_ui_apply_filter( evt, values, slider) {
    if (evt && evt.type == 'keypress' && 13 != evt.keyCode)
        return;

    var prefix = 'amshopby-price';

    if (typeof(evt) == 'string'){
        prefix = evt;
    }

    var a = prefix + '-from';
    var b = prefix + '-to';

    var url =  $amQuery('#' + prefix + '-url').val().replace(a, values[0]).replace(b, values[1]);
    amshopby_set_location(url);
}

function amshopby_slider_ui_init(from, to, max, prefix, min, step, uiParamElement) {

    var slider = $amQuery( uiParamElement ).siblings('.amshopby-slider-ui');
    if(!slider || typeof slider == 'undefined'){
        slider = $amQuery('#' + prefix + '-ui');
    }

    from = from ? from : min;
    to = to ? to : max;

    if (slider) {
        slider.slider({
            range: true,
            min: parseFloat(min),
            max: parseFloat(max),
            step: parseFloat(step),
            values: [parseFloat(from), parseFloat(to)],
            slide: function (event, ui) {
                amshopby_slider_ui_update_values(prefix, ui.values, slider);
            },
            change: function (event, ui) {
                if (ui.values[0] != from || ui.values[1] != to) {
                    amshopby_slider_ui_apply_filter(prefix, ui.values, slider);
                }
            }
        });
    }
}

function amshopby_jquery_init () {
    $amQuery('.amshopby-slider-ui-param').each(function() {
        var params = this.value.split(',');
        amshopby_slider_ui_init( params[0], params[1], parseFloat(params[2]), params[3], parseFloat(params[4]), params[5], this );
    });
}

(function ($) {
    $('document').ready(function () {
        amshopby_jquery_init();
    });
})($amQuery);