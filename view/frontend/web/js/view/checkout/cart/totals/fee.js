define([
    'ko',
    'JustShout_GfsLandedCost/js/view/checkout/summary/fee'
], function (
    ko,
    Component
) {
    'use strict';

    return Component.extend({
        isDisplayed: function ()
        {
            return window.checkoutConfig.landed_fee_show;
        }
    });
});
