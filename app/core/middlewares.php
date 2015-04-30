<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*** Database check ***/
$app->before(function () use ($app) {
    if (isset($app['databaseOptions'])) {
        try {
            $app['db']->connect();
        } catch (PDOException $e) {
            return new Response(
                'Whoops, your database is configured wrong.
                Please check that again! Message: '.$e->getMessage()
            );
        }
    }
});

/*** User check ***/
$app->before(function () use ($app) {
    $token = $app['security']->getToken();
    $app['user'] = null;

    if ($token &&
        ! $app['security.trust_resolver']->isAnonymous($token) &&
        $token->getUser() instanceof \Application\Entity\UserEntity) {
        $app['user'] = $token->getUser();
    }
});

/*** Set Variables ****/
$app->before(function () use ($app) {
    if (!$app['session']->isStarted()) {
        $app['session']->start();
    }

    if (! isset($app['user'])) {
        $app['user'] = null;
    }

    $app['sessionId'] = $app['session']->getId();
    $app['host'] = $app['request']->getHost();
    $app['hostWithScheme'] = $app['request']->getScheme().'://'.$app['host'];
    $app['baseUri'] = $app['request']->getBaseUrl();
    $app['baseUrl'] = $app['request']->getSchemeAndHttpHost().$app['request']->getBaseUrl();
    $app['currentUri'] = $app['request']->getRequestURI();
    $app['currentUrl'] = $app['request']->getUri();
    $app['currentUriRelative'] = rtrim(str_replace($app['baseUri'], '', $app['currentUri']), '/');
    $app['currentUriArray'] = array_filter(
        explode(
            '/',
            str_replace($app['baseUri'], '', $app['currentUri'])
        ),
        'strlen'
    );
}, \Silex\Application::EARLY_EVENT);

/*** Data ****/
$app->before(function () use ($app) {
    // Emojis
    $emojis = array();
    $emojisEntities = $app['orm.em']
        ->getRepository('Application\Entity\EmojiEntity')
        ->findAll()
    ;

    if ($emojisEntities) {
        foreach ($emojisEntities as $emojiEntity) {
            $emojis[$emojiEntity->getName()] = $emojiEntity->toArray();
        }
    }

    $app['autocomplete.emojis'] = $emojis;

    // Users
    $users = array();
    $usersEntities = $app['orm.em']
        ->getRepository('Application\Entity\UserEntity')
        ->findAll()
    ;

    if ($usersEntities) {
        foreach ($usersEntities as $userEntity) {
            $users[$userEntity->getUsername()] = $userEntity->toArray();
        }
    }

    $app['autocomplete.users'] = $users;

    // Channels
    $channels = array();
    $channelsEntities = $app['orm.em']
        ->getRepository('Application\Entity\ChatChannelEntity')
        ->getAllPublic()
    ;

    if ($app['user']) {
        $channelsEntities = array_merge(
            $channelsEntities,
            $app['user']->getChatChannels()
        );
    }

    if ($channelsEntities) {
        foreach ($channelsEntities as $channelEntity) {
            $channels[$channelEntity->getName()] = $channelEntity->toArray();
        }
    }

    $app['autocomplete.channels'] = $channels;
});

/*** Set Logut path ***/
$app->before(function (Request $request) use ($app) {
    $csrfToken = $app['form.csrf_provider']->generateCsrfToken('logout');
    $app['logoutUrl'] = $app['url_generator']->generate('members-area.logout').'?csrf_token='.$csrfToken;
});

/*** SOAP ***/
$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, PATCH, DELETE, OPTIONS');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set(
        'Access-Control-Allow-Headers',
        'Locale'
    );
});
