<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class EmojisControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\EmojisController::indexAction'
        )
        ->bind('members-area.emojis');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\EmojisController::newAction'
        )
        ->bind('members-area.emojis.new');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\EmojisController::editAction'
        )
        ->bind('members-area.emojis.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\EmojisController::removeAction'
        )
        ->bind('members-area.emojis.remove');

        return $controllers;
    }
}
