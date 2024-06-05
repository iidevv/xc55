/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var ppr = popup.postprocessRequest;

xcart.bind('afterPopupPlace', function(event) {
  TableItemsListQueue();
  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );
});

xcart.bind('resources.ready', function(event) {
  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );
});

popup.postprocessRequest = function(XMLHttpRequest, textStatus, data, isValid)
{
  ppr.call(this, XMLHttpRequest, textStatus, data, isValid);
  TableItemsListQueue();

  jQuery('.select-attributetypes select').change(
    function () {
      if (jQuery(this).data('value') == jQuery(this).val()) {
        jQuery('.select-attributetypes .form-field-comment').hide();
        jQuery('li.custom-field').show();
      } else {
        jQuery('.select-attributetypes .form-field-comment').show();
        jQuery('li.custom-field').hide();
      }
    }
  );

  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );

  jQuery('.ajax-container-loadable form.attribute', this.base).commonController('submitOnlyChanged', false);
};

CommonForm.elementControllers.push(
  {
    pattern: '.line .input-field-wrapper.switcher.switcher-read-write.input-checkbox-addtonew',
    handler: function () {
      var $this = this;
      var input  = jQuery(':checkbox', this);
      var widget = jQuery('.widget', this);
      var fa     = jQuery(jQuery('.fa', this), '.create-line');
      var widgetOn  = 'fa-check-circle';
      var widgetOff = 'fa-check-circle-o';

      fa.removeClass('fa-power-off')
        .addClass(input.prop('checked') ? widgetOn : widgetOff);

      var checkDisplayAbove = function () {
        var displayAboveCheckbox = widget.closest('.model-properties').find('.display-above-value :checkbox');
        var switchers = widget.closest('.type-S').find('.switcher.input-checkbox-addtonew.enabled');
        if (switchers.length > 1) {
          displayAboveCheckbox.attr('disabled', 'disabled').prop('checked', true);
        } else {
          displayAboveCheckbox.removeAttr('disabled');
        }
      }

      var changeIcon = function(widgetWrapper) {
          var _input  = jQuery(':checkbox', widgetWrapper);
          var _fa     = jQuery(jQuery('.fa', widgetWrapper), '.create-line');

          _fa.removeClass(widgetOn + ' ' + widgetOff);

          if (!_input.prop('checked')) {
              _fa.addClass(widgetOff);
          } else {
              _fa.addClass(widgetOn);
          }

          checkDisplayAbove();
      };

      checkDisplayAbove();

      widget.click(
        function () {
          if (!input.prop('disabled')) {
              var switchers = jQuery(this).closest('.type-H').find('.line .input-field-wrapper.switcher.switcher-read-write.input-checkbox-addtonew.enabled');

              if (!input.prop('disabled')) {
                  jQuery.each(switchers, function (index, elem) {
                      var switcherInput = jQuery(elem).find(':checkbox');
                      if (input.attr('name') !== switcherInput.attr('name')) {
                          switcherInput.click();
                          switcherInput.change();
                          changeIcon(elem);
                      }
                  });
              }

              changeIcon($this);
          }
        }
      );

    }
  }
);

CommonForm.elementControllers.push(
  {
    pattern: '.model-properties .isselectable-value input[name="isSelectable"]',
    handler: function () {
      var $this = jQuery(this);

      var checkDisplayAbove = function () {
        var displayAboveCheckbox = $this.closest('.model-properties').find('.display-above-value :checkbox');
        if ($this.is(':checked')) {
          displayAboveCheckbox.attr('disabled', 'disabled').prop('checked', true);
        } else {
          displayAboveCheckbox.removeAttr('disabled');
        }
      }

      $this.change(function () {
        checkDisplayAbove();
      })

      checkDisplayAbove();
    }
  }
);

jQuery().ready(
  function () {

    jQuery('button.manage-groups').click(
      function () {
        var product_class_id = jQuery(this).parent().data('class-id')
          ? jQuery(this).parent().data('class-id')
          : 0;
        return !popup.load(
          URLHandler.buildURL({
            target:             'attribute_groups',
            product_class_id:   product_class_id,
            widget:             'XLite\\View\\ItemsList\\Model\\AttributeGroup'
          })
        );
      }
    );

    jQuery('button.new-attribute, .items-list.attributes .entity-edit-link a').click(
      function () {
        var product_class_id = jQuery(this).parent().data('class-id')
          ? jQuery(this).parent().data('class-id')
          : 0;
        return !popup.load(
          URLHandler.buildURL({
            target:           'attribute',
            product_class_id: product_class_id,
            id:               jQuery(this).parent().data('id'),
            widget:           'XLite\\View\\Attribute',
            pageId:           1
          })
        );
      }
    );

    xcart.bind(
      'updateattribute',
      function() {
        xcart.bind('popup.close', function (event, data) {
          //cant be implemented without that dirty timeouts cuz popup.close is pseudo
          setTimeout(function() {
            if (!jQuery('.ui-dialog form.attribute').length) {
              self.location.reload();
            }
          }, 1000);
        });
      }
    );

    xcart.bind(
      'updateattributegroups',
      function() {
        self.location.reload();
      }
    );

    xcart.bind('popup.close', function (event, data) {
      setTimeout(function() {
        if (data.box && data.box.find('.edit-attribute-dialog').length > 0 && jQuery('body').find(data.box).length > 0) {
          data.box.dialog('destroy');
          data.box.remove();
        }
      }, 1000);
    });
  }
);
