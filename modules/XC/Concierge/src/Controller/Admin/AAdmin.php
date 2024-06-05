<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\Track;

/**
 * Abstract customer controller
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\Controller\Admin\AAdmin
{
    /**
     * return string
     */
    public function getConciergeCategory()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getConciergeTitle()
    {
        return $this->getTitle();
    }

    protected function doActionChangeLanguage()
    {
        $session         = \XLite\Core\Session::getInstance();
        $oldLanguageCode = $session->getLanguage()->getCode();

        parent::doActionChangeLanguage();

        $newLanguageCode = $session->getLanguage()->getCode();

        if ($oldLanguageCode !== $newLanguageCode) {
            Mediator::getInstance()->addMessage(
                new Track(
                    'Change Language',
                    [
                        'From' => $oldLanguageCode,
                        'To'   => $newLanguageCode,
                    ]
                )
            );
        }
    }
}
