/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.microhandlers.add(
    'formModel',
    'xlite-form-model',
    function (event, element) {
      define('form_model_start', ['js/vue/vue', 'ready', 'form_model'], function (XLiteVue) {

        if (typeof(Inputmask) !== 'undefined') {
          Inputmask.extendAliases({
            xcdecimal: {
              alias:          "numeric",
              digitsOptional: false,
              groupSeparator: "",
              radixPoint:     "."
            }
          });
        }
      });
    }
);

define('form_model', ['js/vue/vue', 'vue/eventbus', 'js/jquery', 'form_model/sticky_panel', 'form_model/constraints'], function (XLiteVue, EventBus, $, Panel, Validators) {
  XLiteVue.component('xlite-form-model', {
    mixins: [window.vuelidate.validationMixin],

    props: ['formInit'],

    data: function () {
      return {
        original: null,
        form: this.formInit,
        changed: false,
        cleanUrl: {
          model: null,
          cleanUrlTemplate: '',
          cleanUrlSavedValue: '',
          cleanUrlExtension: ''
        },
        vvalidations: {},
        loaded: false,
        formSubmitted: false,
      };
    },

    validations: function () {
      return this.vvalidations || {};
    },

    beforeMount: function () {
      var self = this;
      setTimeout(function () {
        self.original = JSON.parse(JSON.stringify(self.form));
      }, 0);
    },

    created: function () {
      EventBus.$on('form-model-prop-updated', _.bind(function (path, value) {
          var parts = path.replace('[', '.').replace(']', '').split('.');
          var prop = parts.pop();

          var obj = this;
          parts.forEach(function (p) {
            if (obj[p] === undefined && !isNaN(p) && obj[0] !== undefined) {
              Vue.set(obj, p, _.clone(obj[0]));
            }

            if (obj[p] !== undefined) {
              obj = obj[p];
            }
          });

          if (obj[prop] !== undefined) {
            obj[prop] = value;
          }
        }, this)
      );
    },

    mounted: function () {
      new CommonForm(this.$el);
      this.$form = this.$el.commonController.$form[0];
      this.$form.submitted = false;
      this.formSubmitted = false;
      xcart.trigger('vue-form.ready', this.$el);
      this.scrollToError();

      this.loaded = true;

      $('.froala-widget textarea').on('change', this.onFormInputChange);
    },

    directives: {
      validate: {
        inserted: function (el, binding) {
          var vm = Vue.getClosestVueInstance(el);

          if (!vm) {
            return;
          }

          var validators = binding.value;
          var model = '';
          var obj = null;

          for (var validator in validators) {
            if (validators.hasOwnProperty(validator) && !_.isEmpty(validators[validator])) {
              if (Validators[validator]) {
                model = validators[validator].rule.model.split('.');
                obj = vm.vvalidations;

                model.forEach(function (p) {
                  if (obj[p] === undefined) {
                    Vue.set(obj, p, {});
                  }

                  obj = obj[p];
                });

                Vue.set(obj, validator, Validators[validator](validators[validator].rule));
              }
            }
          }

          if (obj) {
            // set watcher for the v-model object to mark inputs as "dirty" explicitly
            vm.$watch(validators[validator].rule.model, function (model) {
              return function (value) {
                var target = _.get(vm.$v, model);
                target.$model = value;
              }
            }(model));
          }
        }
      },

      xliteBackendValidator: {
        inserted: function (el, binding) {
          var vm = Vue.getClosestVueInstance(el);

          if (!vm) {
            return;
          }

          vm.$watch(binding.expression, function () {
            if (el.parentNode) {
              el.parentNode.removeChild(el);
            }
          })
        }
      },

      xliteValidateTrigger: {
        inserted: function (el, binding) {
          var vm = Vue.getClosestVueInstance(el);

          if (!vm) {
            return;
          }

          vm.$watch(binding.expression, function () {
            el.fireEvent('blur');
          });
        }
      }
    },

    methods: {
      isFieldInvalid: function (vmodel) {
        if (!this.loaded) {
          return false;
        }

        var obj = _.get(this.$v, vmodel.replaceAll('[', '.').replaceAll(']', '').split('.'));

        if (!obj) {
          return false;
        }

        return obj.$invalid;
      },

      hasError: function (vmodel) {
        if (!this.loaded) {
          return false;
        }

        var obj = _.get(this.$v, vmodel.replaceAll('[', '.').replaceAll(']', '').split('.'));

        if (!obj) {
          return false;
        }

        return obj.$invalid && (this.formSubmitted || obj.$error);
      },

      isShowError: function (vmodel, validator) {
        if (!this.loaded) {
          return false;
        }

        validator = validator.split('.').pop();
        var obj = _.get(this.$v, vmodel.replaceAll('[', '.').replaceAll(']', '').split('.'));

        if (!obj) {
          return false;
        }

        return !obj[validator] && (this.formSubmitted || obj.$error);
      },

      errorMessage: function (vmodel, validator) {
        validator = validator.split('.').pop();
        var obj = _.get(this.$v, vmodel.replaceAll('[', '.').replaceAll(']', '').split('.'));

        return obj.$params[validator].message;
      },

      onFormInputChange: function (event) {
        var model = event.target.name.replaceAll('[', '.').replaceAll(']', '').split('.'),
            prop = model.pop(),
            obj = _.get(this, model);

        obj[prop] = event.target.value;
      },

      reset: function () {
        var self = this;
        this.$form.submitted = false;
        this.formSubmitted = false;
        this.form = JSON.parse(JSON.stringify(this.original));
        this.$dispatch('form-model-reset', self);

        this.$nextTick(function() {
          self.$v.$reset();
        });
      },

      isChanged: function (model, event) {
        if (this.original === null) {
          return false;
        }

        var result = false;
        for (var sectionName in this.original) {
          for (var fieldName in this.original[sectionName]) {
            if (typeof this.original[sectionName][fieldName] === 'object') {
              var hash1, hash2;

              if (this.form[sectionName][fieldName] instanceof Array) {
                var obj1 = {}, obj2 = {};

                for(var index in this.form[sectionName][fieldName]) {
                  obj1[index] = this.form[sectionName][fieldName][index];
                }

                for(var index in this.original[sectionName][fieldName]) {
                  obj2[index] = this.original[sectionName][fieldName][index];
                }

                hash1 = objectHash.sha1(obj1);
                hash2 = objectHash.sha1(obj2);
              } else {
                hash1 = objectHash.sha1(this.form[sectionName][fieldName]);
                hash2 = objectHash.sha1(this.original[sectionName][fieldName]);
              }

              if (hash1 !== hash2) {
                result = true;
              }
            } else {
              if (this.form[sectionName][fieldName] !== this.original[sectionName][fieldName]) {
                result = true;
              }
            }
          }
        }

        var modified = result && !this.$form.invalid;
        if (this.$form.isBgSubmitting) {
          result = modified;
        }

        this.changed = result;
        return result;
      },

      blockSubmitButton: function () {
        $(this.$el).find('button[type=submit]').addClass('disabled').prop('disabled', true);
      },

      unblockSubmitButton: function () {
        $(this.$el).find('button[type=submit]').removeClass('disabled').prop('disabled', false);
      },

      onSubmit: function (event) {
        var self = this;
        this.$form.submitted = true;
        this.formSubmitted = true;

        if (this.$v.$invalid) {
          this.scrollToError(true);
          this.$form.isBgSubmitting = true;

          event.preventDefault()
        }

        if (!event.defaultPrevented) {
          this.blockSubmitButton();
        }
      },

      scrollToError: function (force) {
        var self = this;
        setTimeout(function () {
          if (force || window.pageYOffset === 0) {
            var firstError = $('.form-row.has-error');
             if (firstError.length === 0) {
               return;
             }

            var statusMsg = $('#status-messages');
            var rowPos = firstError.offset()['top'];

            if (statusMsg) {
              rowPos -= statusMsg.outerHeight(true);
            }
            window.scrollTo(0, rowPos);
            self.$form.isBgSubmitting = false;
          }
        }, 1000);
      }
    },
  });
});
