/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

export default function fetchScenario(store, type) {
  return (to, from, next) => {
    store.dispatch('scenarios/fetch', type);
    next();
  };
}
