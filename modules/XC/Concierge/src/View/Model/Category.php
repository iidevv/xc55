<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\View\Model;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\Category as CategoryTrack;

/**
 * Category view model
 * @Extender\Mixin
 */
abstract class Category extends \XLite\View\Model\Category
{
    protected function postprocessSuccessAction()
    {
        parent::postprocessSuccessAction();

        $action = $this->currentAction;
        if (in_array($action, ['create', 'update', 'modify'], true)) {
            Mediator::getInstance()->addMessage(
                new CategoryTrack(
                    $action === 'create' ? 'Create Category' : 'Update Category',
                    $this->getModelObject()
                )
            );
        }
    }
}
