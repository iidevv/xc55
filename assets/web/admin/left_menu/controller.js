/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Left menu controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function LeftMenu() {
  var self = this;
  this.accordion = true;

  this.$menu = jQuery('#leftMenu');
  this.$body = jQuery('body');

  if (jQuery.cookie('left-menu-state') == 'expanded') {
    this.expand();
  } else {
    this.compress();
  }

  this.$body.on('mouseenter', '#leftMenu.compressed .left-menu-container > ul > li', function () {
    const box = $('.box', this);
    if (box.length) {
      self.correctPosition(box);
    }
  });

  this.scrollTop = window.scrollY;

  jQuery(window)
    .scroll(_.partial(_.bind(this.recalculatePosition, this), undefined))
    .resize(_.partial(_.bind(this.recalculatePosition, this), undefined));

  this.$menu
    .mouseenter(function () {
      jQuery('body').addClass('left-menu-hover');
    })
    .mouseleave(function () {
      jQuery('body').removeClass('left-menu-hover');
    });

  jQuery('.left-menu-ctrl').bind('click', _.bind(this.toggleMenu, this));

  jQuery('.menu .link', this.$menu).filter(function () {
    return jQuery(this).parent().nextAll('.box').length > 0;
  }).each(function () {
    const $box = jQuery(this).parent().nextAll('.box');

    setTimeout(function () {
      $box.css('transition', 'opacity .25s ease-in-out, height .25s ease-in-out');
    }, 300);

  }).bind('click', function (e) {
    e.preventDefault();
    if (!self.$menu.hasClass('compressed')) {
      self.toggleItem(jQuery(this).closest('li'));
    }
    return false;
  });

  if (
    xliteConfig.target === 'apps'
    && window.location.hash !== '#/version'
  ) {
    self.showItem(jQuery('.menu-item.extensions'));
    const path = window.location.hash.substring(2).split(/[/|?|&]/)[0];

    jQuery('.menu-item.' + path).addClass('active');

    jQuery('.menu-item.extensions').find('li').on('click', function () {
      jQuery('.menu-item.extensions').find('li').removeClass('active');
      jQuery(this).addClass('active');
    });
  }

  this.recalculatePosition();

  xcart.bind('recalculateLeftMenuPosition', function() {
    self.recalculatePosition()
  });
}

LeftMenu.prototype.recalculatePosition = function () {
  const beforeHeaderHeight = jQuery('#before-header').outerHeight();
  const scrollPos = window.scrollY;

  if (beforeHeaderHeight > scrollPos) {
    this.$menu.css('top', beforeHeaderHeight - scrollPos);
  } else {
    this.$menu.css('top', 0);
  }
};

LeftMenu.prototype.toggleItem = function (element) {
  if (element.hasClass('expanded')) {
    this.hideItem(element);
  } else {
    if (this.accordion) {
      const self = this;

      jQuery('.menu-item', this.$menu).each(function () {
        const item = jQuery(this);
        self.hideItem(item);
      });
    }

    this.showItem(element);
  }
};

LeftMenu.prototype.hideItem = function (element) {
  element
    .removeClass('expanded')
    .addClass('collapsed');

  xcart.trigger('layout.sidebar.changeHeight');
};

LeftMenu.prototype.showItem = function (element) {
  element
    .addClass('expanded')
    .removeClass('collapsed');

  xcart.trigger('layout.sidebar.changeHeight');
};

LeftMenu.prototype.toggleMenu = function () {
  if (this.$body.hasClass('left-menu-compressed')) {
    this.expand();
  } else {
    this.compress();
  }

  return false;
};

LeftMenu.prototype.compress = function () {
  const box = jQuery('.menu div.box', this.$menu);
  box.hide();

  setTimeout(function () {
    box.show();
  }, 250);

  this.$menu
    .removeClass('expanded')
    .addClass('compressed');

  this.$body
    .removeClass('left-menu-expanded')
    .addClass('left-menu-compressed');

  jQuery.cookie('left-menu-state', 'compressed');
  xcart.trigger('left-menu-compressed');
};

LeftMenu.prototype.expand = function () {
  this.$menu
    .removeClass('compressed')
    .addClass('expanded');

  this.$body
    .removeClass('left-menu-compressed')
    .addClass('left-menu-expanded');

  jQuery.cookie('left-menu-state', 'expanded');
  jQuery('.box', this.$menu).css('top', 0);
};

LeftMenu.prototype.correctPosition = function (box) {
  box.css({
    'top': 0
  });

  const boxTop = box.offset().top;
  const boxBottom = boxTop + box.outerHeight();

  const viewportTop = window.scrollY;
  const viewportBottom = viewportTop + document.documentElement.offsetHeight;

  if (boxBottom > (viewportBottom - 10)) {
    box.css('top', viewportBottom - boxBottom - 5);
  }
};

xcart.autoload(LeftMenu);
