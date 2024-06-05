<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\InputRule;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use XLite\API\InputRule\SubRule\SubRuleInterface;

class RuleSet implements InputRuleInterface
{
    protected DataTransformerInitializerInterface $inner;

    /**
     * @var SubRuleInterface[]
     */
    protected array $subRules;

    public function __construct(
        DataTransformerInitializerInterface $inner,
        array $subRules
    ) {
        $this->inner = $inner;
        foreach ($subRules as $subRule) {
            $this->addSubRule($subRule);
        }
    }

    public function transform($object, string $to, array $context = []): object
    {
        foreach ($this->subRules as $subRule) {
            $subRule->check($object, $context);
        }

        return $this->inner->transform($object, $to, $context);
    }

    public function initialize(string $inputClass, array $context = [])
    {
        return $this->inner->initialize($inputClass, $context);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $this->inner->supportsTransformation($data, $to, $context);
    }

    public function addSubRule(SubRuleInterface $subRule): RuleSet
    {
        $this->subRules[] = $subRule;

        return $this;
    }
}
