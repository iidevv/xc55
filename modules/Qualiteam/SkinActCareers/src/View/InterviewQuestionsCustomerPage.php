<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 *
 * @ListChild (list="center", zone="customer", weight="100")
 */
class InterviewQuestionsCustomerPage extends \XLite\View\AView
{

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/interview_questions_customer_page.less';
        return $list;
    }

    public static function getAllowedTargets()
    {
        return ['interview_questions'];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCareers/interview_questions_customer_page.twig';
    }

}