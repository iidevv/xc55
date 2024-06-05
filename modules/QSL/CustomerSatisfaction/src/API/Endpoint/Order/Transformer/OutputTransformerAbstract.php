<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer;

use DateTimeImmutable;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Output as ModuleOutputDTO;
use QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey\OutputTransformerInterface;
use QSL\CustomerSatisfaction\Model\Order;

/**
 * @Extender\Mixin
 */
class OutputTransformerAbstract extends \XLite\API\Endpoint\Order\Transformer\OutputTransformerAbstract
{
    protected OutputTransformerInterface $surveyTransformer;

    /**
     * @required
     */
    public function setSurveyTransformer(OutputTransformerInterface $surveyTransformer): void
    {
        $this->surveyTransformer = $surveyTransformer;
    }

    /**
     * @param Order $object
     */
    protected function basicTransform(BaseOutput $dto, $object, string $to, array $context = []): BaseOutput
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::basicTransform($dto, $object, $to, $context);

        $dto->survey_future_send_date = new DateTimeImmutable('@' . $object->getSurveyFutureSendDate());
        $dto->survey = $object->getSurvey() ? $this->surveyTransformer->transform($object->getSurvey(), $to, $context) : null;

        return $dto;
    }
}
