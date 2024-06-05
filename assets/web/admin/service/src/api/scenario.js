/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import appConfig from '../../config/app-config';
import { MODULE_STATES } from '../../src/constants';

const url = `${appConfig.url}/service.php/api/scenarios`;

// eslint-disable-next-line import/prefer-default-export

const convertInputTransitions = (transitions) => {
  const result = {
    modulesToEnable: [],
    modulesToDisable: [],
    modulesToRemove: [],
  };

  Object.entries(transitions).forEach(([moduleId, transition]) => {
    if (transition.stateToSet === MODULE_STATES.ENABLED) {
      result.modulesToEnable.push(moduleId);
    } else if (transition.stateToSet === MODULE_STATES.INSTALLED) {
      result.modulesToDisable.push(moduleId);
    } else if (transition.stateToSet === MODULE_STATES.REMOVED) {
      result.modulesToRemove.push(moduleId);
    }
  });

  return result;
};

const convertOutputTransitions = (transitions) => {
  const result = {};

  Object.entries(transitions).forEach(([moduleId, transition]) => {
    result[moduleId] = {
      stateToSet: transition.stateAfter,
      moduleName: transition.moduleName,
    };
  });

  return result;
};

export const createNonPersistentScenario = (moduleTransitions) => {
  const transitions = convertInputTransitions(moduleTransitions);

  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(transitions),
  };

  return fetch(`${url}/nonpersistent`, options).then(async (response) => {
    const data = await response.json().then(jsonData => jsonData);

    if (!response.ok) {
      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    }

    return { transitions: convertOutputTransitions(data.transitions) };
  });
};

export const createScenario = (scenarioType, moduleTransitions) => {
  const createScenarioUrl = (scenarioType === 'common')
    ? url
    : `${url}/upgrade`;

  const transitions = (scenarioType === 'common')
    ? convertInputTransitions(moduleTransitions)
    : { modulesToUpgrade: moduleTransitions };

  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(transitions),
  };

  return fetch(createScenarioUrl, options).then(async (response) => {
    const data = await response.json().then(jsonData => jsonData);

    if (!response.ok) {
      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    }

    return data;
  });
};
