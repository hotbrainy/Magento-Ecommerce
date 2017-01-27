Validation.addAllThese(
    [
        [
            'validate-amshopby-symbols',
            'Please make sure that value in this field is different from value in field below.',
            function (v) {
                return v != $('amshopby_seo_option_char').value;
            }
        ]
    ]
);
