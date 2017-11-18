<?php

return [
    'meta'      => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'title'        => 'NambaNamba Free Music Downloads', // set false to total remove
            'description'  => 'Download and Stream South African Hip Hop, House MP3 Albums and Singles Music and Many More  Free at NambaNamba.COM. You can also download Full International hip hop Albums and Singles', // set false to total remove
            'separator'    => ' - ',
            'keywords'     => ['download mzansi hip hop', 'download south african hip hop mp3', 'south african music downloads', 'free mp3 music download south africa'],
            'canonical'    => false, // Set null for using Url::current(), set false to total remove
        ],

        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => "4335B7F5265B9BABFE4771B9614EB3F4",
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => "43d5ae676b22300d",
        ],
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title'       => 'NambaNamba Free Music Downloads', // set false to total remove
            'description' => 'Download and Stream South African Hip Hop, House MP3 Albums and Singles Music and Many More  Free at NambaNamba.COM. You can also download Full International hip hop Albums and Singles', // set false to total remove
            'url'         => false, // Set null for using Url::current(), set false to total remove
            'type'        => false,
            'site_name'   => false,
            'images'      => [],
        ],
    ],
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
          //'card'        => 'summary',
          //'site'        => '@LuizVinicius73',
        ],
    ],
];
