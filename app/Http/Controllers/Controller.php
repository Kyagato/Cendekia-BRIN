<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'MojoPedia API',
    description: 'REST API untuk sistem manajemen pengetahuan MojoPedia. Menyediakan endpoint untuk manajemen user, role, dan integrasi Keycloak SSO.',
    contact: new OA\Contact(
        name: 'Tim Pengembang MojoPedia',
        email: 'admin@mojopedia.go.id'
    )
)]
#[OA\Server(
    url: '/api',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Masukkan token Sanctum Anda. Dapatkan token melalui endpoint login.'
)]
#[OA\Tag(name: 'System', description: 'Endpoint status dan health check sistem')]
#[OA\Tag(name: 'Authentication', description: 'Endpoint untuk autentikasi dan manajemen token')]
#[OA\Tag(name: 'Users', description: 'Endpoint untuk manajemen data user')]
#[OA\Tag(name: 'Roles', description: 'Endpoint untuk manajemen role user (terintegrasi dengan Keycloak Realm Roles)')]
#[OA\Tag(name: 'Categories', description: 'Endpoint untuk kategori dan klasifikasi repositori')]
#[OA\Tag(name: 'Knowledge', description: 'Endpoint repositori dokumen, riset, dan aset multimedia')]
#[OA\Tag(name: 'Forum', description: 'Endpoint diskusi komunitas, thread topik, dan balasan')]
abstract class Controller
{
    #[OA\Get(
        path: '/health-check',
        summary: 'Cek Status Server / API',
        tags: ['System'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'API running successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'healthy')
                    ]
                )
            )
        ]
    )]
    public function healthCheck()
    {
        return response()->json(['status' => 'healthy']);
    }
}