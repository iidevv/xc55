/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import appConfig from '../../config/app-config';

/* eslint-disable import/prefer-default-export */
export const fetchStoreInfo = () => fetch(`${appConfig.url}/service.php/api/versions`)
  .then(async (response) => {
    const data = await response.json().then(jsonData => jsonData);

    if (!response.ok) {
      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    }

    return data;
  });
