/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
CleanURLSwitcher = Object.extend({
  constructor: function CleanURLSwitcher(base) {
    this.onFormatChange();

    jQuery('#company-name, #parent-category-path, #object-name, #object-name-in-page-title-order')
        .change(_.bind(this.onFormatChange,this));
  },

  buildTitle: function(options, companyName, categoryPath, titleObjectPart) {
    var title = [];

    if (companyName && options.company) {
      title.push(companyName);
    }

    if (categoryPath && options.category) {
      title.push(categoryPath);
    }

    title.push(titleObjectPart);

    if (options.order == true) {
      title = title.reverse();
    }

    return title.join(options.titleDelimiter)
  },

  onFormatChange: function() {
    var template = _.template("<div class='cleanurls-format'>" +
      "<div class='product'><span><%=productTitle%>: </span><span><%=product%></span></div>" +
      "<div class='category'><span><%=categoryTitle%>: </span><span><%=category%></span></div>" +
      "<div class='static'><span><%=staticTitle%>: </span><span><%=static%></span></div>" +
      "</div>");
    var helpData = xcart.getCommentedData('#clean-url-help-data');

    var options = {
      company: jQuery('#company-name').is(':checked'),
      category: jQuery('#parent-category-path').is(':checked'),
      order:  jQuery('#object-name-in-page-title-order').is(':checked'),
      titleDelimiter: helpData.delimiter
    };

    var data = {
      'product':  this.buildTitle(options, helpData.companyNameLabel, helpData.categoryNameLabel, helpData.productNameLabel),
      'category': this.buildTitle(options, helpData.companyNameLabel, helpData.parentCategoryNameLabel, helpData.categoryNameLabel),
      'static':   this.buildTitle(options, helpData.companyNameLabel, '', helpData.staticPageNameLabel),
      'productTitle':   helpData.productTitle,
      'categoryTitle':  helpData.categoryTitle,
      'staticTitle':    helpData.staticTitle
    };

    var htmlContent = template(data);

    var block = jQuery('.general_options-table .cleanurls-format-help');
    if (block.length === 0) {
      block = jQuery("<li class='cleanurls-format-help'></li>");
      block.insertAfter('.general_options-table .cleanurls-pagetitleformat');
    }

    block.html(htmlContent);
  }

});


xcart.autoload(CleanURLSwitcher, undefined, 'CleanURLSwitcher');
