<?php

declare(strict_types=1);

namespace CodeGenerator;

use CodeGenerator\Controller\CodeGeneratorController;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            CodeGeneratorController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'code-generator' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/code-generator[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => CodeGeneratorController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'code_generator' => __DIR__ . '/../view',
        ],
    ],
];