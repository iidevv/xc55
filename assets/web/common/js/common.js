/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
var URLHandler = {

  excluded: {
    'base': true
  },
  baseURLPart: '',
  querySeparator: '?',
  argSeparator: '&',
  nameValueSeparator: '=',

  // Return query param
  getParamValue: function (name, params) {
    return name
      + this.nameValueSeparator
      + encodeURIComponent(typeof params[name] === 'boolean' ? Number(params[name]) : params[name]);
  },

  // Get param value for the remained params
  getQueryParamValue: function (name, params) {
    return URLHandler.getParamValue(name, params);
  },

  // Build HTTP query
  implodeParams: function (params, method) {
    result = '';
    isStarted = false;

    for (x in params) {

      if (isStarted) {
        result += this.argSeparator;
      } else {
        isStarted = true;
      }

      result += method(x, params);
    }

    return result;
  },

  // Implode remained params
  implodeQueryParams: function (params) {
    return this.implodeParams(params, this.getQueryParamValue);
  },

  // Unset some params
  clearParams: function (params, excluded) {
    // clone object
    var result = {};

    for (key in params) {
      if (params[key] !== undefined && params[key] !== null && !(key in excluded)) {
        result[key] = params[key];
      }
    }

    return result;
  },

  preprocessParams: function (params) {
    return this.clearParams(params, this.excluded);
  },

  // Get base url
  buildBaseUrl: function (params) {
    var base = params.base || this.baseURLPart;

    if (xliteConfig.clean_url && base === xliteConfig.clean_urls_base) {
      return '';
    }

    return params.base || this.baseURLPart;
  },

  // Compose query params
  buildQueryParams: function (params) {
    return this.querySeparator + this.implodeQueryParams(this.preprocessParams(params));
  },

  getBuildURLPrefix: function (params) {
    return xliteConfig.ajax_prefix && !(params && params.base === xliteConfig.admin_script)
      ? (xliteConfig.ajax_prefix + '/')
      : '';
  },

  // Compose URL
  buildURL: function (params) {
    return this.getBuildURLPrefix(params) + this.buildBaseUrl(params) + this.buildQueryParams(params);
  }
};

// Dialog

// Abstract open dialog
function openDialog(selector, additionalOptions) {
  additionalOptions = additionalOptions || {};

  var box = jQuery(selector);

  _.each(
    ['h2', 'h1'],
    function (tag) {
      var elm = box.find(tag);
      if ('undefined' == typeof(additionalOptions.title) || !additionalOptions.title) {
        additionalOptions.title = elm.html();
      }
      elm.remove();
    }
  );

  popup.isLoading = false;
  return popup.open(jQuery(selector), additionalOptions);
}

// Loadable dialog
function loadDialog(url, dialogOptions, callback, link, $this) {
  openWaitBar();

  var selector = 'tmp-dialog-' + (new Date()).getTime() + '-' + jQuery(link).attr('class').toString().replace(/[ \.]/g, '-');

  xcart.get(
    url,
    function (xhr, status, data) {
      if (data) {
        var div = jQuery(document.body.appendChild(document.createElement('div'))).hide();

        var uuid = _.uniqueId();

        xcart.bind(['resources.ready', 'resources.empty'], _.bind(
          function (event, args) {
            if (args.uuid === uuid) {
              if (1 == div.get(0).childNodes.length) {
                div = jQuery(div.get(0).childNodes[0]);
              }

              // Specific CSS class to manage this specific popup window
              div.addClass(selector);

              // Every popup window (even hidden one) has this one defined CSS class.
              // You should use this selector to manage any popup window entry.
              div.addClass('popup-window-entry');

              openDialog('.' + selector, dialogOptions);

              if (callback) {
                callback.call($this, '.' + selector, link);
              }
            }
          },
          this)
        );

        div.html(jQuery.trim(data));

        xcart.parsePreloadedLabels(div, uuid);
        xcart.parseResources(div, uuid);
      }
    }
  );

  return '.' + selector;
}

// Load dialog by link
function loadDialogByLink(link, url, options, callback, $this) {
  if (!link.linkedDialog || 0 == jQuery(link.linkedDialog).length || jQuery(link).hasClass('always-reload')) {
    link.linkedDialog = loadDialog(url, options, callback, link, $this);

  } else {
    openDialog(link.linkedDialog, options, callback);
  }
}

function openWaitBar() {
  popup.openAsWait();
}

function closeWaitBar() {
  popup.close();
}

// Check for the AJAX support
function hasAJAXSupport() {
  if (typeof(window.ajaxSupport) == 'undefined') {
    window.ajaxSupport = false;
    try {

      var xhr = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
      window.ajaxSupport = xhr ? true : false;

    } catch (e) {
    }
  }

  return window.ajaxSupport;
}

// Check list of checkboxes
function checkMarks(form, reg, lbl) {
  var is_exist = false;

  if (!form || form.elements.length == 0)
    return true;

  for (var x = 0; x < form.elements.length; x++) {
    if (
      form.elements[x].type == 'checkbox'
      && form.elements[x].name.search(reg) == 0
      && !form.elements[x].disabled
    ) {
      is_exist = true;

      if (form.elements[x].checked)
        return true;
    }
  }

  if (!is_exist)
    return true;

  if (lbl) {
    alert(lbl);

  } else if (lbl_no_items_have_been_selected) {
    alert(lbl_no_items_have_been_selected);

  }

  return false;
}

/*
 Parameters:
 checkboxes       - array of tag names
 checkboxes_form    - form name with these checkboxes
 */
function change_all(flag, formname, arr) {
  if (!formname)
    formname = checkboxes_form;
  if (!arr)
    arr = checkboxes;
  if (!document.forms[formname] || arr.length == 0)
    return false;
  for (var x = 0; x < arr.length; x++) {
    if (arr[x] != '' && document.forms[formname].elements[arr[x]] && !document.forms[formname].elements[arr[x]].disabled) {
      document.forms[formname].elements[arr[x]].checked = flag;
      if (document.forms[formname].elements[arr[x]].onclick)
        document.forms[formname].elements[arr[x]].onclick();
    }
  }
}

function checkAll(flag, form, prefix) {
  if (!form) {
    return;
  }

  if (prefix) {
    var reg = new RegExp('^' + prefix, '');
  }
  for (var i = 0; i < form.elements.length; i++) {
    if (
      form.elements[i].type == "checkbox"
      && (!prefix || form.elements[i].name.search(reg) == 0)
      && !form.elements[i].disabled
    ) {
      form.elements[i].checked = flag;
    }
  }
}

/*
 Opener/Closer HTML block
 */
function visibleBox(id, skipOpenClose) {
  var elm1 = document.getElementById('open' + id);
  var elm2 = document.getElementById('close' + id);
  var elm3 = document.getElementById('box' + id);

  if (!elm3) {
    return false;
  }

  if (skipOpenClose) {
    elm3.style.display = (elm3.style.display == '') ? 'none' : '';

  } else if (elm1) {
    if (elm1.style.display == '') {
      elm1.style.display = 'none';

      if (elm2) {
        elm2.style.display = '';
      }

      elm3.style.display = 'none';
      jQuery('.DialogBox').css('height', '1%');

    } else {
      elm1.style.display = '';
      if (elm2) {
        elm2.style.display = 'none';
      }

      elm3.style.display = '';
    }
  }

  return true;
}

/**
 * Attach tooltip to some element on hover action
 */
function attachTooltip(elm, content, forcePlacement, ttl) {
  var placement = 'right';

  elm = jQuery(elm);

  if (
    elm.length
    && (
      (jQuery(window).width() - elm.offset().left) < 200
      || (
        elm.parents('.ui-dialog').length
        && (elm.parents('.ui-dialog').offset().left + elm.parents('.ui-dialog').width() - elm.offset().left) < 200
      )
    )
  ) {
    placement = 'left';
  }
  placement = forcePlacement || placement;

  jQuery(elm).each(
    function () {
      if (isBootstrapUse()) {

        if ('undefined' == typeof(this.tooltipAssigned) || !this.tooltipAssigned) {

          var to;
          var obj = jQuery(this);
          if (undefined === ttl) {
            ttl = 500;
          }
          ;

          var options = {
            html: true,
            title: content,
            placement: placement,
            trigger: 'manual'
          };

          if (elm.data('container')) {
            options['container'] = elm.data('container');
          } else if (elm.parents('.ui-dialog').length > 0) {
            options['container'] = '.ui-dialog';
          }
          ;
          obj.tooltip(options);

          obj.mouseover(
            function () {
              if (to) {
                clearTimeout(to);
                to = null;
              }
              if (!obj.next('.tooltip').length) {
                jQuery(this).tooltip('show');
              }
            }
          );

          obj.mouseout(
            function () {
              to = setTimeout(
                function () {
                  obj.tooltip('hide');
                },
                ttl
              );
            }
          );

          obj.on(
            'shown.bs.tooltip',
            function (event) {
              var next = jQuery(event.currentTarget).next();
              if ('undefined' == typeof(next.get(0).tooltipAssigned) || !next.get(0).tooltipAssigned) {
                next
                  .mouseover(function () {
                    obj.mouseover();
                  })
                  .mouseout(function () {
                    obj.mouseout();
                  });
                next.get(0).tooltipAssigned = true;
              }
            }
          );

          this.tooltipAssigned = true;

        }

      } else {
        jQuery(this).tooltip({
          items: this,
          'content': content
        });

      }

      jQuery(document).on('click', '.tooltip-main .tooltip', function (evt) {
        return false;
      });

      jQuery(document).on('click', '.tooltip-main .tooltip a', function (evt) {
        evt.stopPropagation();
      });
    }
  );
}

/**
 * Wait overlay
 */

function assignWaitOverlay(elem) {
  return createOverlay(elem, 'wait');
}

function unassignWaitOverlay(elem, force) {
  return removeOverlay(elem, force);
}

/**
 * Shade overlay
 */

function assignShadeOverlay(elem) {
  return createOverlay(elem, 'shade');
}

function unassignShadeOverlay(elem, force) {
  return removeOverlay(elem, force);
}

var overlayRegistry = {};
var overlayAttr = 'data-overlay-id';

function getOverlaySelector (elem) {
  return elem.attr(overlayAttr) || generateOverlayId();
}

function generateOverlayId() {
  do {
    var pattern = 'overlay-' + Math.round(Math.random() * 1000000);
    var isUnique = $('[' + overlayAttr + '=' + pattern + ']').length === 0;
  } while (!isUnique);

  return pattern;
}

function createOverlay(elem, type) {
  type = type || 'wait';
  var pattern = getOverlaySelector(elem);

  $.each(elem, function() {
    var elem = $(this);

    if (!_.isUndefined(this.overlay) && this.overlay) {
      unassignWaitOverlay(elem);
    }

    var overlayElement = null;
    if (type === 'wait') {
      overlayElement = jQuery('<div class="wait-block-overlay"><div class="wait-block"><div></div></div></div>');
    } else {
      overlayElement = jQuery('<div class="shade-block-overlay"></div>');
    }

    overlayElement.css({
      width: elem.outerWidth() + 'px',
      height: elem.outerHeight() + 'px'
    });

    // We do not show the overlay if the element has zero width or height (the element is not visible)
    if (0 !== elem.outerWidth() && 0 !== elem.outerHeight()) {
      elem.prepend(overlayElement)
    }

    var leftOffset = elem.offset().left - overlayElement.offset().left;
    var topOffset = elem.offset().top - overlayElement.offset().top;
    overlayElement.css('margin-left', leftOffset + 'px');
    overlayElement.css('margin-top', topOffset + 'px');

    overlayElement.attr('id', pattern);
    elem.attr(overlayAttr, pattern);
    overlayRegistry[pattern] = overlayElement;
    this.overlay = overlayElement;

    elem.trigger('assignOverlay', {widget: elem, type: type});
  });

  return $('#' + pattern);
}

function removeOverlay(elem, force) {
  var pattern = getOverlaySelector(elem);

  $.each(elem, function() {
    var elem = $(this);

    var overlay = null;
    if (pattern && typeof overlayRegistry[pattern] !== "undefined") {
      overlay = overlayRegistry[pattern];
    } else if (force) {
      overlay = $('.wait-block-overlay, .shade-block-overlay');
    }

    if (overlay) {
      overlay.remove();
      elem.attr(overlayAttr, null);
      elem.trigger('unassignOverlay', {widget: elem});
      this.overlay = null;
    }
  });
}

function isBootstrapUse() {
  return 'undefined' != typeof(jQuery.fn.modal)
    && _.isFunction(jQuery.fn.modal);
}

/**
 * State widget specific objects and methods (used in select_country.js )
 */

var StatesList = (function () {
  var instance;

  function createInstance() {
    function StatesListProto() {
    }

    extend(StatesListProto, Object);

    StatesListProto.states = [];
    StatesListProto.stateSelectors = [];
    StatesListProto.forceCustomState = false;
    StatesListProto.forceCustomStateCountries = [];

    StatesListProto.prototype.addStates = function (states) {
      StatesListProto.states = array_merge(this.states, states);
      return this;
    };

    StatesListProto.prototype.getStates = function (country) {
      return StatesListProto.states[country];
    };

    StatesListProto.prototype.getAllStates = function () {
      return StatesListProto.states;
    };

    StatesListProto.prototype.getStatesArray = function (country) {
      var extractStates = function (state) {
        if (state instanceof Object) {
          if (!_.isUndefined(state.name)) {
            return [state.name];
          }

          var result = [];

          if (state instanceof Array) {
            state.map(function (v) {
              result = result.concat(extractStates(v));
            });
          } else {
            Object.keys(state).forEach(function (key) {
              result = result.concat(extractStates(state[key]));
            });
          }

          return result.sort(function (a, b) {
            return a.toLowerCase().localeCompare(b.toLowerCase());
          });
        }

        return [];
      };

      return extractStates(this.getStates(country));
    };

    StatesListProto.prototype.addStateSelector = function (fieldId, stateSelector) {
      StatesListProto.stateSelectors[fieldId] = stateSelector;
      return this;
    };

    StatesListProto.prototype.getStateSelectors = function () {
      return StatesListProto.stateSelectors;
    };

    StatesListProto.prototype.isForceCustomState = function (country) {
      return StatesListProto.forceCustomState
        || StatesListProto.forceCustomStateCountries.indexOf(country) != -1;
    };

    StatesListProto.prototype.updateStatesList = function (base) {
      var _stateSelectors, _forceCustomState, o = this;

      base = base || document;

      if (!jQuery('.country-selector', base).length) {
        o.addStates(window.statesList);
      } else {
        jQuery('.country-selector', base).each(function (index, elem) {
          o.addStates(xcart.getCommentedData(elem, 'statesList'));
          _forceCustomState = xcart.getCommentedData(elem, 'forceCustomState');
          _stateSelectors = xcart.getCommentedData(elem, 'stateSelectors');

          if (_forceCustomState instanceof Array) {
            if (_forceCustomState.indexOf('All') != -1) {
              StatesListProto.forceCustomState = true;
              _forceCustomState.splice(_forceCustomState.indexOf('All'), 1);
            }

            StatesListProto.forceCustomStateCountries = array_merge(
              StatesListProto.forceCustomStateCountries,
              _forceCustomState
            );
          }

          if (_stateSelectors) {
            o.addStateSelector(_stateSelectors.fieldId, new StateSelector(
              _stateSelectors.fieldId,
              _stateSelectors.stateSelectorId,
              _stateSelectors.stateInputId
            ));
          }
        });
      }
    };

    return new StatesListProto;
  }

  return {
    getInstance: function () {
      if (!instance) {
        instance = createInstance();

        instance.updateStatesList();
      }
      return instance;
    }
  };
})();

function setPriceElement(element, value, e) {
  e = e || 2;

  var str = xcart.numberToString(value, '.', '', e);
  var parts = str.split('.');

  // Sign
  if (!element.find('.part-sign').length) {
    if (!element.find('.part-prefix').length) {
      element.find('.part-integer').before('<span class="part-sign"></span>');

    } else {
      element.find('.part-prefix').before('<span class="part-sign"></span>');
    }
  }
  if (value >= 0) {
    element.find('.part-sign').html('');

  } else {
    element.find('.part-sign').html('&minus;&#8197;');
  }

  element.find('.part-integer').html(Math.abs(parseInt(parts[0])));
  if (parts[1]) {
    element.find('.part-decimal').html(parts[1]);

  } else {
    element.find('.part-decimal').html('');
  }
}

function CacheEngine() {
  this.cache = [];
}

CacheEngine.prototype.add = function (key, value) {
  var updated = false;

  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      updated = true;
      this.cache[i].value = value;

      break;
    }
  }

  if (!updated) {
    this.cache.push({key: key, value: value});
  }
};

CacheEngine.prototype.get = function (key) {
  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      return this.cache[i].value;
    }
  }
};

CacheEngine.prototype.has = function (key) {
  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      return true;
    }
  }
};

CacheEngine.prototype.remove = function (key) {
  var index = null;

  for (var i = 0; i < this.cache.length; i++) {
    if (this.cache[i].key === key) {
      index = i;

      break;
    }
  }

  this.cache.splice(index, 1);
};

CacheEngine.prototype.clear = function () {
  this.cache = [];
};

function getPasswordDifficulty(password) {
  var digits = new RegExp('\\d');

  var lowerLetters, upperLetters, specials;
  try {
    lowerLetters = new RegExp('\\p{Ll}', 'u');
    upperLetters = new RegExp('\\p{Lu}', 'u');
    specials = new RegExp('[^\\d\\p{L}]', 'u');
  } catch (e) {
    console.log("Current browser doesn't support regular expressions with unicode");
    lowerLetters = new RegExp('[a-z]');
    upperLetters = new RegExp('[A-Z]');
    specials = new RegExp('[^A-Za-z\\d]');
  }

  var rating = 0;
  [digits, lowerLetters, upperLetters, specials].forEach(function(item) {
    if (item.test(password)) {
      rating++;
    }
  });

  if (password.length >= 6) {
    rating++;
  }

  if (rating < 3) {
    return 'Weak password';
  }

  return (rating < 4) ? 'Good password' : 'Strong password';
}

function showPasswordDifficultyMessage(elem, difficulty) {
  elem.html(xcart.t(difficulty));
}

function setPasswordDifficultyColor(elem, difficulty) {
  if (difficulty == 'Weak password') {
    elem.css("color", "#D4142C");
  } else if (difficulty == 'Good password') {
    elem.css("color", "#F5A623");
  } else if (difficulty == 'Strong password') {
    elem.css("color", "#7ED321");
  }
}

function smartTrim(string, maxLength) {
  if (string.length <= maxLength) {
    return string;
  }

  if (maxLength == 1) {
    return string.substring(0,1) + '...';
  }

  const midpoint = Math.ceil(string.length / 2);
  const toremove = string.length - maxLength;
  const lstrip = Math.ceil(toremove/2);
  const rstrip = toremove - lstrip;

  return string.substring(0, midpoint-lstrip) + '...'
    + string.substring(midpoint+rstrip);
};

jQuery(document).ready(
  function () {
    var isIE11 = !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);

    if (isIE11) {
      jQuery('body').addClass('ie11');
    }

    // Open warning popup
    xcart.microhandlers.add(
      'OverlayHeightResize',
      '>*:first',
      function (event) {
        jQuery('.ui-widget-overlay').css('height', jQuery(document).height());
        jQuery('.ui-widget-overlay').css('width', jQuery('body').innerWidth());
      }
    );

    xcart.microhandlers.add(
      'PopupModelButtonWidthFix',
      '.model-form-buttons',
      function (event) {
        jQuery('.ajax-container-loadable .model-form-buttons')
          .each(function (index, elem) {
            jQuery('.button', elem).width(jQuery(elem).width());
          });
      }
    );

    xcart.microhandlers.add(
      'promo-close',
      '.promo-block .close',
      function (event) {
        $(this).click(function () {
          var block = jQuery(this).parents('.promo-block').get(0);
          var blockId = jQuery(block).data('promo-id');
          if (0 < blockId.length) {
            blockId = blockId + 'PromoBlock';
            document.cookie = blockId + '=1';
          }
          jQuery(block).hide();
        });
      }
    );

    xcart.microhandlers.add(
      'promo-close-2',
      '.promo-block-close',
      function (event) {
        $(this).click(function () {
          var blockSelector = jQuery(this).data('promo-selector');
          var block = null;

          if (blockSelector) {
            block = jQuery(blockSelector);
          } else {
            block = jQuery(this).parents('.promo-block').get(0)
          }

          var blockId = jQuery(block).data('promo-id');
          if (0 < blockId.length) {
            blockId = blockId + 'PromoBlock';
            document.cookie = blockId + '=1';
          }
          jQuery(block).hide();
        });
      }
    );

    xcart.microhandlers.add(
      'promo-close-2',
      'input.column-selector',
      function (event) {
        $(this).click(function () {
          if (!this.columnSelectors) {
            var idx = jQuery(this).parents('th').get(0).cellIndex;
            var table = jQuery(this).parents('table').get(0);
            this.columnSelectors = jQuery('tr', table)
              .find('td')
              .eq(idx)
              .find(':checkbox');
          }

          this.columnSelectors.prop('checked', this.checked ? 'checked' : '');
        });
      }
    );

    xcart.microhandlers.add(
      'input-file',
      'input.inputfile',
      function (event) {
        $(this).change(function () {
          if (this.files) {
            var filename = '';
            if (this.files.length > 1) {
              filename = this.files.length + " files selected";
            } else {
              filename = this.files[0].name;
            }

            const filenameElement = $(this).siblings('span.input-filename');
            if (filename) {
              filename = filename
                  .replace(/&/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/"/g, "&quot;")
                  .replace(/'/g, "&#039;");
              filenameElement.html(smartTrim(filename, 20));
            }
          }
        });
      }
    );


    if (xliteConfig.zone === 'customer') {
      xcart.microhandlers.add(
        '.profile-form',
        'input#password',
        function (event) {
          $(this)
            .last()
            .on('input', function () {
              var difficulty = getPasswordDifficulty($(this).val());

              var form = $(this).closest('form');
              var formId = form.attr('id')
              var passwordDifficultyElem = $(".password-difficulty[form-id=" + formId + "]");

              if (passwordDifficultyElem.length == 0) {
                form.find('.password-label').append(
                  "<span class='password-difficulty' form-id='" + formId + "'></span>"
                );
                passwordDifficultyElem = $(".password-difficulty[form-id=" + formId + "]");
              }


              showPasswordDifficultyMessage(passwordDifficultyElem, difficulty);
              setPasswordDifficultyColor(passwordDifficultyElem, difficulty);
            });
        }
      );
    }

    xcart.bind('popup.open', function () {
      jQuery('html').addClass('popup-opened');
    });
    xcart.bind('popup.close', function () {
      jQuery('html').removeClass('popup-opened');
    });
  });
