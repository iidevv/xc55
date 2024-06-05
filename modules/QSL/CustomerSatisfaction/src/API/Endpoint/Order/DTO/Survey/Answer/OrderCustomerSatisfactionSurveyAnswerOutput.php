<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\Answer;

use Symfony\Component\Validator\Constraints as Assert;

class OrderCustomerSatisfactionSurveyAnswerOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\PositiveOrZero
     * @var int
     */
    public int $value;

    /**
     * @var string
     */
    public string $origin_question;
}
