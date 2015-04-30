<?php

/***** Config *****/
if (! file_exists(APP_DIR.'/configs/global.php')) {
    exit('No global config file found. Please create one (app/configs/global.php)!');
}

$app->register(
    new Igorw\Silex\ConfigServiceProvider(
        APP_DIR.'/configs/global.php'
    )
);

if (file_exists(APP_DIR.'/configs/global-local.php')) {
    $app->register(
        new Igorw\Silex\ConfigServiceProvider(
                APP_DIR.'/configs/global-local.php'
        )
    );
}

/***** Session *****/
$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => STORAGE_DIR.'/sessions',
));

/* Flashbag */
$app['flashbag'] = $app->share(function () use ($app) {
    return $app['session']->getFlashBag();
});

/***** Http Cache *****/
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => STORAGE_DIR.'/cache/http/',
));

/***** Translation *****/
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback' => 'en_US',
));

$app['translator']->addLoader(
    'yaml',
    new Symfony\Component\Translation\Loader\YamlFileLoader()
);

/***** Form *****/
$app->register(new Silex\Provider\FormServiceProvider());

/***** Twig / Templating Engine *****/
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => APP_DIR.'/templates',
        'twig.form.templates' => array(
            'twig/form.html.twig',
        ),
        'twig.options' => array(
            'cache' => STORAGE_DIR.'/cache/template',
            'debug' => $app['debug'],
        ),
    )
);

/***** Doctrine Database & Doctrine ORM *****/
if (isset($app['databaseOptions'])) {
    $app->register(
        new Silex\Provider\DoctrineServiceProvider(),
        array(
            'dbs.options' => $app['databaseOptions'],
        )
    );

    $app->register(
        new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(),
        array(
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'annotation',
                        'namespace' => 'Application\Entity',
                        'path' => SRC_DIR.'/Application/Entity',
                        'use_simple_annotation_reader' => false,
                    ),
                ),
            ),
        )
    );

    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(
        array(
            require VENDOR_DIR.'/autoload.php',
            'loadClass',
        )
    );

    $entityManagerConfig = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        array(APP_DIR.'/src/Application/Entity'),
        $app['debug']
    );

    $entityManager = Doctrine\ORM\EntityManager::create(
        $app['dbs.options']['default'],
        $entityManagerConfig
    );

    Doctrine\Common\Persistence\PersistentObject::setObjectManager(
        $entityManager
    );

    $app['orm.proxies_dir'] = STORAGE_DIR.'/cache/proxy';

    $app['orm.manager_registry'] = $app->share(function ($app) {
        return new \Application\Doctrine\ORM\DoctrineManagerRegistry(
            'manager_registry',
            array('default' => $app['orm.em']->getConnection()),
            array('default' => $app['orm.em'])
        );
    });

    $app['form.extensions'] = $app->share(
        $app->extend(
            'form.extensions',
            function ($extensions) use ($app) {
                $extensions[] = new \Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension(
                    $app['orm.manager_registry']
                );

                return $extensions;
            }
        )
    );
}

/***** Validator *****/
$app->register(new \Silex\Provider\ValidatorServiceProvider());

$app['validator.mapping.mapping.file_path'] = APP_DIR.'/configs/validation.yml';

$app['validator.mapping.class_metadata_factory'] = $app->share(function ($app) {
    return new Symfony\Component\Validator\Mapping\ClassMetadataFactory(
        new Symfony\Component\Validator\Mapping\Loader\YamlFileLoader(
            APP_DIR.'/configs/validation.yml'
        )
    );
});

$app['validator.unique_entity'] = $app->share(function ($app) {
    return new \Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator(
        $app['orm.manager_registry']
    );
});

$app['security.validator.user_password'] = $app->share(function ($app) {
    return new Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator(
        $app['security'],
        $app['security.encoder_factory']
    );
});

$app['validator.validator_service_ids'] = array(
    'doctrine.orm.validator.unique' => 'validator.unique_entity',
    'security.validator.user_password' => 'security.validator.user_password',
);

/***** Url Generator *****/
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/***** User Provider *****/
$app['user.provider'] = $app->share(function () use ($app) {
    return new \Application\Provider\UserProvider($app);
});

/***** Security *****/
$securityFirewalls = array();

/*** Members Area ***/
$securityFirewalls['members-area'] = array(
    'pattern' => '^/',
    'anonymous' => true,
    'form' => array(
        'login_path' => '/members-area/login',
        'check_path' => '/members-area/login/check',
        'failure_path' => '/members-area/login',
        'default_target_path' => '/members-area',
        //'always_use_default_target_path' => true,
        'use_referer' => true,
        'username_parameter' => 'username',
        'password_parameter' => 'password',
        'csrf_protection' => true,
        'csrf_parameter' => 'csrf_token',
        'with_csrf' => true,
        'use_referer' => true,
    ),
    'logout' => array(
        'logout_path' => '/members-area/logout',
        'target' => '/members-area',
        'invalidate_session' => true,
        'csrf_parameter' => 'csrf_token',
        //'with_csrf' => true
    ),
    'remember_me' => array(
        'key' => 'someRandomRememberMeKey',
        'name' => 'user',
        'remember_me_parameter' => 'remember_me',
    ),
    'switch_user' => array(
        'parameter' => 'switch_user',
        'role' => 'ROLE_ALLOWED_TO_SWITCH',
    ),
    'users' => $app['user.provider'],
);

$app->register(
    new Silex\Provider\SecurityServiceProvider(),
    array(
        'security.firewalls' => $securityFirewalls,
    )
);

$app['security.access_rules'] = array(
    array('^/members-area/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
    array('^/members-area/register', 'IS_AUTHENTICATED_ANONYMOUSLY'),
    array('^/members-area/reset-password', 'IS_AUTHENTICATED_ANONYMOUSLY'),
    array('^/members-area', 'ROLE_USER'),
    array('^/members-area/oauth', 'ROLE_USER'),
);

$app['security.role_hierarchy'] = array(
    'ROLE_SUPER_ADMIN' => array('ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'),
    'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'),
);

/* Voters */
$app['security.voters'] = $app->extend(
    'security.voters',
    function ($voters) use ($app) {
        $voters[] = new Application\Security\Voter\ChatChannelVoter($app);
        $voters[] = new Application\Security\Voter\ChatChannelMessageVoter($app);

        return $voters;
    }
);

/***** Remember Me *****/
$app->register(new Silex\Provider\RememberMeServiceProvider());

/**** Listeners ****/
$app['dispatcher']->addListener(
    \Symfony\Component\Security\Core\AuthenticationEvents::AUTHENTICATION_SUCCESS,
    function ($event) use ($app) {
        $user = $event->getAuthenticationToken()->getUser();

        $user
            ->setTimeLastActive(
                new \DateTime()
            )
        ;

        $app['orm.em']->merge($user);
        $app['orm.em']->flush();
    }
);
