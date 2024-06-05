/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * @param element containing the navbar items
 * @constructor
 */
function ProductTabsMenuAutoHide (element) {
  this.$element = $(element);
  this.element = this.$element.get(0);

  this.bindHandlers();

  this.init();
  this.updateMenu();
  setTimeout(_.bind(this.updateMenu, this), 200);
}

ProductTabsMenuAutoHide.prototype.findNotMoreTabElement = function () {
  return this.$element.children(':not(.more)');
}

ProductTabsMenuAutoHide.prototype.findMoreFirstUlElement = function (more) {
  return more.find('ul').first();
}

ProductTabsMenuAutoHide.prototype.bindHandlers = function () {
  const handler = _.bind(this.updateMenu, this);
  $(window).resize(handler);
}

ProductTabsMenuAutoHide.prototype.init = function () {
  let index = 0;
  this.findNotMoreTabElement().each(function () {
    this.menuItemPosition = index++;
  })
  this.$element.closest('.page-tabs').find('ul').css('display', 'inline-block');
}

ProductTabsMenuAutoHide.prototype.sortItems = function (items) {
  items.sort(function (a, b) {
    const ap = a.menuItemPosition;
    const bp = b.menuItemPosition;
    return ((ap < bp) ? -1 : ((ap > bp) ? 1 : 0));
  });

  return items;
};

/**
 * Recalculates more item position and content, hides extra elements, must be triggered on any navbar layout change
 */
ProductTabsMenuAutoHide.prototype.updateMenu = function () {
  let more = this.$element.find('.more');

  if (more.length) {
    this.findMoreFirstUlElement(more).find('> li').appendTo(this.$element);
    more.detach();
  } else {
    more = this.createMoreItem();
  }

  const menuItems = this.$element.children().detach().filter(function () {
    return $(this).find('*').length;
  })

  const containerWidth = this.calculateNavbarWidth();
  this.$element.append(this.sortItems(menuItems));

  this.resetClassesForSubtabs(this.findNotMoreTabElement());

  while (
    this.$element.outerWidth() > containerWidth
    && this.findNotMoreTabElement().length > 2) {
    this.addElementInMoreTab(more);
  }

  if (!this.findMoreFirstUlElement(more).find('> li').length) {
    more.remove();
  } else {
    more.appendTo(this.$element);

    if (this.$element.outerWidth() > containerWidth) {
      this.addElementInMoreTab(more);
    }

    if (
      this.findMoreFirstUlElement(more).find('> li').html() === ''
      && this.findMoreFirstUlElement(more).find('> li').text() === ''
    ) {
      more.remove();
    }
  }

  this.$element.closest('.page-tabs').css('overflow', 'visible');

  if (more.find('.subtab.selected').length > 0) {
    more.removeClass('tab').addClass('tab-current');
  } else {
    more.removeClass('tab-current').addClass('tab');
  }
}

ProductTabsMenuAutoHide.prototype.addElementInMoreTab = function(more) {
  const child = this.findNotMoreTabElement().last();
  this.replaceClassesForElementInMoreTab(child);
  child.prependTo(this.findMoreFirstUlElement(more));
}

ProductTabsMenuAutoHide.prototype.replaceClassesForElementInMoreTab = function(item) {
  if (item.hasClass('tab-current')) {
    item.removeClass('tab-current').addClass('selected');
  }
  item.removeClass('tab').addClass('subtab');
}

ProductTabsMenuAutoHide.prototype.resetClassesForSubtabs = function(items) {
  items.each(function() {
    if ($(this).hasClass('subtab')) {
      $(this).removeClass('subtab')
      if ($(this).hasClass('selected')) {
        $(this).removeClass('selected').addClass('tab-current');
      } else {
        $(this).addClass('tab');
      }
    }
  });
}

ProductTabsMenuAutoHide.prototype.createMoreItem = function () {
  const label_more = xcart.t ? xcart.t('More') : 'More';

  const template = _.template('<li class="tab tabkey-more has-subtabs more">' +
    '<a>' + label_more + '</a>' +
    '<span class="fa fa-angle-down"></span>' +
    '<ul class="subtabs"/>' +
    '<li>');

  return $(template());
}

/**
 * @returns {Number} width
 */
ProductTabsMenuAutoHide.prototype.calculateNavbarWidth = function () {
  const navbar = this.$element.closest('.page-tabs');
  navbar.css('flex-basis', '1%');
  navbar.css('max-width', '100%');
  const width = navbar.innerWidth() - 1;
  navbar.css('flex-basis', 'auto');
  navbar.css('max-width', 'none');

  return width;
}

xcart.autoload(ProductTabsMenuAutoHide, '#main .tabs-container .page-tabs > ul');
