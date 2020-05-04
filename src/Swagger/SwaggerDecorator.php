<?php


namespace App\Swagger;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SwaggerDecorator implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $docs['components']['schemas']['Credentials'] = [
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                ],
                'password' => [
                    'type' => 'string',
                ],
            ],
        ];
        $docs['components']['schemas']['Logout'] = [
            'type' => 'object',
            'properties' => [
                'logout' => [
                    'type' => 'boolean',
                ],
            ],
        ];

        $tokenDocumentation = [
            'paths' => [
                '/api/login' => [
                    'post' => [
                        'tags' => ['Authentication'],
                        'operationId' => 'login',
                        'summary' => 'Login to the system.',
                        'requestBody' => [
                            'description' => 'User credentials',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get user details',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/User-user:output',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/logout' => [
                    'post' => [
                        'tags' => ['Authentication'],
                        'operationId' => 'logout',
                        'summary' => 'Logout from the system.',
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get user details',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Logout',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }
}
