<?php

namespace Iidev\StripeSubscriptions\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class MigrationStats extends \XLite\View\AView
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['migration_stats']);
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/StripeSubscriptions/page/subscriptions.twig';
    }
}
