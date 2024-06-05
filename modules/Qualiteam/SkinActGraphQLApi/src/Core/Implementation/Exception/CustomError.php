<?php


namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception;


class CustomError extends \GraphQL\Error\UserError
{
    public function __construct($message)
    {
        parent::__construct($message);
    }

}
