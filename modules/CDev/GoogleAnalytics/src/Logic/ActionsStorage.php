<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic;

use XLite\Base\Singleton;
use CDev\GoogleAnalytics\Logic\Action\Interfaces\IAction;

/**
 * Class ActionsStorage
 */
class ActionsStorage extends Singleton
{
    /**
     * @var IAction[]
     */
    protected $actions = [];

    /**
     * Add action
     *
     * @param IAction $action
     */
    public function addAction(IAction $action): void
    {
        $key = str_replace('\\', '', get_class($action));

        $this->actions[$key] = $action;
    }

    /**
     * @return IAction[]
     */
    public function getApplicableActions(): array
    {
        return array_filter(
            $this->getActions(),
            static function (IAction $action) {
                return $action->isApplicable();
            }
        );
    }

    /**
     * @return IAction[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function clearActions(array $actions): void
    {
        foreach ($actions as $key => $action) {
            if (isset($this->actions[$key])) {
                unset($this->actions[$key]);
            }
        }
    }
}
