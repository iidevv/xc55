<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Logic\MembershipsQuickData\Generator;

/**
 * Memberships management page controller
 */
class Memberships extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage users');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t(\XLite\Core\Request::getInstance()->section === 'Communications' ? 'Customers' : 'Users');
    }

    /**
     * Update list
     */
    protected function doActionUpdateItemsList()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Product')->setBlockQuickDataFlag(true);
        parent::doActionUpdateItemsList();

        $newEntitiesIds = [];
        foreach ($this->getItemsList()->getCreatedEntities() as $key => $entity) {
            $newEntitiesIds[] = $entity->getMembershipId();
        }

        if (!empty($newEntitiesIds)) {
            \XLite\Core\Request::getInstance()->action = 'calculate';
            \XLite\Core\Request::getInstance()->memberships = $newEntitiesIds;
            $this->doActionCalculate();
        }
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return bool
     */
    public function isCalculationNotFinished()
    {
        $eventName = Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
        && in_array(
            $state['state'],
            [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS]
        )
        && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getCancelFlagVarName());
    }

    /**
     * Calculate memberships quick data
     */
    protected function doActionCalculate()
    {
        if ($options = $this->assembleOptions()) {
            Generator::run($options);
        }
        $this->setReturnURL(\XLite\Core\Request::getInstance()->returnURL ?: $this->buildURL('memberships'));
    }

    /**
     * Preprocessor for no-action run
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->calculation_completed) {
            \XLite\Core\TopMessage::addInfo('The calculation of quick data has been completed successfully.');

            $this->setReturnURL(
                \XLite\Core\Request::getInstance()->returnURL ?: $this->buildURL('memberships', '', ['section' => \XLite\Core\Request::getInstance()->section ?? ''])
            );
        } elseif ($request->calculation_failed) {
            \XLite\Core\TopMessage::addError('The calculation of quick data has been stopped.');

            $this->setReturnURL(
                $this->buildURL('memberships')
            );
        }
    }

    /**
     * Cancel
     */
    protected function doActionCalculationCancel()
    {
        Generator::cancel();
        \XLite\Core\TopMessage::addWarning('The calculation of quick data has been stopped.');
    }

    /**
     * Assemble options
     *
     * @return array
     */
    protected function assembleOptions()
    {
        $request = \XLite\Core\Request::getInstance();
        $ids = [];

        foreach ($request->memberships as $id) {
            if (\XLite\Core\Database::getRepo('XLite\Model\Membership')->find($id)) {
                $ids[] = $id;
            }
        }

        return $ids ? ['memberships' => $ids] : null;
    }

    /**
     * Get cancel flag name
     *
     * @return string
     */
    protected function getCancelFlagVarName()
    {
        return \XLite\Logic\MembershipsQuickData\Generator::getCancelFlagVarName();
    }
}
