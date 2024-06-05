<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\SearchPanel\Message\Admin;

/**
 * Main admin records search panel
 */
class Main extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return 'XC\VendorMessages\View\Form\ItemsList\Messages\Admin\Search';
    }

    /**
     * @inheritdoc
     */
    protected function getLinkedItemsList()
    {
        return '.conversations .widget.items-list';
    }

    /**
     * @inheritdoc
     */
    protected function getItemsList()
    {
        return parent::getItemsList() ?: \XC\VendorMessages\View\ItemsList\Admin\Conversations::getInstance();
    }

    /**
     * @inheritdoc
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
            'messageSubstring' => [
                static::CONDITION_CLASS                             => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Search keywords'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ],
            'messages' => [
                static::CONDITION_CLASS                            => 'XC\VendorMessages\View\FormField\Select\OrderMessagesFilter',
                \XLite\View\FormField\AFormField::PARAM_LABEL      => static::t('Messages'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ],
        ];
    }
}
