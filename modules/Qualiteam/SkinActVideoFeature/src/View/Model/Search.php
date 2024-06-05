<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Model;

use Qualiteam\SkinActVideoFeature\View\Form\Search as SearchForm;
use XLite\Core\Request;
use XLite\View\Button\AButton;
use XLite\View\Button\Regular;

/**
 * Search videos for customer zone
 */
class Search extends \XLite\View\Model\AModel
{
    const PARAM_VIDEOSUBSTRING   = 'videosubstring';

    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->schemaDefault = [
            'videosubstring' => [
                static::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                static::SCHEMA_PLACEHOLDER => static::t('SkinActVideoFeature search items'),
                static::SCHEMA_VALUE       => $this->getSearchValue(),
                static::SCHEMA_FIELD_ONLY => true,
            ],
        ];
    }

    protected function getSearchValue()
    {
        return Request::getInstance()->{static::PARAM_VIDEOSUBSTRING};
    }

    protected function getDefaultModelObject()
    {
    }

    protected function getFormClass()
    {
        return SearchForm::class;
    }

    protected function getFormButtons()
    {
        $buttons = parent::getFormButtons();

        $buttons['search'] = new \XLite\View\Button\Submit(
            [
                AButton::PARAM_LABEL => static::t('SkinActVideoFeature search button'),
                AButton::PARAM_STYLE => 'regular-button',
                Regular::PARAM_JS_CODE => 'return false',
            ],
        );

        return $buttons;
    }

    protected function getContainerClass()
    {
        return 'video__search';
    }
}