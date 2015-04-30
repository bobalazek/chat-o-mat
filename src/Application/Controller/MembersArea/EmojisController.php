<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmojisController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_EMOJIS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $data['emojis'] = $app['orm.em']
            ->getRepository('Application\Entity\EmojiEntity')
            ->findAll()
        ;

        return new Response(
            $app['twig']->render(
                'contents/members-area/emojis/index.html.twig',
                $data
            )
        );
    }

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_EMOJIS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\EmojiType(),
            new \Application\Entity\EmojiEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $emojiEntity = $form->getData();

                /*** Image ***/
                $emojiEntity
                    ->setImageUploadPath($app['baseUrl'].'/assets/uploads/')
                    ->setImageUploadDir(WEB_DIR.'/assets/uploads/')
                    ->imageUpload()
                ;

                $app['orm.em']->persist($emojiEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.emojis.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.emojis.edit',
                        array(
                            'id' => $emojiEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/emojis/new.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_EMOJIS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $emoji = $app['orm.em']->find('Application\Entity\EmojiEntity', $id);

        if (! $emoji) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\EmojiType(),
            $emoji
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $emojiEntity = $form->getData();

                /*** Image ***/
                $emojiEntity
                    ->setImageUploadPath($app['baseUrl'].'/assets/uploads/')
                    ->setImageUploadDir(WEB_DIR.'/assets/uploads/')
                    ->imageUpload()
                ;

                $app['orm.em']->persist($emojiEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.emojis.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.emojis.edit',
                        array(
                            'id' => $emojiEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['emoji'] = $emoji;

        return new Response(
            $app['twig']->render(
                'contents/members-area/emojis/edit.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_EMOJIS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $emoji = $app['orm.em']->find('Application\Entity\EmojiEntity', $id);

        if (! $emoji) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($emoji);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.emojis.remove.successText'
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
                    'members-area.emojis'
                )
            );
        }

        $data['emoji'] = $emoji;

        return new Response(
            $app['twig']->render(
                'contents/members-area/emojis/remove.html.twig',
                $data
            )
        );
    }
}
