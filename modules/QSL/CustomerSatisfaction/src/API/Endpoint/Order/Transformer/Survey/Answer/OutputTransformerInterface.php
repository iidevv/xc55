<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey\Answer;

use QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\Answer\OrderCustomerSatisfactionSurveyAnswerOutput as OutputDTO;
use QSL\CustomerSatisfaction\Model\Answer;

interface OutputTransformerInterface
{
    public function transform(Answer $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
