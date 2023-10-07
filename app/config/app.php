<?php

/**
 * @author TriNT <trint@vng.com.vn>
 */
return [    
    'debug' => true,
    
    'sub_domain' => '',
    'view_static' => '',
    'path_log' => realpath(__DIR__ . '/../../') . '/logs/' . date('Y-m-d') . '.log',    
    'path_view' => realpath(__DIR__ . '/../../') . '/app/views/',
    
    'api' => [
        'url' => 'http://localhost:8777',
        'callee' => 'client-owner',
        'key' => 'client',
    ],
    'url' => [
        'err' => 'http://localhost:8000',
        'base' => 'http://localhost:8000',
        'client' => 'http://localhost:8001',
    ]
];
