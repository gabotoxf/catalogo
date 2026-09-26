<?php

// CORS del API: permite al frontend dev (Vite) y a los deploys.
// `HandleCors` ya está registrado en bootstrap/app.php para rutas api/*.
return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:5174',
        'http://localhost',
        'http://localhost:8000',
        'https://ecommerce-prod.up.railway.app',
        'https://chaparro-ecommerce.onrender.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
