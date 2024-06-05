<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\Base;

use XLite\Core\Database;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Address extends \XLite\Model\Base\Address
{
    public function setForcedState($state)
    {
        if ($state instanceof \XLite\Model\State) {
            if (
                $this->getCountry()
                && $this->getCountry()->getStatesCount() > 0
            ) {
                // Set by state object
                if ($state->getStateId()) {
                    if (!$this->state || $this->state->getStateId() != $state->getStateId()) {
                        $this->state = $state;
                        $this->setterProperty('state_id', $state->getStateId());
                    }
                    $this->setCustomState($this->state->getState());
                } else {
                    $this->state = null;
                    $this->setCustomState($state->getState());
                }
            } else {
                $this->state = null;
            }
        } elseif (is_string($state)) {
            $statesRepo = Database::getRepo('XLite\Model\State');

            if (
                $this->getCountry()
                && $this->getCountry()->hasStates()
                && ($state = $statesRepo->findOneBy([
                    'code' => $state,
                    'country' => $this->getCountry()
                ]))
            ) {
                $this->state = $state;
            } else {
                $this->state = null;
                $this->setCustomState($state);
            }
        }
    }
}