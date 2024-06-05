/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// import Vue from 'vue';
import Icon from './components/block/Icon';
import appConfig from '../config/app-config';
import { MODULE_STATES } from '../src/constants';

// eslint-disable-next-line no-undef
const IconString = Vue.extend(Icon);

export function iconAsString(params) {
  return new IconString({
    propsData: params,
  }).$mount().$el.outerHTML;
}

export function notify(type, text, data = [], duration = undefined) {
  let notificationData = data;
  if (typeof data === 'string') {
    notificationData = JSON.parse(data);
  }
  // eslint-disable-next-line no-undef
  Vue.prototype.$notify({
    group: 'system',
    type,
    text,
    notificationData,
    duration: duration || (type === 'danger' ? -1 : 10000),
  });
}

export function openTab(url) {
  const tab = window.open(url);

  if (tab) {
    tab.focus();
  } else {
    // eslint-disable-next-line no-console
    console.error('Tab cannot be opened');
    window.location = tab;
  }
}

export function track(data, name) {
  if (typeof window.analytics !== 'undefined') {
    window.analytics.track(name, data);
  }
}

export function trackPage(to, name) {
  if (!to.matched.some(m => m.name === 'iframe') && typeof window.analytics !== 'undefined') {
    window.analytics.page(name || to.path, {
      path: to.fullPath,
      url: window.location.href,
      search: to.query,
    });
  }
}

export function trackModule(name, module) {
  if (typeof window.analytics !== 'undefined') {
    const moduleId = module.moduleId || module.id;
    window.analytics.track(`"${moduleId}" - ${name}`, {
      moduleId,
      moduleName: module.moduleName,
      author: module.author,
      url: window.location.href,
    });
  }
}

export function getModuleWithIcons(module) {
  const url = appConfig.url;
  const publicDir = appConfig.publicDir !== '' ? `/${appConfig.publicDir}` : '';

  let icon;
  let listIcon;
  let skinPreview;

  if (module.id === 'CDev-Core') {
    icon = `${url + publicDir}/assets/web/admin/images/core_image.png`;
    listIcon = icon;
  } else if (module.hasLocalFiles) {
    icon = `${url + publicDir}/modules/${module.author}/${module.name}/images/icon.png`;
    listIcon = `${url + publicDir}/modules/${module.author}/${module.name}/images/list_icon.png`;

    if (module.type === 'skin') {
      skinPreview = `${url + publicDir}/modules/${module.author}/${module.name}/images/skin_list_image.jpg`;
    }
  } else {
    const addonImagesUrl = appConfig.addonImagesUrl;

    icon = `${addonImagesUrl + module.author}/${module.name}/icon.png`;
    listIcon = `${addonImagesUrl + module.author}/${module.name}/list_icon.jpg`;

    if (module.type === 'skin') {
      skinPreview = `${addonImagesUrl + module.author}/${module.name}/skin_list_image.jpg`;
    }
  }

  return { ...module, icon, listIcon, skinPreview };
}

export function upgradeNoteControl(hasUpgrades) {
  const path = window.location.hash.substring(2).split(/[/|?|&]/g)[0];
  const note = document.querySelector('.upgrade-box');

  if (note && hasUpgrades) {
    if (path === 'upgrade') {
      note.style.display = 'none';
    } else {
      note.style.display = null;
    }
  }
}

function parseDataFromHashAnchor(anchor, fullHash) {
  let obj = {};
  const strIndex = fullHash.indexOf(anchor);
  if (strIndex !== -1) {
    const str = fullHash.slice(strIndex + anchor.length) || '';
    obj = new URLSearchParams(str);
    obj = Object.fromEntries(obj.entries());
  }
  return { obj, strIndex };
}

export function parseModuleScenarioFromUrlHash() {
  const resultSanitizedObj = {};
  let fullHash = window.location.hash || '';
  let minHashEndPosition = 0;

  const { obj: parsedErrCodeObj, strIndex: errCodeStrIndex } = parseDataFromHashAnchor('#err_code=', fullHash);
  if (errCodeStrIndex !== -1) {
    minHashEndPosition = errCodeStrIndex;
    fullHash = fullHash.slice(0, errCodeStrIndex);
  }

  // should be in the end
  const { obj: parsedScenarioObj, strIndex: scenarioIdStrIndex } = parseDataFromHashAnchor('#scenario=', fullHash);
  if (scenarioIdStrIndex !== -1) {
    minHashEndPosition = scenarioIdStrIndex;
    fullHash = fullHash.slice(0, scenarioIdStrIndex);
  }
  // see service-tool/src/Controller/MarketModuleInstall.php
  const { obj: parsedModulesObj, strIndex: modulesStrIndex } = parseDataFromHashAnchor('#modules2enable=', fullHash);

  if (modulesStrIndex !== -1) {
    minHashEndPosition = modulesStrIndex;
  }

  Object.entries(parsedModulesObj).forEach((requestedModule) => {
    const moduleId = requestedModule[0];
    const requestedState = requestedModule[1];
    if (
      moduleId.match(/[^- /]+-[^- /]+/gi)
      && (
        requestedState === '0'
        || requestedState === '1'
      )
    ) {
      resultSanitizedObj[moduleId] = (requestedState === '1') ? MODULE_STATES.ENABLED : MODULE_STATES.INSTALLED;
    }
  });

  if (minHashEndPosition > 0) {
    const originalHash = fullHash.slice(0, minHashEndPosition) || '';
    history.replaceState('', document.title, window.location.pathname + window.location.search + originalHash);
  }

  return {
    modulesToToggle: resultSanitizedObj,
    scenarioObj: parsedScenarioObj,
    errCodeObj: parsedErrCodeObj,
  };
}

export function renewLicensesUrl(expiredLicenses) {
  if (!expiredLicenses) {
    return '';
  }

  const currentUrl = window.location.href;

  let commonParams = {
    target: 'generate_invoice',
    action: 'buy',
    proxy_checkout: 1,
    inapp_return_url: `${appConfig.url}/service.php/after-purchase-of-renewals?returnUrl=${encodeURIComponent(currentUrl)}`,
  };

  expiredLicenses.forEach((license, index) => {
    commonParams[`add_${index + 1}`] = license.prolongKey;
    commonParams[`lickey_${index + 1}`] = license.keyValue;
  });

  commonParams = new URLSearchParams(commonParams);

  return `https://${appConfig.xbHost}/customer.php?${commonParams.toString()}`;
}
