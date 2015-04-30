<?php

/*========== Index ==========*/
$app->mount(
    '/',
    new Application\ControllerProvider\IndexControllerProvider()
);

/*========== Members Area ==========*/
$app->mount(
    '/members-area',
    new Application\ControllerProvider\MembersAreaControllerProvider()
);

/******** My ********/
$app->mount(
    '/members-area/my',
    new Application\ControllerProvider\MembersArea\MyControllerProvider()
);

/******** Chat ********/
$app->mount(
    '/members-area/chat',
    new Application\ControllerProvider\MembersArea\ChatControllerProvider()
);

/******** Users ********/
$app->mount(
    '/members-area/users',
    new Application\ControllerProvider\MembersArea\UsersControllerProvider()
);

/******** Integrations ********/
$app->mount(
    '/members-area/integrations',
    new Application\ControllerProvider\MembersArea\IntegrationsControllerProvider()
);

/******** Emojis ********/
$app->mount(
    '/members-area/emojis',
    new Application\ControllerProvider\MembersArea\EmojisControllerProvider()
);

/******** Statistics ********/
$app->mount(
    '/members-area/statistics',
    new Application\ControllerProvider\MembersArea\StatisticsControllerProvider()
);

/******** Settings ********/
$app->mount(
    '/members-area/settings',
    new Application\ControllerProvider\MembersArea\SettingsControllerProvider()
);
