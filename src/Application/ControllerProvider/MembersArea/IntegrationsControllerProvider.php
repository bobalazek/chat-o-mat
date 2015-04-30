<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class IntegrationsControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\IntegrationsController::indexAction'
        )
        ->bind('members-area.integrations');

        return $controllers;
    }
}
