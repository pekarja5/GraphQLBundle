<?php
/**
 * This file is a part of GraphQLBundle project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 9/23/16 11:20 PM
 */

namespace pekarja5\GraphQLBundle\Execution\Context;

use \Youshido\GraphQL\Execution\Context\ExecutionContext as BaseExecutionContext;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use pekarja5\GraphQLBundle\Config\Rule\TypeValidationRule;

class ExecutionContext extends BaseExecutionContext
{
    public function __construct(AbstractSchema $schema) {
        $validator = ConfigValidator::getInstance();
        $validator->addRule('type', new TypeValidationRule($validator));

        parent::__construct($schema);
    }


}