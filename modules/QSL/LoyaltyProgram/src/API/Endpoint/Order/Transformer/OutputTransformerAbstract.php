<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\API\Endpoint\Order\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use XLite\API\Endpoint\Order\Transformer\OutputTransformerAbstract as ExtendedOutputTransformerAbstract;
use QSL\LoyaltyProgram\API\Endpoint\Order\DTO\Output as ModuleOutputDTO;
use QSL\LoyaltyProgram\API\Endpoint\Order\Transformer\RewardHistoryEvent\OutputTransformerInterface;
use QSL\LoyaltyProgram\Model\Order;

/**
 * @Extender\Mixin
 */
class OutputTransformerAbstract extends ExtendedOutputTransformerAbstract
{
    protected OutputTransformerInterface $rewardHistoryEventTransformer;

    /**
     * @required
     */
    public function setRewardHistoryEventTransformer(OutputTransformerInterface $rewardHistoryEventTransformer): void
    {
        $this->rewardHistoryEventTransformer = $rewardHistoryEventTransformer;
    }

    /**
     * @param Order $object
     * @throws \Exception
     */
    protected function basicTransform(BaseOutput $dto, $object, string $to, array $context = []): BaseOutput
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::basicTransform($dto, $object, $to, $context);

        $dto->reward_points = $object->getRewardPoints() ?? 0;
        $dto->settled_points = $object->getSettledPoints() ?? 0;
        $dto->redeemed_points = $object->getRedeemedPoints() ?? 0;
        $dto->max_redeemed_points = $object->getMaxRedeemedPoints() ?? 0;
        $dto->points_rewarded = $object->getPointsRewarded();
        $dto->points_redeemed = $object->getPointsRedeemed();

        $dto->reward_events = [];
        foreach ($object->getRewardEvents() as $rewardHistoryEvent) {
            $dto->reward_events[] = $this->rewardHistoryEventTransformer->transform($rewardHistoryEvent, $to, $context);
        }

        return $dto;
    }
}
