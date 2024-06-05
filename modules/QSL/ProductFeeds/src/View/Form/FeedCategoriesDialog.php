<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Form;

use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * "Update feed categories" dialog form class
 */
class FeedCategoriesDialog extends \XLite\View\Form\AForm implements ProviderInterface
{
    /**
     * Default form target.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'feed_categories';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Get validator
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
     * Set validators pairs for products data
     *
     * @param mixed &$data Data
     *
     * @return void
     */
    protected function setDataValidators(&$data)
    {
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

    public function getPreloadedLanguageLabels()
    {
        return [
            'Searching...'      => static::t('Searching...'),
            'No results found.' => static::t('No results found.')
        ];
    }
}
