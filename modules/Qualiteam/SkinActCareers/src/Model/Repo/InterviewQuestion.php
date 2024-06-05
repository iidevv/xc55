<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Model\Repo;


class InterviewQuestion extends \XLite\Model\Repo\ARepo
{

    public function getEnabledQuestions()
    {
        return $this->findBy(['enabled' => true], ['position' => 'ASC']);
    }

}