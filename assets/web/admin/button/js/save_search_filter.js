/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * 'Save search filter' button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

const saveSearchFilterActions = () => {
  document.querySelector('div.save-search-filter .button-label')
    .addEventListener('click', function () {
      const boxAction = this.nextElementSibling;

      if (boxAction) {
        if (
          boxAction.style.display === ''
          || boxAction.style.display === 'none'
        ) {
          boxAction.style.display = 'block';
        } else {
          boxAction.style.display = 'none';
        }
      }
    });

  document.querySelector('div.save-search-filter .button-action input')
    .addEventListener('keypress', function (event) {
      const clickEvent = new Event('click');
      if (event.keyCode === 13) {
        event.preventDefault();
        this.nextElementSibling.dispatchEvent(clickEvent);
      }
    });
}

xcart.bind('loader.loaded', function () {
  saveSearchFilterActions();
  SearchConditionBox();
  ItemsListQueue();
});

document.addEventListener('DOMContentLoaded', saveSearchFilterActions);
