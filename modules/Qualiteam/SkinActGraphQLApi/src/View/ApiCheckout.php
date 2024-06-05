<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View;

class ApiCheckout extends \XLite\View\Controller
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return ['graphql_api_checkout'];
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/SkinActGraphQLApi/api_checkout/body.twig';
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $return   = parent::getJSFiles();
        $return[] = 'modules/SkinActGraphQLApi/api_checkout/trigger_checkout.js';

        return array_merge($return, $this->getAdditionalPaymentScripts());
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $return   = parent::getCSSFiles();
        $return[] = 'modules/SkinActGraphQLApi/api_checkout/style.css';

        return $return;
    }

    /**
     * @return array
     */
    protected function getAdditionalPaymentScripts()
    {
        return [];
    }
}
