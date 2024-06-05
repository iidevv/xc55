/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import Vuex from 'vuex';

import initialState from './initialState';
import LicensesModule from './modules/licenses';
import ModuleModule from './modules/module';
import ModulesModule from './modules/modules';
import ScenariosModule from './modules/scenarios';
import UpgradeModule from './modules/upgrade';
import XCartModule from './modules/xcart';
import AppStoreModule from './modules/appstore';
import TechInfoModule from './modules/techInfo';

export default function () {
  return new Vuex.Store({
    state: initialState,
    mutations: {},
    modules: {
      licenses: (new LicensesModule()).createModule(),
      modulesData: (new ModulesModule()).createModule(),
      scenarios: (new ScenariosModule()).createModule(),
      singleModule: (new ModuleModule()).createModule(),
      upgrades: (new UpgradeModule()).createModule(),
      xcart: (new XCartModule()).createModule(),
      appstore: (new AppStoreModule()).createModule(),
      techInfo: (new TechInfoModule()).createModule(),
    },
  });
}
