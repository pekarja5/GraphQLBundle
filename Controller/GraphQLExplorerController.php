<?php
/**
 * Date: 31.08.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace pekarja5\GraphQLBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GraphQLExplorerController extends Controller
{

    /**
     * @Cache(expires="tomorrow", public=true)
     * @Route("/graphql/explorer")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function explorerAction()
    {
        return $this->render('GraphQLBundle:Feature:explorer.html.twig', [
            'graphQLUrl' => $this->generateUrl('pekarja5_graphql_graphql_default'),
            'tokenHeader' => 'access-token'
        ]);
    }

}
