/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Constraints
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/constraints', [], function () {

  var END_OF_2038_TIMESTAMP = 2177438399999;

  var withParams = window.validators.helpers.withParams;
  var maxLength = window.validators.maxLength;
  var required = window.validators.required;
  var minValue = window.validators.minValue;

  return {
    Backend: function (params) {
      return function () {
        return true;
      };
    },

    MetaDescription: function (params) {
      return withParams({message: params.message}, function (value, vm) {
        var isCustom = vm[params.dependency.split('.').pop()] === params.dependency_value;
        return !isCustom || required(value);
      });
    },

    DateRange: function (params) {
      return function () {
        return true;
      };
    },

    GreaterThanOrEqual: function (params) {
      return withParams({message: params.message}, minValue(params.value));
    },

    Timestamp: function (params) {
      return withParams({message: params.message.replace('{{ field }} ', '')}, function (value) {
        if (!/\s/.test(value)) {
          value = +value * 1000;

          return 0 <= value && END_OF_2038_TIMESTAMP >= value;
        }

        value = new Date(value);
        if (value instanceof Date && !isNaN(value)) {
          return 0 <= +value && END_OF_2038_TIMESTAMP >= +value;
        }

        return false;
      });
    },

    MaxLength: function (params) {
      return withParams({message: params.message}, maxLength(params.length));
    },

    NotBlank: function (params) {
      return withParams({message: params.message}, required);
    },
  };
});
