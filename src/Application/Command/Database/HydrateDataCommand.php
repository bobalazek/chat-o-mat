<?php

namespace Application\Command\Database;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HydrateDataCommand
    extends ContainerAwareCommand
{
    protected $app;
    protected $consoleApp;

    public function __construct(
        $name,
        \Silex\Application $app
    ) {
        parent::__construct($name);

        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName(
                'application:database:hydrate-data'
            )
            ->setDescription('Add an Test User to the database')
            ->addOption(
                'remove-existing-data',
                'r',
                InputOption::VALUE_NONE,
                'When the existing data should be removed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->app;

        $removeExistingData = $input->getOption('remove-existing-data');

        if ($removeExistingData) {
            try {
                $app['db']->query('SET foreign_key_checks = 0;');

                $tables = $app['db']->getSchemaManager()->listTables();

                foreach ($tables as $table) {
                    $table = $table->getName();

                    $app['db']->query('TRUNCATE TABLE '.$table.';');
                }

                $app['db']->query('SET foreign_key_checks = 1;');

                $output->writeln('<info>All tables were successfully truncated!</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
        }

        /***** Users *****/
        $users = include APP_DIR.'/fixtures/users.php';

        foreach ($users as $user) {
            $userEntity = new \Application\Entity\UserEntity();
            $profileEntity = new \Application\Entity\ProfileEntity();

            // Profile
            $profileEntity
                ->setFirstName($user['profile']['firstName'])
                ->setLastName($user['profile']['lastName'])
            ;

            if (isset($user['profile']['gender'])) {
                $profileEntity
                    ->setGender($user['profile']['gender'])
                ;
            }

            if (isset($user['profile']['birthdate'])) {
                $profileEntity
                    ->setBirthdate($user['profile']['birthdate'])
                ;
            }

            // User
            $userEntity
                ->setId($user['id'])
                ->setUsername($user['username'])
                ->setEmail($user['email'])
                ->setPlainPassword(
                    $user['plainPassword'],
                    $app['security.encoder_factory']
                )
                ->setRoles($user['roles'])
                ->setProfile($profileEntity)
                ->enable()
            ;

            $app['orm.em']->persist($userEntity);
        }

        /***** Emojis *****/
        $emojis = include APP_DIR.'/fixtures/emojis.php';

        foreach ($emojis as $emoji) {
            $emojiEntity = new \Application\Entity\EmojiEntity();

            $emojiEntity
                ->setId($emoji['id'])
                ->setName($emoji['name'])
                ->setImageUrl($app['baseUrl'].$emoji['imageUrl'])
            ;

            $app['orm.em']->persist($emojiEntity);
        }

        /***** Chat channels *****/
        $chatChannels = include APP_DIR.'/fixtures/chat-channels.php';

        foreach ($chatChannels as $chatChannel) {
            $chatChannelEntity = new \Application\Entity\ChatChannelEntity();

            $chatChannelEntity
                ->setId($chatChannel['id'])
                ->setName($chatChannel['name'])
                ->setDescription($chatChannel['description'])
            ;

            $app['orm.em']->persist($chatChannelEntity);
        }

        try {
            $app['orm.em']->flush();

            $output->writeln('<info>Data was successfully hydrated!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
    }
}
