<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\API\Endpoint\Order\Transformer\RewardHistoryEvent;

use QSL\LoyaltyProgram\API\Endpoint\Order\DTO\RewardHistoryEvent\OrderRewardHistoryEventOutput as OutputDTO;
use QSL\LoyaltyProgram\Model\RewardHistoryEvent;

interface OutputTransformerInterface
{
    public function transform(RewardHistoryEvent $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
