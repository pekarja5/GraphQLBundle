<?php
/**
 * Date: 25.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace pekarja5\GraphQLBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;
use pekarja5\GraphQLBundle\Execution\Processor;

class GraphQLController extends Controller
{

    /**
     * @Route("/graphql")
     *
     * @throws ConfigurationException
     *
     * @return JsonResponse
     */
    public function defaultAction()
    {
        if ($this->get('request_stack')->getCurrentRequest()->getMethod() == 'OPTIONS') {
            return $this->createEmptyResponse();
        }

        list($queries, $isMultiQueryRequest) = $this->getPayload();

        $schemaClass = $this->getParameter('graphql.schema_class');
        if (!$schemaClass || !class_exists($schemaClass)) {
            return new JsonResponse([['message' => 'Schema class ' . $schemaClass . ' does not exist']], 200, $this->getParameter('graphql.response.headers'));
        }

        if (!$this->get('service_container')->initialized('graphql.schema')) {
            $schema = new $schemaClass();
            if ($schema instanceof ContainerAwareInterface) {
                $schema->setContainer($this->get('service_container'));
            }

            $this->get('service_container')->set('graphql.schema', $schema);
        }

        $queryResponses = array_map(function($queryData) {
            return $this->executeQuery($queryData['query'], $queryData['variables']);
        }, $queries);

        $response = new JsonResponse($isMultiQueryRequest ? $queryResponses : $queryResponses[0], 200, $this->getParameter('graphql.response.headers'));

        if ($this->getParameter('graphql.response.json_pretty')) {
            $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        }

        return $response;
    }

    private function createEmptyResponse()
    {
        return new JsonResponse([], 200, $this->getParameter('graphql.response.headers'));
    }

    private function executeQuery($query, $variables)
    {
        /** @var Processor $processor */
        $processor = $this->get('graphql.processor');
        $processor->processPayload($query, $variables);

        return $processor->getResponseData();
    }

    private function getPayload()
    {
        $request   = $this->get('request_stack')->getCurrentRequest();
        $query     = $request->get('query', null);
        $variables = $request->get('variables', []);
        $isMultiQueryRequest = false;
        $queries = [];

        $variables = is_string($variables) ? json_decode($variables, true) ?: [] : [];

        $content = $request->getContent();
        if (!empty($content)) {
            if ($request->headers->has('Content-Type') && 'application/graphql' == $request->headers->get('Content-Type')) {
                $queries[] = $content;
            } else {
                $params = json_decode($content, true);

                if ($params) {
                    // check for a list of queries
                    if (isset($params[0]) === true) {
                        $isMultiQueryRequest = true;
                    } else {
                        $params = [$params];
                    }

                    foreach ($params as $queryParams) {
                        $query = isset($queryParams['query']) ? $queryParams['query'] : $query;

                        if (isset($queryParams['variables'])) {
                            if (is_string($queryParams['variables'])) {
                                $variables = json_decode($queryParams['variables'], true) ?: $variables;
                            } else {
                                $variables = $queryParams['variables'];
                            }

                            $variables = is_array($variables) ? $variables : [];
                        }

                        $queries[] = [
                            'query' => $query,
                            'variables' => $variables,
                        ];
                    }
                }
            }
        } else {
            $queries[] = [
                'query' => $query,
                'variables' => $variables,
            ];
        }

        return [$queries, $isMultiQueryRequest];
    }
}
