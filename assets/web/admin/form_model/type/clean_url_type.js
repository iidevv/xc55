/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/type/clean_url_type', ['js/vue/vue', 'form_model'], function (XLiteVue) {

  Inputmask.extendAliases({
    CleanUrl : {
      alias: 'Regex',
      onBeforeMask: function (value, opts) {
        var result = value;

        if (result.length) {
          result = result.replace(new RegExp('^.*/'), '');
          result = result.replace(new RegExp('\\' + opts.extension + '$'), '');
        }

        return result;
      }
    }
  });

  XLiteVue.component('xlite-form-model', {
    methods: {
      getModel: function () {
        var parts = this.cleanUrl.model.replace('[','.').replace(']','').split('.');

        var obj = this;
        parts.forEach(function (p) {
          obj = obj[p];
        });

        return obj;
      },

      getCleanURLResult: function () {
        if (!this.cleanUrl.model) {
          return '';
        }

        return this.cleanUrl.cleanUrlTemplate.replace('#PLACEHOLDER#', '<span class="editable">' + this.getModel().clean_url + '</span>')
      },

      isCleanURLChanged: function () {
        if (!this.cleanUrl.model) {
          return '';
        }

        var savedValue = this.cleanUrl.cleanUrlSavedValue === true ? true : this.cleanUrl.cleanUrlSavedValue.replace(new RegExp('\\' + this.cleanUrl.cleanUrlExtension + '$'), '');

        return savedValue === true || this.getModel().clean_url !== savedValue;
      },

      isCleanUrlAutogenerate: function () {
        if (!this.cleanUrl.model) {
          return true;
        }

        return this.getModel().autogenerate || this.getModel().clean_url === '';
      }
    },

    directives: {
      xliteCleanUrl: {
        inserted: function (el, binding) {
          var vm = Vue.getClosestVueInstance(el);

          if (!vm) {
            return;
          }

          vm.cleanUrl = {
            model: binding.expression,
            cleanUrlTemplate: el.getAttribute('clean-url-template'),
            cleanUrlSavedValue: el.getAttribute('clean-url-saved-value'),
            cleanUrlExtension: el.getAttribute('clean-url-extension')
          };
        }
      }
    }
  });
});
