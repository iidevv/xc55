(function () {
  var initialCompiled = false;

  var cache = {};

  var VueLoadableMixin = {
    created: function () {
      // TODO: This code is unreliable since it's not a part of the public API
      Vue.util.defineReactive(this, '$reloading', false);
    },

    beforeMount: function () {
      if (this.$options.loadable && this.$options.loadable.loadOnCompile && !initialCompiled) {
        this.$reload();
        initialCompiled = true;
      }
    },

    methods: {
      $reload: function () {
        var loader = this.$options.loadable.loader;

        if (loader) {
          this.$reloading = true;
          var promise;

          if (this.$options.loadable.cacheSimultaneous) {
            if (!_.has(cache, this._getCacheKey())) {
              cache[this._getCacheKey()] = loader.call(this, arguments);
            }
            promise = cache[this._getCacheKey()];
          } else {
            promise = loader.call(this, arguments);
          }

          if (promise && typeof promise.then === 'function') {
            promise.then(this._resolve, this._reject);
          }
        }
      },

      _getCacheKey: function() {
        return _.isFunction(this.$options.loadable.cacheKey)
          ? this.$options.loadable.cacheKey.apply(this)
          : this.$options.name;
      },

      _resolve: function (data) {
        var uuid = _.uniqueId();
        var self = this;

        xcart.bind(['resources.ready', 'resources.empty'], _.bind(
          function (event, args) {
            if (args.uuid === uuid) {
              if (this.$options.loadable.transferState) {
                var oldData = JSON.parse(JSON.stringify(this.$data));
              }

              if (this.$options.loadable.update !== false) {
                this._updateComponent(data);
              }

              if (this.$options.loadable.transferState) {
                // TODO: doesn't work
                this.$data = oldData;
              }

              this.$reloading = false;

              if ('function' === typeof(this.$options.loadable.resolve)) {
                this.$options.loadable.resolve.apply(this, [data]);
              }

              delete cache[this._getCacheKey()];
            }
          },
          this)
        );

        xcart.parsePreloadedLabels(data, uuid);
        xcart.parseResources(data, uuid);
      },

      _reject: function (data) {
        this.$reloading = false;
        if ('function' === typeof(this.$options.loadable.reject)) {
          this.$options.loadable.reject.apply(this, [data]);
        }

        delete cache[this._getCacheKey()];
      },

      _updateComponent: function (html) {
        var element = this._parseTemplate(html);
        // Disable optimization to avoid static roots are not refreshing
        this.$options.render = Vue.compile(element.outerHTML, {optimize: false}).render;

        var update = typeof(this.$options.loadable.update) === 'function'
          ? this.$options.loadable.update(this)
          : this.$options.loadable.update;

        // Force "initial render" instead of old vnode update (see Vue.prototype._update implementation)
        if (update === true) {
          this._vnode = null;
        }

        this._update(this._render(), false);
      },

      _parseTemplate: function (html) {
        var element;

        if (this.$options.loadable.parser) {
          element = this.$options.loadable.parser.apply(this, [html]);
        } else {
          var temp = document.createElement('div');
          temp.innerHTML = html;

          element = temp.querySelector('[is=' + this.$options._componentTag + '] > *');
          if (!element) {
            element = temp.querySelector(this.$options._componentTag + ' > *');
          }
        }

        return element;
      }
    }
  }

  if (typeof exports === 'object' && typeof module === 'object') {
    module.exports = VueLoadableMixin
  } else if(typeof define === 'function' && define.amd) {
    define(function () { return VueLoadableMixin })
  } else if (typeof window !== 'undefined') {
    window.VueLoadableMixin = VueLoadableMixin
  }
})();

define('vue/vue.loadable', function () { return VueLoadableMixin; });
