<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Auth
 * @Extender\Mixin
 */
class Auth extends \XLite\Core\Auth
{
    protected $processLogin = false;

    /**
     * @inheritdoc
     */
    public function login($login, $password, $secureHash = null)
    {
        $this->processLogin = true;

        $result = parent::login($login, $password, $secureHash);
        if (is_object($result) && $result instanceof \XLite\Model\Profile) {
            \QSL\Segment\Core\Mediator::getInstance()->doLogin($result);
        } else {
            \QSL\Segment\Core\Mediator::getInstance()->doLoginFailed($result);
        }

        $this->processLogin = false;

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function loginProfile(\XLite\Model\Profile $profile, $withCells = true)
    {
        $result = parent::loginProfile($profile);

        if (!$this->processLogin && $result) {
            \QSL\Segment\Core\Mediator::getInstance()->doLogin($profile);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function logoff()
    {
        parent::logoff();

        \QSL\Segment\Core\Mediator::getInstance()->doLogoff();
    }
}
