<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['order_list'])) {
            $this->relatedTargets['order_list'] = [];
        }

        $this->relatedTargets['order_list'][] = 'order_returns';

        parent::__construct($params);

        $this->addRelatedTarget('return_reasons', 'order_statuses', [], ['page' => 'payment']);
        $this->addRelatedTarget('return_actions', 'order_statuses', [], ['page' => 'payment']);
    }
}
