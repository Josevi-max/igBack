<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default CORS Settings
    |--------------------------------------------------------------------------
    |
    | Estas son las opciones predeterminadas para CORS.
    |
    */

    'paths' => ['api/*', 'broadcasting/auth'], // Permite /broadcasting/auth

    'allowed_methods' => ['*'], // Permite todos los métodos HTTP

    'allowed_origins' => ['http://localhost:4200'], // Dominio de tu frontend

    'allowed_origins_patterns' => [], // Opcional: Patrones adicionales de origen

    'allowed_headers' => ['Content-Type', 'Authorization'], // Encabezados permitidos

    'exposed_headers' => [], // Encabezados expuestos (opcional)

    'max_age' => 0, // Tiempo máximo de caché de la política CORS

    'supports_credentials' => true, // Necesario para enviar cookies/tokens
];