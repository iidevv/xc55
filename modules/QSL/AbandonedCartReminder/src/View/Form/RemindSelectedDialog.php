<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Form;

/**
 * "Choose reminder to send" dialog class.
 */
class RemindSelectedDialog extends \XLite\View\Form\AForm
{
    /**
     * Return default target for the dialog form.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'abandoned_carts';
    }

    /**
     * Return default action for the dialog form.
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'remind';
    }

    /**
     * Get form validator.
     *
     * @return \XLite\Core\Validator\HashArray
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();

        $data = $validator->addPair('postedData', new \XLite\Core\Validator\HashArray());
        $this->setDataValidators($data);

        return $validator;
    }

    /**
     * Called before the includeCompiledFile()
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $data = [];

        if (is_array(\XLite\Core\Request::getInstance()->select)) {
            foreach (\XLite\Core\Request::getInstance()->select as $id => $value) {
                $data['select[' . $id . ']'] = $id;
            }
        }
        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($data);
    }
}
