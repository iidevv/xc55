<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Get;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\AssemblerInterface;

class RequestAssembler implements AssemblerInterface
{
    protected string $path = '';

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Get\RequestConstructor $requestConstructor
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler                       $assembler
     */
    public function __construct(
        private RequestConstructor $requestConstructor,
        private Assembler          $assembler,
    ) {
    }

    /**
     * Assembling a data to call endpoint
     */
    public function assemble(): void
    {
        $this->requestConstructor->build();

        $this->assembler->setBody(
            $this->getBody()
        );

        $this->assembler->setPath(
            $this->getPath()
        );

        $this->assembler->assemble();
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->requestConstructor->getBody();
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }
}