/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Browser server button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// New Delete category popup button widget constructor
function PopupButtonBrowseServer()
{
  PopupButtonBrowseServer.superclass.constructor.apply(this, arguments);
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonBrowseServer, PopupButton);

// New pattern is defined
PopupButtonBrowseServer.prototype.pattern = '.browse-server-button';

var browseServerOptions = {
  minWidth: 860,
  minHeight: 300
}

PopupButtonBrowseServer.prototype.options = browseServerOptions;

PopupButtonBrowseServer.prototype.loadDialog = function (browseServerObject, link, catalog)
{
  return loadDialogByLink(
    link,
    URLHandler.buildURL({
      'target'  : 'browse_server',
      'widget'  : '\\XLite\\View\\BrowseServer',
      'catalog' : catalog
    }),
    browseServerOptions,
    function (selectorCallback, linkCallback) {
      PopupButtonBrowseServer.prototype.callback.call(browseServerObject, selectorCallback, linkCallback);
    }
  );
}

// Decorating of callback of new class for POPUP widget
decorate(
  'PopupButtonBrowseServer',
  'callback',
  function (selector, link)
  {
    var browseServerObject = this;

    // Store fileinfo structure
    var fileInfo = xcart.getCommentedData('.up-level');

    // Delete categories popup dialog has 'back-button' button with defined action.
    // We change this action to 'popup dialog close' action.
    jQuery('.back-button').each(
      function () {
        jQuery(this).bind(
          'click',
          function () {

            // Close the current popup
            link.linkedDialog = undefined;
            jQuery(selector).dialog('close').remove();

            // Open "File select" dialog
            lastFileSelectorButton.click();
          }
        );
      }
    );

    // Double click event on UP LEVEL element.
    jQuery('a.up-level').each(
      function () {
        if (fileInfo.current_catalog != "") {
          jQuery(this).parent().bind(
            'dblclick',
            function () {

              // Close the previous popup window
              link.linkedDialog = undefined;
              jQuery(selector).dialog('close').remove();

              // Open new popup window. "Catalog" parameter is taken from "UP CATALOG" value
              return PopupButtonBrowseServer.prototype.loadDialog.call(this, browseServerObject, link, fileInfo.up_catalog);
            }
          );
        } else {
          jQuery(this).parent().addClass('not-file-entry');
        }
      }
    );

    jQuery('.fs-entry a').each(
      function () {
        var entry = this;

        jQuery(this).parent().bind(
          // Selected entry by "one-mouse-click" gets "selected" CSS class
          'click',
          function () {
            jQuery('.fs-entry').removeClass('selected');
            jQuery(entry).parent().addClass('selected');
          }
        ).bind(
          // "double-click" event
          // AJAX-browse in file system of local server
          'dblclick',
          function () {
            var entryName = fileInfo.current_catalog + '/' + entry.title;

            // Close previous popup
            link.linkedDialog = undefined;
            jQuery(selector).dialog('close').remove();

            if (jQuery(entry).hasClass('type-catalog')) {
              // Catalog entry clicking opens new popup
              return PopupButtonBrowseServer.prototype.loadDialog.call(this, browseServerObject, link, entryName);

            } else {
              // File entry is sent to "File select" dialog.
              // openDialog(popup.previousPopup.box, popup.previousPopup.additionalOptions);
              decorate(
                'PopupButtonFileSelector',
                'callback',
                function (selector)
                {
                  arguments.callee.previousMethod.apply(this, arguments);
                  jQuery('#file_select_local').click();
                  jQuery('#local-server-file').focus().val(entryName.replace(/\\/g, '/'));
                }
              );
              lastFileSelectorButton.click();
            }
          }
        );

        jQuery('.browse-selector-actions .choose-file-button').bind(
          'click',
          function () {
            var entry = jQuery('.fs-entry.selected');

            if (jQuery('a.type-file', entry).length > 0) {
              decorate(
                'PopupButtonFileSelector',
                'callback',
                function (selector)
                {
                  arguments.callee.previousMethod.apply(this, arguments);
                  jQuery('#file_select_local').click();
                  jQuery('#local-server-file').focus().val(
                    fileInfo.current_catalog + '/' + jQuery('a.type-file', entry).attr('title').replace(/\\/g, '/')
                  );
                }
              );
              lastFileSelectorButton.click();
            }
          }
        );
      }
    );

  }
);

// Autoloading new POPUP widget
xcart.autoload(PopupButtonBrowseServer);
