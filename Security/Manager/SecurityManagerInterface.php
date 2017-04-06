<?php

namespace pekarja5\GraphQLBundle\Security\Manager;

use pekarja5\GraphQLBundle\Execution\ResolveInfo;
use Youshido\GraphQL\Parser\Ast\Query;

/**
 * Date: 29.08.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */
interface SecurityManagerInterface
{

    const RESOLVE_ROOT_OPERATION_ATTRIBUTE = 'RESOLVE_ROOT_OPERATION';
    const RESOLVE_FIELD_ATTRIBUTE          = 'RESOLVE_FIELD';

    /**
     * @param $attribute string
     *
     * @return bool
     */
    public function isSecurityEnabledFor($attribute);

    /**
     * @param ResolveInfo $resolveInfo
     *
     * @return bool
     */
    public function isGrantedToFieldResolve(ResolveInfo $resolveInfo);

    /**
     * @param Query $query
     *
     * @return bool
     */
    public function isGrantedToOperationResolve(Query $query);

    /**
     * @param ResolveInfo $resolveInfo
     *
     * @return mixed
     *
     * @throw \Exception
     */
    public function createNewFieldAccessDeniedException(ResolveInfo $resolveInfo);

    /**
     * @param Query $query
     *
     * @return mixed
     *
     * @throw \Exception
     */
    public function createNewOperationAccessDeniedException(Query $query);
}