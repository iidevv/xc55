<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey\Tag;

use QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\Tag\OrderCustomerSatisfactionSurveyTagOutput as OutputDTO;
use QSL\CustomerSatisfaction\Model\Tag;

interface OutputTransformerInterface
{
    public function transform(Tag $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
