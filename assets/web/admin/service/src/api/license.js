/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import appConfig from '../../config/app-config';
import fetchPageData from './fetch';

const url = `${appConfig.url}/service.php/api/licenses`;

/* eslint-disable import/prefer-default-export */
export const fetchCoreLicenseInfo = () => fetch(`${url}/core`)
  .then(async (response) => {
    const data = await response.json().then(jsonData => jsonData);

    if (!response.ok) {
      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    }

    return data;
  });

export const fetchStoreLicenses = params => fetchPageData(url, params, 'licenses');
