<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();

        // Verificar que exista el admin antes de continuar
        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador. Asegúrate de ejecutar UserSeeder primero.');
        }

        $projects = [
            [
                'name' => 'Residencial Miraflores Park',
                'description' => 'Exclusivo proyecto residencial en el corazón de Miraflores, con acabados de lujo y amenidades premium.',
                'project_type' => 'departamentos',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Arequipa 1234',
                'district' => 'Miraflores',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1194,
                'longitude' => -77.0333,
                'total_units' => 120,
                'available_units' => 45,
                'reserved_units' => 25,
                'sold_units' => 50,
                'blocked_units' => 0,
                'start_date' => '2023-01-15',
                'end_date' => '2025-06-30',
                'delivery_date' => '2025-12-31',
                'status' => 'activo',
                'path_images' => [
                    [
                        'type' => 'portada',
                        'path' => '/storage/projects/miraflores-park/portada.jpg',
                        'name' => 'Portada del Proyecto'
                    ],
                    [
                        'type' => 'interior',
                        'path' => '/storage/projects/miraflores-park/interior-1.jpg',
                        'name' => 'Interior de Departamento Modelo'
                    ],
                    [
                        'type' => 'exterior',
                        'path' => '/storage/projects/miraflores-park/exterior-1.jpg',
                        'name' => 'Vista Exterior del Edificio'
                    ],
                ],
                'path_videos' => [
                    [
                        'type' => 'tour',
                        'path' => '/storage/projects/miraflores-park/tour-virtual.mp4',
                        'name' => 'Tour Virtual del Proyecto'
                    ],
                    [
                        'type' => 'amenidades',
                        'path' => '/storage/projects/miraflores-park/amenidades.mp4',
                        'name' => 'Video de Amenidades'
                    ]
                ],
                'path_documents' => [
                    [
                        'type' => 'brochure',
                        'path' => '/storage/projects/miraflores-park/brochure.pdf',
                        'name' => 'Brochure Residencial Miraflores Park'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/miraflores-park/plano-general.pdf',
                        'name' => 'Plano General del Proyecto'
                    ],
                    [
                        'type' => 'contrato',
                        'path' => '/storage/projects/miraflores-park/contrato-modelo.pdf',
                        'name' => 'Contrato de Compra Venta'
                    ]
                ],
            ],
            [
                'name' => 'Torres San Isidro Business',
                'description' => 'Complejo de oficinas corporativas de alta gama en San Isidro, ideal para empresas multinacionales.',
                'project_type' => 'oficinas',
                'stage' => 'lanzamiento',
                'legal_status' => 'habilitado',
                'address' => 'Av. Javier Prado 2345',
                'district' => 'San Isidro',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.0972,
                'longitude' => -77.0267,
                'total_units' => 80,
                'available_units' => 60,
                'reserved_units' => 15,
                'sold_units' => 5,
                'blocked_units' => 0,
                'start_date' => '2024-03-01',
                'end_date' => '2026-08-31',
                'delivery_date' => '2026-12-31',
                'status' => 'activo',
                'path_images' => [
                    [
                        'type' => 'fachada',
                        'path' => '/storage/projects/san-isidro-business/fachada.jpg',
                        'name' => 'Fachada Principal del Edificio'
                    ],
                    [
                        'type' => 'oficina',
                        'path' => '/storage/projects/san-isidro-business/oficina-modelo.jpg',
                        'name' => 'Oficina Modelo'
                    ]
                ],
                'path_videos' => [
                    [
                        'type' => 'presentacion',
                        'path' => '/storage/projects/san-isidro-business/presentacion.mp4',
                        'name' => 'Presentación del Proyecto'
                    ],
                    [
                        'type' => 'recorrido',
                        'path' => '/storage/projects/san-isidro-business/recorrido.mp4',
                        'name' => 'Recorrido por las Instalaciones'
                    ]
                ],
                'path_documents' => [
                    [
                        'type' => 'brochure',
                        'path' => '/storage/projects/san-isidro-business/brochure-corporativo.pdf',
                        'name' => 'Brochure Corporativo Torres San Isidro'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/san-isidro-business/plano-oficinas.pdf',
                        'name' => 'Plano de Oficinas Disponibles'
                    ],
                    [
                        'type' => 'especificaciones',
                        'path' => '/storage/projects/san-isidro-business/especificaciones-tecnicas.pdf',
                        'name' => 'Especificaciones Técnicas'
                    ]
                ],
            ],
            [
                'name' => 'Lotes Barranco Golf',
                'description' => 'Exclusivos lotes residenciales con vista al mar en Barranco, perfectos para construir casas de lujo.',
                'project_type' => 'lotes',
                'stage' => 'preventa',
                'legal_status' => 'con_titulo',
                'address' => 'Av. Costanera 3456',
                'district' => 'Barranco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1419,
                'longitude' => -77.0217,
                'total_units' => 50,
                'available_units' => 40,
                'reserved_units' => 8,
                'sold_units' => 2,
                'blocked_units' => 0,
                'start_date' => '2024-06-01',
                'end_date' => '2027-12-31',
                'delivery_date' => '2027-12-31',
                'status' => 'activo',
                'path_images' => [
                    [
                        'type' => 'vista',
                        'path' => '/storage/projects/barranco-golf/vista-mar.jpg',
                        'name' => 'Vista al Mar desde los Lotes'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/barranco-golf/plano-lotes.jpg',
                        'name' => 'Plano de Lotes Disponibles'
                    ]
                ],
                'path_videos' => [
                    [
                        'type' => 'drone',
                        'path' => '/storage/projects/barranco-golf/drone-aereo.mp4',
                        'name' => 'Vista Aérea con Drone'
                    ],
                    [
                        'type' => 'ubicacion',
                        'path' => '/storage/projects/barranco-golf/ubicacion.mp4',
                        'name' => 'Video de Ubicación'
                    ]
                ],
                'path_documents' => [
                    [
                        'type' => 'brochure',
                        'path' => '/storage/projects/barranco-golf/brochure-lotes.pdf',
                        'name' => 'Brochure Lotes Barranco Golf'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/barranco-golf/plano-lotes.pdf',
                        'name' => 'Plano de Lotes Disponibles'
                    ],
                    [
                        'type' => 'reglamento',
                        'path' => '/storage/projects/barranco-golf/reglamento-condominio.pdf',
                        'name' => 'Reglamento de Condominio'
                    ]
                ],
            ],
            [
                'name' => 'Casas Surco Family',
                'description' => 'Proyecto de casas familiares en Surco, con amplios jardines y excelente conectividad.',
                'project_type' => 'casas',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Benavides 4567',
                'district' => 'Surco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1583,
                'longitude' => -76.9933,
                'total_units' => 60,
                'available_units' => 20,
                'reserved_units' => 15,
                'sold_units' => 25,
                'blocked_units' => 0,
                'start_date' => '2022-09-01',
                'end_date' => '2024-12-31',
                'delivery_date' => '2025-03-31',
                'status' => 'activo',
                'path_images' => [
                    [
                        'type' => 'casa',
                        'path' => '/storage/projects/surco-family/casa-modelo.jpg',
                        'name' => 'Casa Modelo'
                    ],
                    [
                        'type' => 'jardin',
                        'path' => '/storage/projects/surco-family/jardin.jpg',
                        'name' => 'Jardín de la Casa'
                    ]
                ],
                'path_videos' => [
                    [
                        'type' => 'tour',
                        'path' => '/storage/projects/surco-family/tour-casa.mp4',
                        'name' => 'Tour de la Casa'
                    ],
                    [
                        'type' => 'entorno',
                        'path' => '/storage/projects/surco-family/entorno.mp4',
                        'name' => 'Video del Entorno'
                    ]
                ],
                'path_documents' => [
                    [
                        'type' => 'brochure',
                        'path' => '/storage/projects/surco-family/brochure-familiar.pdf',
                        'name' => 'Brochure Casas Surco Family'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/surco-family/plano-casas.pdf',
                        'name' => 'Plano de Casas Disponibles'
                    ],
                    [
                        'type' => 'financiamiento',
                        'path' => '/storage/projects/surco-family/opciones-financiamiento.pdf',
                        'name' => 'Opciones de Financiamiento'
                    ]
                ],
            ],
            [
                'name' => 'Mixto Chorrillos Plaza',
                'description' => 'Proyecto mixto con departamentos, oficinas y locales comerciales en Chorrillos.',
                'project_type' => 'mixto',
                'stage' => 'lanzamiento',
                'legal_status' => 'en_tramite',
                'address' => 'Av. Primavera 5678',
                'district' => 'Chorrillos',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1750,
                'longitude' => -76.9917,
                'total_units' => 200,
                'available_units' => 180,
                'reserved_units' => 15,
                'sold_units' => 5,
                'blocked_units' => 0,
                'start_date' => '2024-01-01',
                'end_date' => '2027-06-30',
                'delivery_date' => '2027-12-31',
                'status' => 'activo',
                'path_images' => [
                    [
                        'type' => 'maqueta',
                        'path' => '/storage/projects/chorrillos-plaza/maqueta.jpg',
                        'name' => 'Maqueta del Proyecto'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/chorrillos-plaza/plano-general.jpg',
                        'name' => 'Plano General del Proyecto'
                    ]
                ],
                'path_videos' => [
                    [
                        'type' => 'concepto',
                        'path' => '/storage/projects/chorrillos-plaza/concepto.mp4',
                        'name' => 'Video del Concepto'
                    ],
                    [
                        'type' => 'desarrollo',
                        'path' => '/storage/projects/chorrillos-plaza/desarrollo.mp4',
                        'name' => 'Video del Desarrollo'
                    ]
                ],
                'path_documents' => [
                    [
                        'type' => 'brochure',
                        'path' => '/storage/projects/chorrillos-plaza/brochure-mixto.pdf',
                        'name' => 'Brochure Mixto Chorrillos Plaza'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/chorrillos-plaza/plano-mixto.pdf',
                        'name' => 'Plano General del Proyecto Mixto'
                    ],
                    [
                        'type' => 'estudio-mercado',
                        'path' => '/storage/projects/chorrillos-plaza/estudio-mercado.pdf',
                        'name' => 'Estudio de Mercado'
                    ]
                ],
            ],
            [
                'name' => 'Oficinas San Borja Center',
                'description' => 'Centro empresarial moderno en San Borja con oficinas flexibles y espacios de coworking.',
                'project_type' => 'oficinas',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Aviación 6789',
                'district' => 'San Borja',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'latitude' => -12.1083,
                'longitude' => -76.9917,
                'total_units' => 100,
                'available_units' => 35,
                'reserved_units' => 20,
                'sold_units' => 45,
                'blocked_units' => 0,
                'start_date' => '2023-06-01',
                'end_date' => '2025-12-31',
                'delivery_date' => '2026-03-31',
                'status' => 'activo',
                'path_images' => [
                    [
                        'type' => 'edificio',
                        'path' => '/storage/projects/san-borja-center/edificio.jpg',
                        'name' => 'Vista del Edificio'
                    ],
                    [
                        'type' => 'coworking',
                        'path' => '/storage/projects/san-borja-center/coworking.jpg',
                        'name' => 'Espacio de Coworking'
                    ]
                ],
                'path_videos' => [
                    [
                        'type' => 'instalaciones',
                        'path' => '/storage/projects/san-borja-center/instalaciones.mp4',
                        'name' => 'Video de las Instalaciones'
                    ],
                    [
                        'type' => 'ventajas',
                        'path' => '/storage/projects/san-borja-center/ventajas.mp4',
                        'name' => 'Video de Ventajas del Proyecto'
                    ]
                ],
                'path_documents' => [
                    [
                        'type' => 'brochure',
                        'path' => '/storage/projects/san-borja-center/brochure-empresarial.pdf',
                        'name' => 'Brochure Empresarial San Borja Center'
                    ],
                    [
                        'type' => 'plano',
                        'path' => '/storage/projects/san-borja-center/plano-oficinas.pdf',
                        'name' => 'Plano de Oficinas y Coworking'
                    ],
                    [
                        'type' => 'servicios',
                        'path' => '/storage/projects/san-borja-center/servicios-incluidos.pdf',
                        'name' => 'Servicios Incluidos'
                    ]
                ],
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create([
                ...$projectData,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }

        // Crear proyectos adicionales usando factory (comentado porque no existe ProjectFactory)
        // Project::factory(8)->create([
        //     'created_by' => $admin->id,
        //     'updated_by' => $admin->id,
        // ]);

        $this->command->info('Proyectos creados exitosamente');
    }
}
