/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Inline form field common controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

let rowId;
let select2Data = {};

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field',
    handler: function () {

      // Field properties and methods
      const field = jQuery(this);
      const obj = this;

      this.viewValuePattern = '.view';
      this.isAffectWholeLine = true;

      const line = field.parents('.line').eq(0);
      const list = line.parents('.items-list').eq(0);
      const row = line.get(0);
      const inputs = jQuery('.field :input', this);

      const vTab = !!list.data('vtab');

      // Get field position into current line
      this.getPositionIntoLine = function()
      {
        const inlineField = this;
        let inlineFieldIndex = 0;
        line.find('.inline-field').each(
          function (index) {
            if (this == inlineField) {
              inlineFieldIndex = index;
            }
          }
        );

        return inlineFieldIndex;
      }

      this.startEdit = function(e) {
        if (
          field.hasClass('editable')
          && (!field.parents('.line').length || !field.parents('.line').hasClass('remove-mark'))
        ) {
          field.trigger('beforeStartEditInline');

          if (row && this.isAffectWholeLine) {
            line.addClass('edit-open-mark');

          } else {
            field.addClass('edit-open-mark');
          }

          const targetSelector = jQuery(e.target)

          const selectField = targetSelector
            .closest('.inline-field.editable')
            .find('select.select2-hidden-accessible');

          if (selectField.length) {
            selectField.select2('open');

            select2Data = {
              edit: true,
              selectField,
              selectRowId: selectField.closest('tr').data('id')
            }
          }

          if (!_.isEmpty(select2Data)) {
            if (
              rowId !== targetSelector.closest('tr').data('id')
              && !field.closest('form').get(0).commonController.isChanged()
            ) {
              jQuery('tr[data-id="'+rowId+'"').removeClass('edit-open-mark');
            }
          }

          jQuery('.field :input', this).first().focus();
          field.trigger('startEditInline');
        }
      }

      // View click effect (show field and hide view)
      jQuery('.view', this).click(_.bind(this.startEdit, this));

      this.getViewValueElements = function()
      {
        return field.find(this.viewValuePattern);
      }

      // Save field into view
      this.saveField = function()
      {
        const value = this.getFieldFormattedValue();

        // undefined value cannot be saved
        if (value !== undefined && "" !== value) {
          const preparedValue = field.data('is-escape') && (_.isUndefined(inputs.data().isEscape) || inputs.data('is-escape'))
            ? htmlspecialchars("" == value ? " " : value, null, null, false)
            : ("" == value ? " " : value);
          const data = {
            'value': preparedValue
          };
          field.trigger('beforeSaveFieldInline', data);
          this.getViewValueElements().html(data.value);
          field.trigger('afterSaveFieldInline', data);

        } else {
          field.trigger('saveEmptyFieldInline');
        }
      };

      let clickTarget;
      let evt;

      jQuery(document).on('click', function (e) {
        evt = e;
        clickTarget = jQuery(e.target).get(0).nodeName;
      });

      this.endEdit = function(noSave)
      {
        if (
          line.hasClass('edit-open-mark')
          || field.hasClass('edit-open-mark')
        ) {
          if (!noSave) {
            this.saveField();
          }

          if (line.hasClass('edit-open-mark')) {
            if (
              rowId === jQuery(row).data('id')
              && jQuery(evt.target).closest('.inline-field.editable').length
            ) {
              return;
            }

            line.removeClass('edit-open-mark');
          }

          if (field.hasClass('edit-open-mark')) {
            field.removeClass('edit-open-mark');
          }

          field.trigger('endEditInline');
        }
      }

      // Get field(s) formatted value (usage as view content)
      this.getFieldFormattedValue = function(input)
      {
        input = input ? jQuery(input).eq(0) : inputs.eq(0);
        let result = '';
        if (input) {
          if (input.is('select')) {
            if (input.is('[multiple]')) {
              return result;
            }
            const elm = input.get(0);
            const option = jQuery(elm.options[elm.selectedIndex]);
            if (option.length) {
              if (option.data('value')) {
                result = option.data('value');

              } else {
                result = elm.options[elm.selectedIndex].text;
              }
            }

          } else {
            result = input.val();
          }
        }

        return result;
      };

      // Sanitize-and-set value into field
      this.sanitize = function()
      {
      };

      // Check - process blur event or not
      this.isProcessBlur = function()
      {
        return true;
      };

      // Field input(s)

      inputs.bind(
        'undo',
        function () {
          field.get(0).saveField();
        }
      );

      // Input blur effect (initialize save fields group)
      inputs.blur(
        function (e) {
          let result = true;

          if (obj.isProcessBlur()) {
            obj.sanitize();
            result = !jQuery(this).validationEngine('validate');

            if (result && row) {
              row.inlineGroupBlurTimeout = setTimeout(
                function () {
                  row.inlineGroupBlurTimeout = false;
                  row.saveFields();
                },
                100
              );
            }

            const endEditTimeoutVal = jQuery(e.target).hasClass('select2-hidden-accessible') ? 100 : 200;

            this.endEditTimeout = setTimeout(function () {
              obj.endEdit();
            }, endEditTimeoutVal);
          }

          return result;
        }
      );

      // Cancel save fields group if focus move to input in this group
      inputs.focus(
        function () {
          if (row) {
            rowId = jQuery(row).data('id');

            if (this.endEditTimeout) {
              clearTimeout(this.endEditTimeout);
            }

            if (row.inlineGroupBlurTimeout) {
              clearTimeout(row.inlineGroupBlurTimeout);
              row.inlineGroupBlurTimeout = false;
            }
          }
        }
      );

      // Input methods
      inputs.each(
        function () {
          const current = this;
          // Get next inputs into thid field
          this.getNextInputs = function()
          {
            let found = false;

            return field.find('.field :input').find(
              function () {
                if (this == current) {
                  found = true;
                }
                return found && this != current;
              }
            );
          };

          // Get previous inputs into thid field
          this.getPreviousInputs = function()
          {
            let found = true;

            return field.find('.field :input').find(
              function () {
                if (this == current) {
                  found = false;
                }

                return found;
              }
            );
          };
        }
      );

      // Move focus to next field in this column (if axists)
      inputs.keydown(
        function (event) {
          const result = {state: true};

          // Press 'Tab' / 'Enter' button
          if (!vTab && (9 === event.keyCode || 13 === event.keyCode) && !$(this).is('textarea')) {
            if (!$(this).is('textarea')) {
              $(this).trigger('enterPress', [event, result]);
            } else if (event.metaKey || event.ctrlKey) {
              $(this).trigger('enterPress', [event, result]);
            }
          } else if (27 === event.keyCode) {
            $(this).trigger('escPress', [event, result]);
          }

          return result.state;
        }
      );

      inputs.bind(
        'enterPress',
        function (currentEvent, event, result) {
          const target = event.shiftKey ? this.getPreviousInputs() : this.getNextInputs();

          if (0 < target.length) {
            // Go to target (next / previous) input into current inline-field box
            target.eq(0).focus();
          } else {
            // Go to similar inline-field into next / previous line
            let l = line;
            let f;

            const cell = jQuery(this).parents('.cell').filter(function () {
              return $(this).parent().hasClass('line');
            }).eq(0);

            const index = cell.index();

            do {
              l = event.shiftKey ? l.prev('.line') : l.next('.line');
              if (l.length) {
                f = l
                  .find('> .cell')
                  .eq(index)
                  .find('.inline-field.editable .view');
              }

            } while (l.length && 0 == f.length);

            if (l.length && f.length) {
              result.state = false;
              f.click();
            } else {
              jQuery(this).trigger('blur');
            }
          }
        }
      );

      inputs.bind(
        'escPress',
        function (currentEvent, event, result) {
          inputs.each(function () {
            const input = $(this);

            if (input.get(0).commonController
              && !line.hasClass('create-line')
            ) {
              input.val(input.get(0).commonController.element.initialValue);
              input.change();
            }
          });

          jQuery(this).trigger('blur');

          result.state = false;
        }
      );

      // Line (fields group) methods

      if (row && typeof(row.saveFields) == 'undefined') {

        row.inlineGroupBlurTimeout = false;

        // Save line fields into views
        row.saveFields = function()
        {
          jQuery('.inline-field', this).each(
            function () {
              this.saveField();
              jQuery(this).removeClass('edit-mark');
            }
          );
          jQuery(this).removeClass('edit-open-mark');
        };

        // Add line hover effect
        line.hover(
          function() {
            jQuery(this).addClass('edit-mark');
          },
          function() {
            jQuery(this).removeClass('edit-mark');
          }
        );
      }
    }
  }
);
