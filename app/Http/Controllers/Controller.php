<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Laravel Fundamental API Documentation",
    description: "Dokumentasi API Swagger / OpenAPI",
    contact: new OA\Contact(
        email: "admin@example.com"
    )
)]
#[OA\Server(
    url: "http://127.0.0.1:8000/api",
    description: "Local Development Server"
)]
abstract class Controller
{
    #[OA\Get(
        path: "/health-check",
        summary: "Cek Status Server / API",
        tags: ["System"],
        responses: [
            new OA\Response(
                response: 200,
                description: "API running successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "healthy")
                    ]
                )
            )
        ]
    )]
    public function healthCheck()
    {
        //
    }
}