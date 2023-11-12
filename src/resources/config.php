<?php

return [
    /**
     * JSON:API Server
     */
    'server' => [
        'name' => 'v1',
        'namespace' => '\\App\\JsonApi',
    ],

    'controller' => [
        'namespace' => '\\App\\Http\\Controllers\\Api'
    ],
    'models' => [
        /**
         * Change this configuration when generating Models
         * 
         * Default is `int`
         * 
         * Use `int`, `uuid`, or `uuid`
         * 
         * `int`    -   Default Auto-Incrementing IDs
         * `uuid`   -   UUID generated IDs
         * `ulid`   -   ULID generated IDs
         * 
         */
        'primary_type' => 'ulid',

        'namespace' => '\\App\\Models',
    ],
];