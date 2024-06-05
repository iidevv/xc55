/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import fetchPageData from './fetch';
import appConfig from '../../config/app-config';

const serviceApi = `${appConfig.url}/service.php/api`;
const url = `${serviceApi}/module_upgrades`;
const wavesUrl = `${serviceApi}/waves?page=`;
const disallowedModules = `${serviceApi}/modules/disallowed`;

// eslint-disable-next-line import/prefer-default-export
export const fetchUpgradePageData = params => fetchPageData(url, params, 'upgrade');

export const fetchWavesInfo = params => fetchPageData(wavesUrl, params, 'waves');

export const fetchDisallowedModulesData = () => fetchPageData(disallowedModules, null, 'disallowed');
