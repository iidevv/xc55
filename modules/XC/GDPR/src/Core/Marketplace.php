<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Marketplace\Constant;
use XLite\Core\Marketplace\QueryRegistry;
use XLite\Core\Marketplace\Retriever;
use XLite\Core\TmpVars;

/**
 * @Extender\Mixin
 */
class Marketplace extends \XLite\Core\Marketplace
{
    /**
     * @return bool
     */
    public function isGdprModulesListActual()
    {
        [$cellTTL, $cellData] = $this->getActionCacheVars(Constant::REQUEST_GDPR_MODULES);

        return $this->checkTTL($cellTTL, static::TTL_LONG)
            && TmpVars::getInstance()->{$cellData};
    }

    /**
     * Retireve GDPR-active modules list from marketplace
     *
     * @param array
     *
     * @return array
     */
    public function retrieveGdprModules()
    {
        return $this->performActionRequestWithCache(
            Constant::REQUEST_GDPR_MODULES,
            static function () {
                return Retriever::getInstance()->retrieve(
                    QueryRegistry::getQuery('gdpr_modules'),
                    new \XC\GDPR\Core\Marketplace\Normalizer\GDPRModules()
                );
            }
        );
    }

    protected function getCachedRequestTypes()
    {
        return array_merge(parent::getCachedRequestTypes(), [
            Constant::REQUEST_GDPR_MODULES,
        ]);
    }
}
