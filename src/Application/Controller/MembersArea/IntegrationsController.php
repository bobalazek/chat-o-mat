<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IntegrationsController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        return new Response(
            $app['twig']->render(
                'contents/members-area/integrations/index.html.twig',
                $data
            )
        );
    }
}
