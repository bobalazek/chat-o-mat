<?php

$emojisArray = array();

$emojis = array(
    'amused',  'amused3', 'amused5',
    'angry32', 'angry35', 'angry36',
    'angry37', 'astonished', 'astonished3',
    'black387', 'emoticon105', 'emoticon106',
    'emoticon111', 'emoticon112', 'emoticon116',
    'eyeglasses19', 'face6', 'fake2',
    'false', 'flashed1', 'flashed2',
    'flashed4', 'gringing9', 'kissing1',
    'kissing3', 'laughing1', 'laughing2',
    'laughing3', 'moustache10', 'neutral3',
    'neutral5', 'neutral7', 'old56',
    'open200', 'sad46', 'sad47',
    'sad48', 'sad50', 'sad52',
    'sad56', 'sad59', 'sad60',
    'sad61', 'sad62',
    'smiling48', 'smiling50', 'smiling52',
    'smiling53', 'smiling54', 'smiling56',
    'smirking', 'smirking', 'unamused1',
    'winking12', 'winking13', 'winking15',
    'worried4', 'worried6', 'worried7',
);

foreach ($emojis as $emojiKey => $emoji) {
    $emojisArray[] = array(
        'id' => $emojiKey + 1,
        'name' => $emoji,
        'imageUrl' => 'assets/images/emojis/'.$emoji.'.png',
    );
}

return $emojisArray;
