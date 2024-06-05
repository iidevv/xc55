<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        $request = \XLite\Core\Request::getInstance();
        if (
            $this->getModuleId()
            && $this->getModule() === 'CDev-Egoods'
            && $request->action === 'update'
        ) {
            if (isset($request->esd_fullfilment)) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                        'category' => 'CDev\PINCodes',
                        'name'     => 'esd_fullfilment',
                        'value'    => (bool)$request->esd_fullfilment,
                ]);
            }

            if (isset($request->approve_before_download)) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                        'category' => 'CDev\PINCodes',
                        'name'     => 'approve_before_download',
                        'value'    => (bool)$request->approve_before_download,
                ]);
            }
        }

        parent::handleRequest();
    }
}
