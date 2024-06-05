<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['product_questions'])) {
            $this->relatedTargets['product_questions'] = [];
        }

        $this->relatedTargets['product_questions'][] = 'product_question';

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['communications'])) {
            $list['communications'][static::ITEM_CHILDREN]['product_questions'] = [
                static::ITEM_TITLE  => static::t('Manage product questions'),
                static::ITEM_WIDGET => 'QSL\ProductQuestions\View\Menu\Admin\LeftMenu\Questions',
                static::ITEM_TARGET => 'product_questions',
                static::ITEM_WEIGHT => 190
            ];

            if (!isset($list['communications'][static::ITEM_WIDGET])) {
                $list['communications'] += [
                    static::ITEM_WIDGET => 'QSL\ProductQuestions\View\Menu\Admin\LeftMenu\QuestionsIcon'
                ];
            }
        }

        return $list;
    }
}
