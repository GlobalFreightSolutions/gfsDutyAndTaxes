define([
    'jquery',
    "underscore",
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'mage/template',
    'mage/storage',
    'gfsAsync!https://maps.googleapis.com/maps/api/js?key=' + gfsGoogleMapsApiKey + '&libraries=places'
], function (
    $,
    _,
    ko,
    Component,
    quote,
    shippingService,
    rateRegistry,
    mageTemplate,
    storage
) {
    'use strict';

    var mixin = {
        /**
         * This method will generate the html used for the gfs widget
         *
         * @param {Object} response
         *
         * @return string
         */
        generateGfsWidgetHtml: function(response)
        {
            if (!window.gfsData.is_calculator_active) {
                return this._super();
            }

            var gfsData = btoa(JSON.stringify(response.data)),
                initialAddress = response.initial_address,
                taxDutyType = response.landed_cost_included,
                taxDuty = response.landed_cost,
                gfsWidgetTemplate = mageTemplate('#gfs-checkout-widget-template');

            return gfsWidgetTemplate({
                data: {
                    'access_token': window.gfsData.accessToken,
                    'currency_symbol': window.gfsData.currency_symbol,
                    'delivery_types': window.gfsData.delivery_types,
                    'standard_delivery_title': window.gfsData.standard_delivery_title,
                    'calendar_delivery_title': window.gfsData.calendar_delivery_title,
                    'drop_point_title': window.gfsData.drop_point_title,
                    'service_sort_order': window.gfsData.service_sort_order,
                    'home_icon': window.gfsData.home_icon,
                    'use_standard': window.gfsData.use_standard,
                    'use_calendar': window.gfsData.use_calendar,
                    'use_drop_points': window.gfsData.use_drop_points,
                    'default_service': window.gfsData.default_service,
                    'default_carrier': window.gfsData.default_carrier,
                    'default_carrier_code': window.gfsData.default_carrier_code,
                    'default_price': window.gfsData.default_price,
                    'default_min_delivery_time': window.gfsData.default_min_delivery_time,
                    'default_max_delivery_time': window.gfsData.default_max_delivery_time,
                    'show_calendar_no_services': window.gfsData.show_calendar_no_services,
                    'calendar_no_services': window.gfsData.calendar_no_services,
                    'day_labels': window.gfsData.day_labels,
                    'month_labels': window.gfsData.month_labels,
                    'disabled_dates': window.gfsData.disabled_dates,
                    'disable_prev_days': window.gfsData.disable_prev_days,
                    'disable_next_days': window.gfsData.disable_next_days,
                    'tax_duty_type': taxDutyType,
                    'tax_duty': taxDuty,
                    'gfs_data': gfsData,
                    'initial_address': initialAddress
                }
            });
        },
    };

    return function (target) {
        return target.extend(mixin);
    };
});