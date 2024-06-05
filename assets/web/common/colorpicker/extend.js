/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Color Picker field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('colorpicker/pickr', function () {
  return Object.extend({
    constructor: function ColorPicker(element, options) {
      this.el = element;
      this.$el = $(this.el);
      this.input = this.$el.is('input') ? this.$el : this.$el.find('input');
      this.options = options ?? {};
      this.picker = null;
      this.inputColor = this.input.val().length > 0 ? '#' + this.input.val() : null;
      this.commentedData = xcart.getCommentedData(this.$el.closest('.input-field-wrapper'));
      this.initialize();
    },

    initialize: function() {
      let self = this;
      this.el.parent().find('.pickr').remove();
      this.pickr = $('<span class="pickr"></span>').insertAfter(this.input);
      this.picker = new Pickr(
        _.extend(self.defaultOptions(), self.options)
      )
      /** fix for case if initial value equal null */
      .on("init", function(instance) {
        let palette = instance._components.palette;
        if (self.paletteIsShown() && !palette.options.element.style.backgroundColor) {
          palette.trigger();
          instance.setColor(null);
        }
        self.triggerEvent('init');
      })
      .on("clear", () => {
        self.syncColor();
        self.change();
        self.hide();
        self.triggerEvent('clear');
      })
      .on("save", (color, instance) => {
        self.change();
        self.hide();
        self.fixForSafari(instance);
        self.triggerEvent('save');
      })
      .on("change", (color, event, instance) => {
        self.triggerEvent('change');
      })
      .on("swatchselect", (color, instance) => {
        instance.setColor(color.toHEXA().toString());
        self.triggerEvent('swatchselect');
      })
      .on("hide", (instance) => {
        self.syncColor();
        self.triggerEvent('hide');
      });
    },

    emptyColor: function () {
      let defaultRgbaColor = 'rgba(0,0,0,0)';
      let button = this.picker.getRoot().button;
      $(button).addClass('clear').css({
        'background-color': defaultRgbaColor,
        'color': defaultRgbaColor
      });
    },

    syncColor: function () {
      let inputValHexColor = this.input.val().length > 0 ? this.input.val() : '';
      let inputValRgbaColor = this.hex2rgba(inputValHexColor);
      let button = this.picker.getRoot().button;
      if (inputValRgbaColor) {
        $(button).css({
          'background-color': inputValRgbaColor,
          'color': inputValRgbaColor
        });
      } else {
        this.emptyColor();
      }
    },

    hex2rgba: (hex, alpha = 1) => {
      let result = null;
      let c;
      hex = hex.replace('#', '');
      if (/^([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
        c = hex.split('');
        if (c.length === 3) {
          c = [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c = '0x' + c.join('');
        result = 'rgba(' + [(c>>16)&255, (c>>8)&255, c&255].join(',') + ',' + alpha + ')';
      }
      return result;
    },

    fixForSafari: instance => {
      let button = instance.getRoot().button;
      button.style.backgroundColor = button.style.color;
    },

    defaultOptions: function () {
      return {
        el: this.pickr[0],
        theme: "classic",
        swatches: this.commentedData.availableColors ?? null,
        defaultRepresentation: "HEX",
        default: this.inputColor,
        comparison: false,
        components: {
          preview: this.paletteIsShown(),
          hue: this.paletteIsShown(),
          interaction: {
            input: true,
            clear: true,
            save: true
          }
        },
        i18n: {
          'btn:save': this.commentedData.save,
          'btn:clear': this.commentedData.clear,
        }
      }
    },

    paletteIsShown: function () {
      return !this.commentedData.availableColors || this.commentedData.availableColors.length === 0;
    },

    show: function () {
      this.picker.show();
    },

    hide: function () {
      this.picker.hide();
    },

    change: function (color = null) {
      color = this.selectedColor(color);
      this.input.val(color).trigger("change");
    },

    selectedColor: function (color = null) {
      let data = color ?? this.picker.getSelectedColor();
      return data
        ? data
          .toHEXA()
          .toString()
          .replace('#', '')
        : null;
    },

    triggerEvent: (event) => {
      xcart.trigger('color-picker.' + event);
    }
  });
});
