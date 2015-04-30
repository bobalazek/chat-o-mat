<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class ChatControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\ChatController::indexAction'
        )
        ->bind('members-area.chat');

        /***** User messages *****/
        $controllers->match(
            '/user-messages',
            'Application\Controller\MembersArea\Chat\UserMessagesController::indexAction'
        )
        ->bind('members-area.chat.user-messages');

        $controllers->match(
            '/user-messages/{id}',
            'Application\Controller\MembersArea\Chat\UserMessagesController::detailAction'
        )
        ->bind('members-area.chat.user-messages.detail');

        /***** Channels *****/
        $controllers->match(
            '/channels',
            'Application\Controller\MembersArea\Chat\ChannelsController::indexAction'
        )
        ->bind('members-area.chat.channels');

        $controllers->match(
            '/channels/new',
            'Application\Controller\MembersArea\Chat\ChannelsController::newAction'
        )
        ->bind('members-area.chat.channels.new');

        $controllers->match(
            '/channels/{id}',
            'Application\Controller\MembersArea\Chat\ChannelsController::detailAction'
        )
        ->bind('members-area.chat.channels.detail');

        $controllers->match(
            '/channels/{id}/edit',
            'Application\Controller\MembersArea\Chat\ChannelsController::editAction'
        )
        ->bind('members-area.chat.channels.edit');

        $controllers->match(
            '/channels/{id}/remove',
            'Application\Controller\MembersArea\Chat\ChannelsController::removeAction'
        )
        ->bind('members-area.chat.channels.remove');

        /*** Messages ***/
        $controllers->match(
            '/channels/messages/{id}/edit',
            'Application\Controller\MembersArea\Chat\ChannelsController::messagesEditAction'
        )
        ->bind('members-area.chat.channels.messages.edit');

        $controllers->match(
            '/channels/messages/{id}/remove',
            'Application\Controller\MembersArea\Chat\ChannelsController::messagesRemoveAction'
        )
        ->bind('members-area.chat.channels.messages.remove');

        /***** Users *****/
        $controllers->match(
            '/users/{id}',
            'Application\Controller\MembersArea\Chat\UsersController::detailAction'
        )
        ->bind('members-area.chat.users.detail');

        return $controllers;
    }
}
