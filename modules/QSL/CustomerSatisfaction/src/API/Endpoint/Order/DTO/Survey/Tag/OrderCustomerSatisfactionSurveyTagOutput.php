<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\Tag;

use Symfony\Component\Validator\Constraints as Assert;

class OrderCustomerSatisfactionSurveyTagOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank
     * @var string
     */
    public string $name;
}
