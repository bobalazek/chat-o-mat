<?php

namespace Application\Controller\MembersArea\Chat;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelsController
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

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ChatChannelType(),
            new \Application\Entity\ChatChannelEntity(),
            array(
                'user' => $app['user'],
            )
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $chatChannelEntity = $form->getData();

                $chatChannelEntity
                    ->setUser($app['user'])
                ;

                // Private
                if ($chatChannelEntity->isPrivate()) {
                    $chatChannelEntity
                        ->getUsers()
                        ->add($app['user'])
                    ;
                }

                $app['orm.em']->persist($chatChannelEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.chat.channels.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.chat.channels.detail',
                        array(
                            'id' => $chatChannelEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/channels/new.html.twig',
                $data
            )
        );
    }

    public function detailAction($id, Request $request, Application $app)
    {
        $data = array();

        $chatChannel = $app['orm.em']->find(
            'Application\Entity\ChatChannelEntity',
            $id
        );

        if (! $chatChannel) {
            $app->abort(404);
        }

        if (! $app['security']->isGranted('VIEW', $chatChannel)) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ChatChannelMessageType(),
            new \Application\Entity\ChatChannelMessageEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $chatChannelMessageEntity = $form->getData();

                $chatChannelMessageEntity
                    ->setChatChannel($chatChannel)
                    ->setUser($app['user'])
                ;

                $app['orm.em']->persist($chatChannelMessageEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.chat.channels.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.chat.channels.detail',
                        array(
                            'id' => $chatChannel->getId(),
                        )
                    )
                );
            }
        }

        // By Page
        $limitPerPage = 25;
        $page = $request->query->get('page', 1);
        // By Page /END

        // By Datetime
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
        // By Datetime /END

        // if($before || $after) {
            /* $chatChannelMessages = $app['orm.em']
                ->getRepository('Application\Entity\ChatChannelMessageEntity')
                ->getByChatChannelDatetime( $chatChannel, $type, $datetime)
            ; */
            // To-Do: Temp solution is to show all.
            //   Need to fix the pagination first
            $chatChannelMessages = $app['orm.em']
                ->getRepository('Application\Entity\ChatChannelMessageEntity')
                ->findBy(
                    array(
                        'chatChannel' => $chatChannel,
                    ),
                    array(
                        'timeCreated' => 'ASC',
                    )
                )
            ;
        /* } else {
            $chatChannelMessages = $app['orm.em']
                ->getRepository('Application\Entity\ChatChannelMessageEntity')
                ->getByChatChannelPage( $chatChannel, $page, $limitPerPage)
            ;
        } */
        // To-Do: We'll probably need to do it that way (by page)!

        $data['form'] = $form->createView();
        $data['chatChannel'] = $chatChannel;
        $data['chatChannelMessages'] = $chatChannelMessages;
        $data['chatChannelMessagesCount'] = $app['orm.em']
            ->getRepository('Application\Entity\ChatChannelMessageEntity')
            ->countAllByChatChannel($chatChannel)
        ;

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/channels/detail.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        $chatChannel = $app['orm.em']->find(
            'Application\Entity\ChatChannelEntity',
            $id
        );

        if (! $chatChannel) {
            $app->abort(404);
        }

        if (! $app['security']->isGranted('EDIT', $chatChannel)) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ChatChannelType(),
            $chatChannel,
            array(
                'user' => $app['user'],
            )
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $chatChannelEntity = $form->getData();

                // Private
                if ($chatChannelEntity->isPrivate()) {
                    $chatChannelEntity
                        ->getUsers()
                        ->add($app['user'])
                    ;
                }

                $app['orm.em']->persist($chatChannelEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.chat.channels.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.chat.channels.edit',
                        array(
                            'id' => $chatChannelEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['chatChannel'] = $chatChannel;

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/channels/edit.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        $chatChannel = $app['orm.em']->find(
            'Application\Entity\ChatChannelEntity',
            $id
        );

        if (! $chatChannel) {
            $app->abort(404);
        }

        if (! $app['security']->isGranted('EDIT', $chatChannel)) {
            $app->abort(403);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($chatChannel);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.chat.channels.remove.successText'
                    )
                );
            } catch (\Exception $e) {
                $app['flashbag']->add(
                    'danger',
                    $app['translator']->trans(
                        $e->getMessage()
                    )
                );
            }

            return $app->redirect(
                $app['url_generator']->generate(
                    'members-area.chat.channels'
                )
            );
        }

        $data['chatChannel'] = $chatChannel;

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/channels/remove.html.twig',
                $data
            )
        );
    }

    /***** Messages *****/
    public function messagesEditAction($id, Request $request, Application $app)
    {
        $data = array();

        $chatChannelMessage = $app['orm.em']->find(
            'Application\Entity\ChatChannelMessageEntity',
            $id
        );

        if (! $chatChannelMessage) {
            $app->abort(404);
        }

        if (! $app['security']->isGranted('EDIT', $chatChannelMessage)) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ChatChannelMessageType(),
            $chatChannelMessage
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $chatChannelMessageEntity = $form->getData();

                $app['orm.em']->persist($chatChannelMessageEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.chat.channels.messages.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.chat.channels.detail',
                        array(
                            'id' => $chatChannelMessageEntity->getChatChannel()->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['chatChannelMessage'] = $chatChannelMessage;

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/channels/messages/edit.html.twig',
                $data
            )
        );
    }

    public function messagesRemoveAction($id, Request $request, Application $app)
    {
        $data = array();

        $chatChannelMessage = $app['orm.em']->find(
            'Application\Entity\ChatChannelMessageEntity',
            $id
        );

        if (! $chatChannelMessage) {
            $app->abort(404);
        }

        if (! $app['security']->isGranted('REMOVE', $chatChannelMessage)) {
            $app->abort(403);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($chatChannelMessage);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.chat.channels.messages.remove.successText'
                    )
                );
            } catch (\Exception $e) {
                $app['flashbag']->add(
                    'danger',
                    $app['translator']->trans(
                        $e->getMessage()
                    )
                );
            }

            return $app->redirect(
                $app['url_generator']->generate(
                    'members-area.chat.channels.detail',
                    array(
                        'id' => $chatChannelMessage->getChatChannel()->getId(),
                    )
                )
            );
        }

        $data['chatChannelMessage'] = $chatChannelMessage;

        return new Response(
            $app['twig']->render(
                'contents/members-area/chat/channels/messages/remove.html.twig',
                $data
            )
        );
    }
}
