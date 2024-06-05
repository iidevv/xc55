/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copy link button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ClipboardButton(base)
{
    var clipboard = new ClipboardJS(base.get(0));
    clipboard.on('success', function(e) {
      xcart.trigger('message', {type: 'info', message: xcart.t('The link was copied to your clipboard')});
    });
}

xcart.autoload(ClipboardButton, 'button.copy-link');
