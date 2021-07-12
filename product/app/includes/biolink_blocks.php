<?php

/* Check and get Pro notifications pack */
$pro_blocks = file_exists(ROOT_PATH . 'app/includes/pro_biolink_blocks.php') ? include ROOT_PATH . 'app/includes/pro_biolink_blocks.php' : [];

return array_merge(
    $pro_blocks,
    [
        'link',
        'text',
        'image',
        'mail',
        'soundcloud',
        'spotify',
        'youtube',
        'twitch',
        'vimeo',
        'tiktok',
    ]
);

