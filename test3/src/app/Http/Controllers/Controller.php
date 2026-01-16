<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API для системы комментариев",
    title: "Comment System API"
)]
#[OA\Server(
    url: "http://127.0.0.1/api/v1",
    description: "Local server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
abstract class Controller
{
}
