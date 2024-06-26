/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Top message controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var MESSAGE_INFO    = 'info';
var MESSAGE_WARNING = 'warning';
var MESSAGE_ERROR   = 'error';
var MESSAGE_UPGRADE_MINOR = 'upgrade-minor';
var MESSAGE_UPGRADE_MAJOR = 'upgrade-major';

/**
 * Controller
 */

// Constructor
function TopMessages(container) {
  if (!container) {
    return false;
  }

  this.container = jQuery(container).eq(0);
  if (!this.container.length) {
    return false;
  }

  this.container.get(0).topMessagesController = this;
  this.hiddenCloseMessageButton = this.container.children('a.close-message').hide();

  // Add listeners
  var o = this;

  // Global event
  if ('undefined' != typeof(window.xcart)) {
    xcart.bind(
      'message',
      function(event, data) {
        return o.messageHandler(data.message, data.type);
      }
    );

    xcart.bind(
      'messages',
      function(event, data) {
        for (var i in data) {
          xcart.trigger('message', data[i])
        }
      }
    );

    xcart.bind(
      'clearMessages',
      function(event) {
        o.clearRecords();
      }
    );
  }
  // Remove dump items (W3C compatibility)
  jQuery('li.dump', this.container).remove();

  // Initial show
  if (!this.isVisible() && jQuery('li', this.container).length) {
    setTimeout(
      function() {
        o.show();

        // Set initial timers
        jQuery('li.' + MESSAGE_INFO, o.container).each(
          function() {
            o.setTimer(this);
          }
        );
      },
      1000
    );

  } else {

    // Set initial timers
    jQuery('li.' + MESSAGE_INFO, this.container).each(
      function () {
        o.setTimer(this);
      }
    );
  }

  jQuery('li', this.container).each(
    function () {
      const closeMessageButton = o.hiddenCloseMessageButton.clone().show()[0];
      jQuery(this).append(closeMessageButton);
      o.addEventsToCloseMessageButton(closeMessageButton);
    }
  );
}

/**
 * Properties
 */
TopMessages.prototype.container = null;
TopMessages.prototype.to = null;

TopMessages.prototype.ttl = 10000;

/**
 * Methods
 */

TopMessages.prototype.addEventsToCloseMessageButton = function (closeMessageButton) {
  const o = this;
  jQuery(closeMessageButton)
    .click(
      function(event) {
        event.stopPropagation();
        const currentLi = $(this).closest('li')[0];
        o.hideRecord(currentLi);

        return false;
      }
    )
    .hover(
      function() {
        jQuery(this).addClass('close-hover');
      },
      function() {
        jQuery(this).removeClass('close-hover');
      }
    );
};

// Check visibility
TopMessages.prototype.isVisible = function () {
  return this.container.css('display') != 'none';
};

// Show widget
TopMessages.prototype.show = function () {
  this.container.slideDown();

  if (
    jQuery('body').hasClass('authorized')
    || beforeHeader.length
  ) {
    const beforeHeader = jQuery('#before-header');
    const container = jQuery(this.container);

    const beforeHeaderHeight =
      (beforeHeader.outerHeight() + beforeHeader[0].getBoundingClientRect().top) > 0
        ? (beforeHeader.outerHeight() + beforeHeader[0].getBoundingClientRect().top)
        : 0;
    const headerHeight = jQuery('#header').outerHeight();
    const maxHeight = beforeHeaderHeight + headerHeight + 10;

    if (!container.parent().attr('style')) {
      container.parent().css({
        top: maxHeight
      });
    }

    document.addEventListener('scroll', function () {
      if ((beforeHeader.outerHeight() + beforeHeader[0].getBoundingClientRect().top) > 0) {
        container.parent().css({
          top: beforeHeader.outerHeight() + beforeHeader[0].getBoundingClientRect().top + headerHeight + 10
        });
      } else {
        container.parent().removeAttr('style');
      }
    }, {passive: true});
  }
};

// Hide widget
TopMessages.prototype.hide = function (callback) {
  this.container.hide(0, callback);
};

TopMessages.prototype.getSameRecord = function (ul, text) {
  return ul.find('li').filter(function() {
    var reg = new RegExp(text + " \\\((\\\d*?)\\\)", "i");
    return jQuery(this).text() === text || jQuery(this).text().match(reg);
  }).get(0);
};

TopMessages.prototype.updateRecord = function (li) {
  var recordLi = jQuery(li);
  var array = /(.*) \((\d*?)\)/i.exec(recordLi.text());
  var oldText = array && array[1]
    ? array[1]
    : recordLi.text();
  var oldIndex = array && array[2]
    ? array[2]
    : 0;

  recordLi.text(oldText + ' (' + (intval(oldIndex)+1) + ')');
};

// Add record
TopMessages.prototype.addRecord = function (text, type) {
  if (
    !type
    || (
      MESSAGE_INFO != type
      && MESSAGE_WARNING != type
      && MESSAGE_ERROR != type
      && MESSAGE_UPGRADE_MINOR != type
      && MESSAGE_UPGRADE_MAJOR != type
    )
  ) {
    type = MESSAGE_INFO;
  }

  var ul = jQuery('ul', this.container).length
    ? jQuery('ul', this.container)
    : jQuery(document.createElement('UL')).appendTo(this.container);

  var sameLi = this.getSameRecord(ul, text);
  var li;

  if (sameLi) {
    this.updateRecord(sameLi);
    li = sameLi;
  } else {
    li = document.createElement('LI');
    li.innerHTML = text;
    li.className = type;

    ul.append(li);
  }

  const closeMessageButton = this.hiddenCloseMessageButton.clone().show()[0];
  li.append(closeMessageButton);
  this.addEventsToCloseMessageButton(closeMessageButton);


  if (
    jQuery('li', this.container).length
    && !this.isVisible()
  ) {
    this.show();
  }

  jQuery(li).slideDown('fast');

  if (type == MESSAGE_INFO) {
    this.setTimer(li);
  }
};

// Clear record
TopMessages.prototype.hideRecord = function (li)
{
  if (jQuery('li:not(.remove)', this.container).length == 1) {
    this.clearRecords();

  } else {
    jQuery(li).addClass('remove').slideUp(
      'fast',
      function() {
        jQuery(this).remove();
      }
    );
  }
};

// Clear all records
TopMessages.prototype.clearRecords = function () {
  var container = this.container;
  this.hide(function () {
    jQuery('li', container).remove();
  });
};

// Set record timer
TopMessages.prototype.setTimer = function (li) {
  li = jQuery(li).get(0);

  if (li.timer) {
    clearTimeout(li.timer);
    li.timer = false;
  }

  var o = this;
  li.timer = setTimeout(
    function() {
      o.hideRecord(li);
    },
    this.ttl
  );
};

// onmessage event handler
TopMessages.prototype.messageHandler = function (text, type) {
  this.addRecord(text, type);
};

jQuery(function () {
  new TopMessages(jQuery('#status-messages'));
});
