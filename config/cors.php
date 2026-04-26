<?php

return [
    'paths' => [ '*' ],
    'allowed_methods' => [ '*' ],
    // 'allowed_origins' => [ 'http://127.0.0.1:8001', 'http://127.0.0.1:8080', 'http://localhost:5173' ],
    'allowed_origins' => [ '*' ],

    //     'allowed_origins_patterns' => [],
    'allowed_headers' => [ '*' ],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
