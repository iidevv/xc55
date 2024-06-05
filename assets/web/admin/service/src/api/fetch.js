/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

const fetchPageData = (url, params, key) => {
  if (typeof url === 'undefined') {
    return;
  }

  const paramsStr = params ? new URLSearchParams(params).toString() : '';

  // eslint-disable-next-line consistent-return
  return fetch(`${url}?${paramsStr}`).then(async (response) => {
    const data = await response.json().then(jsonData => jsonData);
    let result = {};

    if (!response.ok) {
      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    }

    result = {
      [key]: data['hydra:member'],
      totalItems: data['hydra:totalItems'],
    };

    return result;
  });
};

export default fetchPageData;
