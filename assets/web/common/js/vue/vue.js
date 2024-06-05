/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('vue/vue', function () { return Vue; });

if ('undefined' !== typeof(Vuex)) {
  Vue.use(Vuex);
  define('vue/vuex', function () { return Vuex; });
}

define('js/vue/vue', ['vue/vue', 'vue/eventbus', 'js/vue/component'], function (Vue, EventBus, XLiteVueComponent) {
  Vue.getClosestVueInstance = function (el) {
    var _getClosestVueInstance = function (element) {
      if (element) {
        return element.__vue__ || _getClosestVueInstance(element.parentElement);
      }
    }

    return _getClosestVueInstance(el);
  };

  Vue.directive('data', {
    inserted: function (el, binding) {
      var vm = Vue.getClosestVueInstance(el);

      if (!vm) {
        return;
      }

      var object = binding.value;
      for (var key in object) {
        var parts = key.replace('[','.').replace(']','').split('.');
        var path = '';

        parts.forEach(function (part) {
          if (path) {
            path = isNaN(part)
              ? path.concat('.' + part)
              : path.concat('[' + part + ']');
          } else {
            path = part;
          }

          // TODO: doesn't work since we don't have this/self and $get method anymore
          /*if (_.isUndefined(self.vm.$get(path))) {
            self.vm.$set(path, {});
          }*/
        });

        if (vm[key] !== undefined) {
          vm[key] = object[key];
        }
      }
    },

    // for loadable components only
    update: function (el, binding) {
      var vm = Vue.getClosestVueInstance(el);

      if (!vm || vm.$options.loadable === undefined) {
        return;
      }

      var object = binding.value;
      for (var key in object) {
        if (vm.$options.loadable.ignoreUpdates && _.contains(vm.$options.loadable.ignoreUpdates, key)) {
          continue;
        }

        if (vm[key] !== undefined && !_.isEqual(vm[key], object[key])) {
          vm[key] = object[key];
        }
      }
    }
  });

  function XLiteVue() {
    this.root = null;
  }

  XLiteVue.prototype.components = {};

  XLiteVue.prototype.start = function (element) {
    for (var componentName in this.components) if (this.components.hasOwnProperty(componentName)) {
      Vue.component(componentName, this.components[componentName].definition)
    }

    if (_.isEmpty(this.components)) {
      return;
    }

    var elementToInit = element || '#page-wrapper';

    if (element instanceof jQuery && element.length > 0) {
      elementToInit = element.get(0);
    }

    this.root = new Vue({el: elementToInit});

    Vue.nextTick(function () {
      typeof CommonForm !== 'undefined' && CommonForm.autoload();
      typeof StickyPanel !== 'undefined' && StickyPanel.reload();
      xcart.microhandlers.runInitial();
    });
  };

  XLiteVue.prototype.component = function (name, definition) {
    if (this.components[name]) {
      this.components[name].extend(definition);
    } else {
      this.components[name] = new XLiteVueComponent(name, definition);
    }

    return this.components[name].definition;
  };

  Vue.prototype.$dispatch = function (event) {
    EventBus.$emit.apply(EventBus, arguments);
  };

  Vue.prototype.$broadcast = function (event) {
    EventBus.$emit.apply(EventBus, arguments);
  };

  Vue.mixin({
    created: function () {
      if (typeof this.$options.events !== 'undefined') {
        var self = this;
        for (var eventName in this.$options.events) {
          if (this.$options.events.hasOwnProperty(eventName)) {
            (function (_eventName) {
              EventBus.$on(_eventName, _.bind(function () {
                this.$options.events[_eventName].apply(this, arguments);
              }, self));
            })(eventName);
          }
        }
      }
    },

    mounted: function () {
      if (typeof this.$options.ready !== 'undefined') {
        this.$nextTick(function () {
          this.$options.ready.call(this);
        });
      }
    },
  })

  return new XLiteVue();
});

jQuery(document).ready(function () {
  define('xlite_vue_model_start', ['js/vue/vue', 'ready'], function (XLiteVue) {
    if ('admin' === xliteConfig.zone) {
      XLiteVue.start();
    }
  });
});

