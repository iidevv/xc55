<?php

namespace Iidev\StripeSubscriptions\View;

use XCart\Extender\Mapping\ListChild;
/**
 * @ListChild (list="center", zone="customer")
 */
class ActivationPage extends \XLite\View\AView
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {

        $list = parent::getAllowedTargets();
        $list[] = 'activation_page';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/StripeSubscriptions/page/activation.twig';
    }
}