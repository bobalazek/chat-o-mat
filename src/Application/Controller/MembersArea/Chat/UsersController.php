<?php

namespace Application\Controller\MembersArea\Chat;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        return $app->redirect(
            $app['url_generator']->generate(
                'members-area.chat'
            )
        );
    }

    public function detailAction($id, Request $request, Application $app)
    {
        $data = array();

        $user = $app['orm.em']->find(
            'Application\Entity\UserEntity',
            $id
        );

        if (! $user) {
            $app->abort(404);
        }

        $data['user'] = $user;

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/users/detail.html.twig',
                $data
            )
        );
    }
}
