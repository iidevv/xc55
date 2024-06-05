<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Page\Admin;

use XLite\Model\Config;

/**
 * Return reasons page view
 *
 * #ListChild (list="admin.center", zone="admin")
 */
class ReturnReasons extends \XLite\View\AView
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'return_reasons';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/Returns/return_reasons/body.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/Returns/settings.less';

        return $list;
    }

    /**
     * @return Config
     */
    protected function getHideOtherReasonOption()
    {
        return $this->executeCachedRuntime(static function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Config')
                ->findOneBy([
                    'name'     => 'hide_other_reason',
                    'category' => 'QSL\Returns',
                ]);
        });
    }
}
