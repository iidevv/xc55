<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\Controller\Admin;

use XC\BulkEditing\Logic\BulkEdit\Scenario;

class BulkEdit extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    /**
     * @var string Current scenario
     */
    protected $scenario;

    /**
     * @var \XC\BulkEditing\Logic\BulkEdit\Generator
     */
    protected $generator;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['scenario']);
    }

    /**
     * Returns object to get initial data and populate submitted data to
     *
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
        $dto = Scenario::getScenarioDTO($this->scenario);

        return $dto ? new $dto() : null;
    }

    /**
     * Store 'scenario' request param
     */
    public function handleRequest()
    {
        parent::handleRequest();

        $this->scenario = \XLite\Core\Request::getInstance()->scenario;
    }

    /**
     * @return string
     */
    public function getCurrentScenario()
    {
        return $this->scenario;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $scenario = \XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioData($this->scenario);

        return static::t('Bulk edit') . ' ' . mb_strtolower($scenario['title']);
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        $sessionCellName = \XC\BulkEditing\Logic\BulkEdit\Scenario::$searchCndSessionCell;
        $sessionCell = \XLite\Core\Session::getInstance()->{$sessionCellName};

        $this->addLocationNode(static::t('Product list'), $sessionCell['returnURL'] ?: $this->buildURL('product_list'));
    }

    /**
     * @return boolean
     */
    public function checkAccess()
    {
        $sessionCellName = \XC\BulkEditing\Logic\BulkEdit\Scenario::$searchCndSessionCell;
        $filter = \XLite\Core\Session::getInstance()->{$sessionCellName};

        return parent::checkAccess() && ($filter || $this->getAction() === 'start');
    }

    /**
     * @return \XC\BulkEditing\Logic\BulkEdit\Generator
     */
    public function getGenerator()
    {
        if ($this->generator === null) {
            $eventName = \XC\BulkEditing\Logic\BulkEdit\Generator::getEventName();
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);
            $this->generator = ($state && isset($state['options']))
                ? new \XC\BulkEditing\Logic\BulkEdit\Generator($state['options'])
                : false;
        }

        return $this->generator;
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isBulkEditNotFinished()
    {
        $eventName = \XC\BulkEditing\Logic\BulkEdit\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
        && in_array(
            $state['state'],
            [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS],
            true
        )
        && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(
            \XC\BulkEditing\Logic\BulkEdit\Generator::getBulkEditCancelFlagVarName()
        );
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (\XLite\Core\Request::getInstance()->completed) {
            \XLite\Core\TopMessage::addInfo('Bulk edit has been processed successfully.');
            $this->setReturnURL($this->buildURL('bulk_edit', '', ['scenario' => $this->scenario]));
        } elseif (\XLite\Core\Request::getInstance()->failed) {
            \XLite\Core\TopMessage::addError('Bulk edit processing has been interrupted.');
            $this->setReturnURL($this->buildURL('bulk_edit', '', ['scenario' => $this->scenario]));
        }
    }

    /**
     * Before bulk edit form
     */
    protected function doActionStart()
    {
        $selected = \XLite\Core\Request::getInstance()->select;
        $selected = $selected ? array_keys($selected) : null;

        $conditionCell = null;
        if ($selected === null) {
            $itemList = \XLite\Core\Request::getInstance()->itemsList;
            if (class_exists($itemList) && method_exists($itemList, 'getConditionCellName')) {
                $conditionCell = $itemList::getConditionCellName();
            }
        }

        $sessionCellName = \XC\BulkEditing\Logic\BulkEdit\Scenario::$searchCndSessionCell;
        \XLite\Core\Session::getInstance()->{$sessionCellName} = [
            'selected'      => $selected,
            'conditionCell' => $conditionCell,
            'returnURL'     => \XLite\Core\Request::getInstance()->returnURL,
        ];

        $this->setReturnURL($this->buildURL('bulk_edit', '', ['scenario' => $this->scenario]));
    }

    /**
     * Bulk edit form submit
     */
    protected function doActionBulkEdit()
    {
        /** @var \XC\BulkEditing\Model\DTO\Product\AProduct $dto */
        $dto = $this->getFormModelObject();

        $formModel = null;
        $formModelClass = Scenario::getScenarioFormModel($this->scenario);
        if ($formModelClass) {
            $formModel = new $formModelClass(['object' => $dto]);
        }

        if ($formModel) {
            $form = $formModel->getForm();
            $data = \XLite\Core\Request::getInstance()->getData();

            $form->submit($data[$this->formName]);

            $dto->setEditedFields(json_decode($data['bulk_edit']));

            if ($form->isValid()) {
                $sessionCellName = \XC\BulkEditing\Logic\BulkEdit\Scenario::$searchCndSessionCell;
                \XC\BulkEditing\Logic\BulkEdit\Generator::run(
                    [
                        'data'      => serialize($dto),
                        'scenarios' => [Scenario::getScenarioStep($this->scenario)],
                        'filter'    => \XLite\Core\Session::getInstance()->{$sessionCellName},
                    ]
                );
            } else {
                \XLite\Core\Session::getInstance()->{$this->formModelDataSessionCellName} = $data[$this->formName];
            }
        }
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionCancel()
    {
        \XC\BulkEditing\Logic\BulkEdit\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('Bulk edit processing has been interrupted.');
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Request::getInstance()->scenario;
    }
}
