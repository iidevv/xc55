/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import fetchPageData from './fetch';
import appConfig from '../../config/app-config';

const url = `${appConfig.url}/service.php/api/modules`;

export const fetchModulesPage = params => fetchPageData(url, params, 'modules');

export const fetchModuleById = id => fetch(`${url}/byModuleId/${id}`)
  .then(async (response) => {
    const data = await response.json().then(jsonData => jsonData);
    let result = {};

    if (!response.ok) {
      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    }

    result = {
      id: data.id,
      module: data,
    };

    return result;
  });
