var Checkout = Class.create();

Checkout.prototype = {
    initialize: function (accordion, urls) {
    },
    _onSectionClick: function (event) {
    },
    ajaxFailure: function () {
    },
    reloadProgressBlock: function (toStep) {
    },
    reloadReviewBlock: function () {
    },
    _disableEnableAll: function (element, isDisabled) {
    },
    setLoadWaiting: function (step, keepDisabled) {
    },
    gotoSection: function (section) {
    },
    setMethod: function () {
    },
    setBilling: function () {
    },
    setShipping: function () {
    },
    setShippingMethod: function () {
    },
    setPayment: function () {
    },
    setReview: function () {
    },
    back: function () {
    },
    setStepResponse: function (response) {
    }
}

var Billing = Class.create();
Billing.prototype = {
    initialize: function (form, addressUrl, saveUrl) {
    },
    setAddress: function (addressId) {
    },
    newAddress: function (isNew) {
    },
    resetSelectedAddress: function () {
    },
    fillForm: function (transport) {
    },
    setUseForShipping: function (flag) {
    },
    save: function () {
    },
    resetLoadWaiting: function (transport) {
    },

    nextStep: function (transport) {
    }
}

// shipping
var Shipping = Class.create();
Shipping.prototype = {
    initialize: function (form, addressUrl, saveUrl, methodsUrl) {
    },
    setAddress: function (addressId) {
    },
    newAddress: function (isNew) {
    },
    resetSelectedAddress: function () {
    },
    fillForm: function (transport) {
    },
    setSameAsBilling: function (flag) {
    },
    syncWithBilling: function () {
    },
    setRegionValue: function () {
    },
    save: function () {
    },
    resetLoadWaiting: function (transport) {
    },
    nextStep: function (transport) {
    }
}

// shipping method
var ShippingMethod = Class.create();
ShippingMethod.prototype = {
    initialize: function (form, saveUrl) {
    },
    validate: function () {
    },
    save: function () {
    },
    resetLoadWaiting: function (transport) {
    },
    nextStep: function (transport) {
    }
}

// payment
var Payment = Class.create();
Payment.prototype = {
    beforeInitFunc: $H({}),
    afterInitFunc: $H({}),
    beforeValidateFunc: $H({}),
    afterValidateFunc: $H({}),
    initialize: function (form, saveUrl) {
    },
    addBeforeInitFunction: function (code, func) {
    },
    beforeInit: function () {
    },
    init: function () {
    },
    addAfterInitFunction: function (code, func) {
    },
    afterInit: function () {
    },
    switchMethod: function (method) {
    },
    changeVisible: function (method, mode) {
    },
    addBeforeValidateFunction: function (code, func) {
    },
    beforeValidate: function () {
    },
    validate: function () {
    },
    addAfterValidateFunction: function (code, func) {
    },
    afterValidate: function () {
    },
    save: function () {
    },
    resetLoadWaiting: function () {
    },
    nextStep: function (transport) {
    },
    initWhatIsCvvListeners: function () {
    }
}

var Review = Class.create();
Review.prototype = {
    initialize: function (saveUrl, successUrl, agreementsForm) {
    },
    save: function () {
    },
    resetLoadWaiting: function (transport) {
    },
    nextStep: function (transport) {
    },
    isSuccess: false
}