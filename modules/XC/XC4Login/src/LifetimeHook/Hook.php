<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\XC4Login\LifetimeHook;

use XC\MigrationWizard\Logic\Migration\Wizard;
use XLite\Core\Database;

final class Hook
{
    /**
     * @throws \Exception
     */
    public function onRebuild(): void
    {
        if (
            \XLite\Core\Config::getInstance()->XC->XC4Login->blowfish_key === ''
            && class_exists('\XC\MigrationWizard\Logic\Migration\Wizard')
        ) {
            $connectStep = Wizard::getInstance()->getStep('Connect');
            if ($connectStep && $secretKey = $connectStep->getSecret()) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                    'category' => 'XC\XC4Login',
                    'name'     => 'blowfish_key',
                    'value'    => $secretKey,
                ]);
            }
        }
        Database::getEM()->flush();
    }
}
