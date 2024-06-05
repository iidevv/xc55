<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\AssemblerInterface;

class GenerateAssembler implements AssemblerInterface
{
    protected string $path = '';

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth\GenerateConstructor $generateConstructor
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler                $assembler
     */
    public function __construct(
        private GenerateConstructor $generateConstructor,
        private Assembler           $assembler,
    ) {
        $this->generateConstructor->build();
    }

    /**
     * Assembling a data to call endpoint
     */
    public function assemble(): void
    {
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
        return $this->generateConstructor->getBody();
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