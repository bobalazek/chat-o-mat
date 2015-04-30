<?php

namespace Application\Controller\MembersArea\Chat;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMessagesController
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

        if ($id == $app['user']->getId()) { // You obviously can not chat with yourself!
            return $app->redirect(
                $app['url_generator']->generate(
                    'members-area.chat'
                )
            );
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ChatUserMessageType(),
            new \Application\Entity\ChatUserMessageEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $chatUserMessageEntity = $form->getData();

                $chatUserMessageEntity
                    ->setUser($user)
                    ->setUserFrom($app['user'])
                ;

                $app['orm.em']->persist($chatUserMessageEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.chat.user-messages.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.chat.user-messages.detail',
                        array(
                            'id' => $user->getId(),
                        )
                    )
                );
            }
        }

        $after = $request->query->get('after', false);
        $before = $request->query->get('before', false);

        $type = 'after'; // before or after
        $datetime = new \DateTime('- 12 hours');

        if($after) {
            $datetime = new \DateTime(date('Y-m-d H:i:s', $after));
        } elseif( $before ) {
            $type = 'before';
            $datetime = new \DateTime(date('Y-m-d H:i:s', $before));
        }

        $chatUserMessages = $app['orm.em']
            ->getRepository('Application\Entity\ChatUserMessageEntity')
            ->getByUsersByDatetime($user, $app['user'], $type, $datetime)
        ;

        $data['form'] = $form->createView();
        $data['user'] = $user;
        $data['chatUserMessages'] = $chatUserMessages;
        $data['chatUserMessagesCount'] = $app['orm.em']
            ->getRepository('Application\Entity\ChatUserMessageEntity')
            ->countAllByUsers($user, $app['user'])
        ;

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/user-messages/detail.html.twig',
                $data
            )
        );
    }
}
