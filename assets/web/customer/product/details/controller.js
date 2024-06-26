/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

function ProductDetailsController(base)
{
  var o = this;
  ProductDetailsController.superclass.constructor.apply(this, arguments);

  if (
    this.base
    && this.base.get(0)
    && (
      xcart.getCommentedData(jQuery('body'), 'product_id')
      || (
        jQuery('form.product-details', this.base).get(0)
        && jQuery('form.product-details', this.base).get(0).elements.namedItem('product_id')
      )
    )
  ) {
    this.productId = xcart.getCommentedData(jQuery('body'), 'product_id')
      ? xcart.getCommentedData(jQuery('body'), 'product_id')
      : jQuery('form.product-details', this.base).get(0).elements.namedItem('product_id').value;

    this.block = new ProductDetailsView(this.base, this.productId);

    xcart.bind(
      'updateCart',
      function(event, data) {
        var i;

        if (!_.isUndefined(data.items)) {
          for (i = 0; i < data.items.length; i++) {
            if (data.items[i].object_type == 'product' && data.items[i].object_id == o.productId) {
              if (0 < data.items[i].quantity && !jQuery('body').hasClass('added-product')) {
                jQuery('body').addClass('added-product')
                  .removeClass('non-added-product');

              } else if (0 == data.items[i].quantity && jQuery('body').hasClass('added-product')) {
                jQuery('body').addClass('non-added-product')
                  .removeClass('added-product');
              }
            }
          }

          if (!o.selfAdded) {
            for (i = 0; i < data.items.length; i++) {
              if (data.items[i].object_type == 'product' && data.items[i].object_id == o.productId) {
                o.block.load();
              }
            }
          }
        }
      }
    );

    var use = this.base.data('use-widgets-collection');
    if ('undefined' == typeof(use) || use) {
      xcart.callTriggersBind('update-product-page');
    }
  }
}

extend(ProductDetailsController, AController);

// Prodiuct id
ProductDetailsController.prototype.productId = null;

// Controller name
ProductDetailsController.prototype.name = 'ProductDetailsController';

// Find pattern
ProductDetailsController.prototype.findPattern = 'div.product-details';

// Controller associated main widget
ProductDetailsController.prototype.block = null;

// Controller associated buttons block widget
ProductDetailsController.prototype.buttonsBlock = null;

ProductDetailsController.prototype.selfAdded = false;

// Initialize controller
ProductDetailsController.prototype.initialize = function()
{
  this.base.bind(
    'reload',
    _.bind(
      function(event, box) {
        this.bind(box);
      },
      this
    )
  );
};

/**
 * Main widget
 */

function ProductDetailsView (base, productId) {
  this.callSupermethod('constructor', arguments);

  this.productId = productId;

  this.bind('local.loaded', function () {
    xcart.trigger('update-product-page', productId);
  });

  xcart.bind('mm-menu.created', function(event, api){
    if (_.has(jQuery, 'colorbox')) {
      jQuery.colorbox.remove();
    }
  });
  xcart.bind('initialize-product-gallery', _.bind(this.initializeGallery, this));

  this.linkClickHandler = _.bind(
    function(event)
    {
      event.stopPropagation();

      this.showLightbox();
      jQuery('.product-image-gallery li.selected a', this.base).eq(0).trigger('click');

      return false;
    },
    this
  );
}

extend(ProductDetailsView, ALoadable);

// Prodiuct id
ProductDetailsView.prototype.productId = null;

ProductDetailsView.prototype.shadeClass = 'wait-progress overlay-blur-base';
// Widget target
ProductDetailsView.prototype.widgetTarget = 'product';

// Widget class name
ProductDetailsView.prototype.widgetClass = '\\XLite\\View\\Product\\Details\\Customer\\Page\\Main';

// Imgaes gallery
ProductDetailsView.prototype.galleries = null;

// Zoom layer max. width
ProductDetailsView.prototype.zoomMaxWidth = 460;

// Width after which the zoom will be initialized
ProductDetailsView.prototype.zoomWidth = 991;

// Zoom widget
ProductDetailsView.prototype.zoomWidget = false;

// Zoom layer max. width
ProductDetailsView.prototype.kZoom = 1.3;

ProductDetailsView.prototype.shadeWidget = function()
{
  return 0 == this.base.parents('.ui-dialog').length;
};

ProductDetailsView.prototype.preloadHandler = function ()
{
  if (this.hasClass('product-quicklook')) {
    popup.close();
  }
};

ProductDetailsView.prototype.initializeGallery = function ()
{
  var self = this;
  self.galleries = [];
  jQuery('.product-image-gallery', this.base).each(function () {
    self.galleries.push($(this).find('li'));
  });
};

ProductDetailsView.prototype.applyToGalleries = function (callback)
{
  if (this.galleries) {
    var self = this;
    this.galleries.forEach(function (gallery) {
      callback.apply(self, [gallery]);
    })
  }
};

// Postprocess widget
ProductDetailsView.prototype.postprocess = function(isSuccess, initial)
{
  this.callSupermethod('postprocess', arguments);

  if (isSuccess) {
    // Hide popup title
    jQuery(this.base).parents('.ui-dialog').eq(0).addClass('no-title');

    this.initializeGallery();

    var o = this;

    // Bind the cloud zoom triggering event.
    // The element initializes itself the cloud zoom widget
    jQuery('.cloud-zoom', this.base).bind(
      'cloud-zoom',
      function (event) {
        var rel;
        if (jQuery(window).width() > o.zoomWidth) {
          if (jQuery(this).data('zoom')) {
            jQuery(this).data('zoom').destroy();
          }

          var baseRel = _.object(_.map(jQuery(this).data('rel-base').split(','), function (value, key, list) {
            return _.map(_.trim(value).split(':'), function (value) {return _.trim(value);});
          }));
          jQuery(this).attr('data-rel', jQuery(this).data('rel-base'));

          // adjust zoom width and height for current image
          var img = jQuery('img', this);

          if (typeof jQuery(this).CloudZoom !== "undefined") {
            jQuery(this).CloudZoom();
          };

          rel = xcart.getRelArray(jQuery(this));
          rel.zoomWidth = Math.min(img.width(), baseRel.zoomWidth);
          rel.zoomHeight = Math.min(img.height(), baseRel.zoomHeight);
          rel.adjustX  = intval(img.offset().left - jQuery(this).offset().left);

          if (rel.position !== "'inside'") {
            rel.adjustX += 10;
            jQuery(this).closest('.cloud-zoom-photo').addClass('cloud-zoom-outside').removeClass('cloud-zoom-inside');
          }


          xcart.setRelArray(jQuery(this), rel);

          if (typeof jQuery(this).CloudZoom !== "undefined") {
            jQuery(this).CloudZoom();
          };

          jQuery('.image .product-photo img', o.base).css({width: '', height: ''});
        } else {
          jQuery(this).closest('.cloud-zoom-photo').removeClass('cloud-zoom-outside').addClass('cloud-zoom-inside');

          jQuery(this).attr('data-rel', jQuery(this).data('rel-base'));

          if (jQuery(this).data('zoom')) {
            jQuery(this).data('zoom').destroy();
          }

          rel = xcart.getRelArray(jQuery(this));

          var photo = jQuery('.image .product-photo', o.base);
          rel.position = "'inside'";
          rel.adjustX  = intval(
            photo.find('img').offset().left - photo.find('.cloud-zoom').offset().left
          );

          xcart.setRelArray(jQuery(this), rel);

          jQuery(this).CloudZoom();

          jQuery('.image .product-photo img', o.base).css({width: 'auto', height: 'auto'});
        }
      }
    );

    // Arrow-based image navigation
    jQuery('.image .left-arrow', this.base).click(
      function (event) {
        o.switchImage(-1);
      }
    );

    jQuery('.image .right-arrow', this.base).click(
      function (event) {
        o.switchImage(1);
      }
    );

    // Form AJAX-based submit
    var form = this.base.find('form.product-details').get(0);
    if (form) {
      form.commonController
        .enableBackgroundSubmit()
        .bind('local.beforeSubmit', _.bind(this.addProductToCart, this))
        .bind('local.submitted', _.bind(this.postprocessAdd2Cart, this))
    }

    // Cloud zoom
    var cloud = jQuery('.cloud-zoom', this.base);

    if (cloud.length) {
      // The zoom effect is removed from Quick look
      this.zoomWidget = !this.base.hasClass('product-quicklook');

      if (xcart.getCommentedData(cloud, 'kZoom')) {
        this.kZoom = xcart.getCommentedData(cloud, 'kZoom');
      }

      var imageWrapper = jQuery(document.createElement('div')).addClass('wrapper');

      cloud.wrap(imageWrapper);
    }

    // Change Continue shopping button for QuickLook mode
    if (this.base.hasClass('product-quicklook') && 0 < this.base.parents('.blockUI').length) {
      jQuery('button.continue', this.base)
        .unbind('click')
        .removeAttr('onclick');
      jQuery('button.continue', this.base).click(
        function() {
          popup.close();
          return false;
        }
      );
    }

    // Show Lightbox on the image click
    cloud.click(
      function(event) {
        o.showLightbox();
        jQuery('.product-image-gallery li.selected a').eq(0).trigger('click');

        return false;
      }
    );

    // Gallery
    if (typeof(window.lightBoxImagesDir) != 'undefined') {
      jQuery('.loupe', this.base).click(
        function(event) {
          o.showLightbox();
          setTimeout(
            function() {
              jQuery('.product-image-gallery li.selected a').eq(0).trigger('click');
            },
            500
          );

          return false;
        }
      );
    }

    this.hideLightbox();

    // Tabs
    var tabsBase = jQuery('.product-details-tabs', this.base);
    var initialURL = document.location.toString().split('#')[0];

    tabsBase.find('.tabs li a').click(
      _.bind(
        function (event) {
          event.preventDefault();

          var link = jQuery(event.currentTarget);
          this.openTab(link);

          if (history.replaceState) {
            history.replaceState(null, null, initialURL + '#' + link.data('id'));

          } else {
            self.location.hash = link.data('id');
          }
        },
        this
      )
    );

    this.checkLocation();

    if (jQuery('.product-image-gallery li a', this.base).length) {
      // TODO: improve to skip additional JS manipulations
      // like resizing etc when it is not needed
      jQuery('.product-image-gallery li a', o.base).first().click();
    } else if (this.zoomWidget && !cloud.data('zoom')) {
      cloud.trigger('cloud-zoom');
    }

    jQuery('a.link-to-tab').click(
      _.bind(
        function (event) {
          self.location = event.currentTarget.href;
          this.checkLocation();
        },
        this
      )
    );
  }
};

ProductDetailsView.prototype.openTab = function(link)
{
  var tabsBase = jQuery('.product-details-tabs', this.base);

  link = jQuery(link);
  tabsBase.find('.tabs li').removeClass('active');
  link.parent().addClass('active');

  tabsBase.find('.tabs-container .tab-container').hide();
  tabsBase.find('.tabs-container #' + link.data('id')).show();

  this.triggerVent('tab.open', { widget: this, tab: link });

  if (link.closest('.panel').find('.panel-collapse').length) {
    var pane = link.closest('.panel').find('.panel-collapse').first();
    pane.on('transitionend webkitTransitionEnd oTransitionEnd', function () {
      pane.unbind('transitionend webkitTransitionEnd oTransitionEnd');
      $('html, body').animate({
        scrollTop: (link.offset().top - $('#header-area').height())
      },300);
    });

    if (pane.hasClass('in')) {
      setTimeout(function () {
        $('html, body').animate({
            scrollTop: (link.offset().top - $('#header-area').height())
        }, 300);
      }, 0);
      pane.unbind('transitionend webkitTransitionEnd oTransitionEnd');
    }
    pane.collapse('show');
  }
};

ProductDetailsView.prototype.checkLocation = function()
{
    var hash = ((self.location.hash) + '').replace(/^#/, '');
    if (hash) {
      var tabsBase = jQuery('.product-details-tabs', this.base);
      var found = null;

      var state = {
        'widget': this,
        'hash':   hash
      };

      this.triggerVent('tab.hash.resolve', state);
      hash = state.hash;

      tabsBase.find('.tabs li a').each(
        function() {
          var link = jQuery(this);
          if (link.data('id') == hash) {
            found = link;
          } else if (link.data('alt-id') == hash) {
            found = link;
          }
        }
      );

      state = {
        'tab':    found,
        'widget': this,
        'hash':   hash
      };

      this.triggerVent('tab.detection', state);

      if (state.tab) {
        this.openTab(state.tab);
      } else {
        tabsBase.find('a[data-id="' + hash + '"]').first().click();
      }
    } else {
      var tabsBase = jQuery('.product-details-tabs', this.base);
      this.openTab(tabsBase.find('.tabs li a').first());
    }
};

ProductDetailsView.prototype.showLightbox = function()
{
  if (_.has(jQuery, 'colorbox')) {
    jQuery('.product-image-gallery a', this.base)
      .unbind('click')
      .colorbox(this.getColorboxOptions());
  };
};

ProductDetailsView.prototype.hideLightbox = function()
{
  var o = this;

  jQuery('.product-image-gallery a', this.base)
    .unbind('click')
    .bind(
      'click',
      function(event) {
        event.stopPropagation();
        o.selectImage(
          jQuery.inArray(this, jQuery(this).parents('ul').eq(0).find('a').get())
        );

        return false;
      }
    );
};

ProductDetailsView.prototype.getColorboxOptions = function () {
  return {
    onComplete: function () {
      jQuery('#cboxCurrent').css('display', 'none');
      jQuery('#cboxTitle').text(jQuery('img', this).attr('alt'));
    },
    onClosed: _.bind(this.hideLightbox, this),
    maxWidth: jQuery(window).width(),
    maxHeight: jQuery(window).height(),
    title: jQuery('.product-photo img', this.base).attr('alt')
  };
};

// Get base element for shade / unshade operation
ProductDetailsView.prototype.getShadeBase = function () {
  return jQuery('.shade-base', this.base).eq(0);
};

// Image gallery switcher
ProductDetailsView.prototype.switchImage = function (diff) {
  var next = -1;

  this.applyToGalleries(function (gallery) {
    if (next !== -1) {
      return;
    }
    var selected = -1;
    var i = 0;

    // Detect current index
    gallery.each(function () {
      if (selected === -1 && jQuery(this).hasClass('selected')) {
        selected = i;
      }
      i++;
    });

    if (selected === -1) {
      selected = 0;
    }

    // Calculate new position
    next = selected + diff;

    if (next < 0) {
      next = gallery.length - Math.abs(next) % gallery.length;
    } else if (next >= gallery.length) {
      next = next % gallery.length;
    }
  });

  this.selectImage(next);
};

// Select image from gallery
ProductDetailsView.prototype.selectImage = function (pos) {
  this.applyToGalleries(function (gallery) {
    gallery.removeClass('selected');

    // Refresh main image and another options + cloud zoom plugin restart
    var next = gallery.eq(pos);
    next.addClass('selected');

    if (this.zoomWidget) {
      var cloud = jQuery('.cloud-zoom', this.base);

      if (cloud.data('zoom')) {
        cloud.data('zoom').destroy();
      } else {
        cloud.unbind('click', this.linkClickHandler);
      }

      cloud.attr('href', jQuery('a', next).attr('href'));
    }

    var middle = jQuery('img.middle', next).eq(0);
    if (middle.length > 0) {
      var src = middle.attr('src') || null;
      var srcset = middle.attr('srcset') || null;
      var alt = middle.attr('alt') || null;
      jQuery('.image .product-photo img', this.base)
        .hide()
        .attr('src', src)
        .attr('srcset', srcset)
        .attr('alt', alt)
        .show();
    }

    if (jQuery('a', next).length) {
      var params = jQuery('a', next).attr('rev');

      if (this.zoomWidget && params) {
        var tmp = xcart.parseObjectString('{' + params + '}');
        if (
          tmp &&
          (tmp.width > (middle.width()  * this.kZoom)
          || tmp.height > (middle.width()  * this.kZoom))
        ) {
          cloud.trigger('cloud-zoom');
        } else {
          cloud.click(this.linkClickHandler);
        }
      }
    }
  })
};

// Get additional parameters
ProductDetailsView.prototype.getParams = function(params)
{
  params = this.callSupermethod('getParams', arguments);

  params.product_id = this.productId;
  params.added = 1;

  return params;
};

// Form submit handler
ProductDetailsView.prototype.addProductToCart = function(event)
{
  // We do not shade widgets collection after the product is added to cart
  xcart.doShadeWidgetsCollection = false;
  this.base.get(0).controller.selfAdded = true;
  this.gatherFormFields();
};

ProductDetailsView.prototype.gatherFormFields = function()
{
  var elements = jQuery('input:not(:checkbox,:radio),select,textarea,input:checkbox:checked,input:radio:checked', this.base);

  for (i = 0; i < elements.length; i++) {
    if (
      (jQuery(elements[i]).hasClass('form-control')
        || jQuery(elements[i]).parents('.attribute-values').length !== 0
      )
      && 0 == jQuery(elements[i]).parents('form').length
    ) {
      jQuery('<input>').attr({
        type: 'hidden',
        name: jQuery(elements[i]).prop('name'),
        value: jQuery(elements[i]).val()
      }).appendTo('form.product-details');
    }
  }
}

// Form POST processor
ProductDetailsView.prototype.postprocessAdd2Cart = function(event, data)
{
  this.base.get(0).controller.selfAdded = false;

  data.isValid ? this.load() : this.unshade();
};

// Get event namespace (prefix)
ProductDetailsView.prototype.getEventNamespace = function()
{
  return 'block.product.details';
};

xcart.autoload(ProductDetailsController);

xcart.bind('updateCart', (event, data) => {
  let isProductInCart = false;
  const productDetailsBlock = document.querySelector('.product-details.box-product');
  if (data.items && productDetailsBlock) {
    const productId = parseInt(document.querySelector('input[name="product_id"]').value);
    data.items.forEach(item => {
      if (item.object_id === productId) {
        isProductInCart = true;
      }
    });
    productDetailsBlock.classList.toggle('product-added', isProductInCart);
  }
});

xcart.bind(
  'update-product-page',
  function (event, productId)
  {
    xcart.processUpdateWidgetsCollection(
      'update-product-page',
      '\\XLite\\View\\ProductPageCollection',
      {product_id: productId},
      '.product-info-' + productId
    );
  }
);

var resizerTimer;

jQuery(window).resize(
  function (event) {
    clearTimeout(resizerTimer);
    resizerTimer = setTimeout(
      function () {
        jQuery('.cloud-zoom').trigger('cloud-zoom');
      },
      500
    );
  }
);

define("ProductDetails", function () {});
