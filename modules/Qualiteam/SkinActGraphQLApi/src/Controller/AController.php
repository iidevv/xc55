<?php

namespace Qualiteam\SkinActGraphQLApi\Controller;

/**
 * Class AController
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\After("CDev\XPaymentsConnector")
 *
 */

class AController extends \XLite\Controller\AController
{
    public function setActionError($message = '', $code = 0)
    {
        parent::setActionError($message, $code);

        \Qualiteam\SkinActGraphQLApi\Core\ActionResult::getInstance()
            ->setActionError($message, $code);
    }

    public function isActionError()
    {
        return parent::isActionError()
            || \Qualiteam\SkinActGraphQLApi\Core\ActionResult::getInstance()->isError();
    }

    protected function redirect($url = null, $code = null)
    {
        if (!\Qualiteam\SkinActGraphQLApi\Core\ActionResult::getInstance()->isEnabled()) {
            parent::redirect($url, $code);
        }
    }
}