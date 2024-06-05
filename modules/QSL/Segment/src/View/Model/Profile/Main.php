<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * Profile model view
 * @Extender\Mixin
 */
class Main extends \XLite\View\Model\Profile\Main
{
    /**
     * @inheritdoc
     */
    protected function postprocessSuccessAction()
    {
        parent::postprocessSuccessAction();

        if (in_array($this->currentAction, ['update'])) {
            // Update profile
            \QSL\Segment\Core\Mediator::getInstance()->doUpdateProfile($this->getModelObject());
        }
    }
}
