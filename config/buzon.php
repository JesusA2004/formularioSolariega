<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Correo del buzón
    |--------------------------------------------------------------------------
    |
    | Dirección que recibe la notificación automática cada vez que se
    | envía una nueva solicitud desde el formulario público.
    |
    */

    'mail_to' => env('BUZON_MAIL_TO', 'buzon@solariegacenit.com'),

    /*
    |--------------------------------------------------------------------------
    | Límites de adjuntos
    |--------------------------------------------------------------------------
    */

    'max_attachments' => 5,
    'max_attachment_size_kb' => 20480,

];
