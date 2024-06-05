<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Menu\Admin\LeftMenu;

/**
 * Questions
 */
class QuestionsIcon extends Questions
{
    protected function getLabel()
    {
        return $this->getCountUnansweredQuestions()
            ? '<i class="fa fa-comments-o"></i>'
            : null;
    }
}
