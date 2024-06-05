<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated abstract controller for catalog pages.
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return TRUE if the customer can ask product questions.
     *
     * @return boolean
     */
    public function isAllowedAskQuestion()
    {
        return $this->isGuestProductQuestionAllowed() || $this->getProfile();
    }

    /**
     * Return TRUE if guest customers can ask product questions.
     *
     * @return boolean
     */
    public function isGuestProductQuestionAllowed()
    {
        return (bool)\XLite\Core\Config::getInstance()->QSL->ProductQuestions->guest_questions_allowed;
    }
}
