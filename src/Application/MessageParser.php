<?php

namespace Application;

class MessageParser
{
    protected $app;

    public function __construct(\Silex\Application $app = null)
    {
        $this->app = $app;
    }

    public function parseEmojis($content)
    {
        $output = $content;

        preg_match_all('/(:[a-zA-Z0-9_-]+:)/', $content, $hashMatches);

        $hashMatches = $hashMatches[0];

        if (!empty($hashMatches)) {
            $chatEmojis = array();

            $emojis = $this->app['autocomplete.emojis'];

            if ($emojis) {
                foreach ($emojis as $emoji) {
                    $chatEmojis[$emoji['name']] = $emoji;
                }

                foreach ($hashMatches as $hashMatch) {
                    $name = str_replace(':', '', $hashMatch);

                    if (array_key_exists($name, $chatEmojis)) {
                        $output = str_replace(
                            $hashMatch,
                            '<span class="emoji-icon" title="'.$chatEmojis[$name]['name'].
                            '" style="background-image: url(\''.$chatEmojis[$name]['image']['url']
                            .'\');"></span>',
                            $output
                        );
                    }
                }
            }
        }

        return $output;
    }

    public function parseChannels($content)
    {
        $output = $content;

        preg_match_all('/(#[a-zA-Z0-9_-]+)/', $content, $hashMatches);

        $hashMatches = $hashMatches[0];

        if (!empty($hashMatches)) {
            $chatChannels = array();

            $channels = $this->app['autocomplete.channels'];

            foreach ($channels as $channel) {
                $chatChannels[$channel['name']] = $channel;
            }

            foreach ($hashMatches as $hashMatch) {
                $name = str_replace('#', '', $hashMatch);

                if (array_key_exists($name, $chatChannels)) {
                    $output = str_replace(
                        $hashMatch,
                        '<a class="channel-link" data-channel="'.$name.'" href="'.$this
                            ->app['url_generator']
                            ->generate(
                                'members-area.chat.channels.detail',
                                array(
                                    'id' => $chatChannels[$name]['id'],
                                )
                            ).'">'.
                            $hashMatch.
                        '</a>',
                        $output
                    );
                }
            }
        }

        return $output;
    }

    public function parseUsers($content)
    {
        $output = $content;

        preg_match_all('/(@[a-zA-Z0-9_-]+)/', $content, $hashMatches);

        $hashMatches = $hashMatches[0];

        if (!empty($hashMatches)) {
            $chatUsers = array();

            $users = $this->app['autocomplete.users'];

            foreach ($users as $user) {
                $chatUsers[$user['username']] = $user;
            }

            foreach ($hashMatches as $hashMatch) {
                $name = str_replace('@', '', $hashMatch);

                if (array_key_exists($name, $chatUsers)) {
                    $output = str_replace(
                        $hashMatch,
                        '<a class="user-link" data-user="'.$name.'" href="'.$this
                            ->app['url_generator']
                            ->generate(
                                'members-area.chat.users.detail',
                                array(
                                    'id' => $chatUsers[$name]['id'],
                                )
                            ).'">'.
                            $hashMatch.
                        '</a>',
                        $output
                    );
                }
            }
        }

        return $output;
    }

    public function parse($content)
    {
        $output = $content;

        $output = $this->parseChannels($output);
        $output = $this->parseUsers($output);
        $output = $this->parseEmojis($output);

        /* $parser = new \Parsedown();
        $output = $parser->text($output); */

        $parser = new \Application\Markdown();
        $parser->html5 = true;
        $parser->enableNewlines = true;

        $output = $parser->parse($output);

        // $output = nl2br($output);

        return $output;
    }
}
