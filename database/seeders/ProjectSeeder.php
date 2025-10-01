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
                'name' => 'Lotes Miraflores Park',
                'description' => 'Exclusivos lotes residenciales en el corazón de Miraflores, con ubicación privilegiada y amenidades premium.',
                'project_type' => 'lotes',
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
                'path_image_portada' => '/storage/projects/miraflores-park/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/miraflores-park/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista Aérea de los Lotes',
                        'path' => '/storage/projects/miraflores-park/vista-aerea.jpg',
                        'descripcion' => 'Vista aérea de todos los lotes disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes Disponibles',
                        'path' => '/storage/projects/miraflores-park/plano-lotes.jpg',
                        'descripcion' => 'Plano detallado de lotes disponibles'
                    ],
                    [
                        'title' => 'Vista del Entorno',
                        'path' => '/storage/projects/miraflores-park/entorno.jpg',
                        'descripcion' => 'Vista del entorno y ubicación privilegiada'
                    ],
                ],
                'path_videos' => [
                    [
                        'title' => 'Tour Virtual de los Lotes',
                        'path' => '/storage/projects/miraflores-park/tour-lotes.mp4',
                        'descripcion' => 'Recorrido virtual por todos los lotes'
                    ],
                    [
                        'title' => 'Video de Ubicación',
                        'path' => '/storage/projects/miraflores-park/ubicacion.mp4',
                        'descripcion' => 'Video explicativo de la ubicación privilegiada'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes Miraflores Park',
                        'path' => '/storage/projects/miraflores-park/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo completo de lotes disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes',
                        'path' => '/storage/projects/miraflores-park/plano-lotes.pdf',
                        'descripcion' => 'Plano detallado de todos los lotes'
                    ],
                    [
                        'title' => 'Contrato de Compra Venta',
                        'path' => '/storage/projects/miraflores-park/contrato-lotes.pdf',
                        'descripcion' => 'Contrato modelo para la compra de lotes'
                    ]
                ],
            ],
            [
                'name' => 'Lotes San Isidro Business',
                'description' => 'Exclusivos lotes comerciales en San Isidro, ideales para construir oficinas corporativas y edificios empresariales.',
                'project_type' => 'lotes',
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
                'path_image_portada' => '/storage/projects/san-isidro-business/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/san-isidro-business/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista Aérea de Lotes Comerciales',
                        'path' => '/storage/projects/san-isidro-business/vista-aerea.jpg',
                        'descripcion' => 'Vista aérea de los lotes comerciales disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes Comerciales',
                        'path' => '/storage/projects/san-isidro-business/plano-lotes.jpg',
                        'descripcion' => 'Plano detallado de lotes comerciales'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Presentación de Lotes Comerciales',
                        'path' => '/storage/projects/san-isidro-business/presentacion-lotes.mp4',
                        'descripcion' => 'Video de presentación de lotes comerciales'
                    ],
                    [
                        'title' => 'Recorrido por los Lotes',
                        'path' => '/storage/projects/san-isidro-business/recorrido-lotes.mp4',
                        'descripcion' => 'Tour completo por todos los lotes'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes San Isidro Business',
                        'path' => '/storage/projects/san-isidro-business/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes comerciales'
                    ],
                    [
                        'title' => 'Plano de Lotes Comerciales',
                        'path' => '/storage/projects/san-isidro-business/plano-lotes.pdf',
                        'descripcion' => 'Plano detallado de lotes comerciales'
                    ],
                    [
                        'title' => 'Especificaciones de Lotes',
                        'path' => '/storage/projects/san-isidro-business/especificaciones-lotes.pdf',
                        'descripcion' => 'Especificaciones técnicas de los lotes'
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
                'path_image_portada' => '/storage/projects/barranco-golf/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/barranco-golf/video-aereo.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista al Mar desde los Lotes',
                        'path' => '/storage/projects/barranco-golf/vista-mar.jpg',
                        'descripcion' => 'Vista panorámica del océano desde los lotes'
                    ],
                    [
                        'title' => 'Plano de Lotes Disponibles',
                        'path' => '/storage/projects/barranco-golf/plano-lotes.jpg',
                        'descripcion' => 'Plano detallado de lotes disponibles'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Vista Aérea con Drone',
                        'path' => '/storage/projects/barranco-golf/drone-aereo.mp4',
                        'descripcion' => 'Vista aérea capturada con drone'
                    ],
                    [
                        'title' => 'Video de Ubicación',
                        'path' => '/storage/projects/barranco-golf/ubicacion.mp4',
                        'descripcion' => 'Video explicativo de la ubicación'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes Barranco Golf',
                        'path' => '/storage/projects/barranco-golf/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes Disponibles',
                        'path' => '/storage/projects/barranco-golf/plano-lotes.pdf',
                        'descripcion' => 'Plano detallado de lotes'
                    ],
                    [
                        'title' => 'Reglamento de Condominio',
                        'path' => '/storage/projects/barranco-golf/reglamento-condominio.pdf',
                        'descripcion' => 'Reglamento interno del condominio'
                    ]
                ],
            ],
            [
                'name' => 'Lotes Surco Family',
                'description' => 'Lotes familiares en Surco, con amplios espacios y excelente conectividad para construir casas familiares.',
                'project_type' => 'lotes',
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
                'path_image_portada' => '/storage/projects/surco-family/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/surco-family/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista de Lotes Familiares',
                        'path' => '/storage/projects/surco-family/lotes-familiares.jpg',
                        'descripcion' => 'Vista de los lotes familiares disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes',
                        'path' => '/storage/projects/surco-family/plano-lotes.jpg',
                        'descripcion' => 'Plano detallado de lotes familiares'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Tour de Lotes Familiares',
                        'path' => '/storage/projects/surco-family/tour-lotes.mp4',
                        'descripcion' => 'Recorrido por todos los lotes familiares'
                    ],
                    [
                        'title' => 'Video del Entorno',
                        'path' => '/storage/projects/surco-family/entorno.mp4',
                        'descripcion' => 'Video del entorno y vecindario'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes Surco Family',
                        'path' => '/storage/projects/surco-family/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes familiares'
                    ],
                    [
                        'title' => 'Plano de Lotes Familiares',
                        'path' => '/storage/projects/surco-family/plano-lotes.pdf',
                        'descripcion' => 'Plano de lotes familiares disponibles'
                    ],
                    [
                        'title' => 'Opciones de Financiamiento',
                        'path' => '/storage/projects/surco-family/opciones-financiamiento.pdf',
                        'descripcion' => 'Documento con opciones de financiamiento'
                    ]
                ],
            ],
            [
                'name' => 'Lotes Chorrillos Plaza',
                'description' => 'Lotes mixtos en Chorrillos, ideales para construir proyectos residenciales, comerciales o mixtos.',
                'project_type' => 'lotes',
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
                'path_image_portada' => '/storage/projects/chorrillos-plaza/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/chorrillos-plaza/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista Aérea de Lotes Mixtos',
                        'path' => '/storage/projects/chorrillos-plaza/vista-aerea.jpg',
                        'descripcion' => 'Vista aérea de los lotes mixtos disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes Mixtos',
                        'path' => '/storage/projects/chorrillos-plaza/plano-lotes.jpg',
                        'descripcion' => 'Plano general de lotes mixtos'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Video de Lotes Mixtos',
                        'path' => '/storage/projects/chorrillos-plaza/video-lotes.mp4',
                        'descripcion' => 'Video explicativo de los lotes mixtos'
                    ],
                    [
                        'title' => 'Video del Desarrollo',
                        'path' => '/storage/projects/chorrillos-plaza/desarrollo.mp4',
                        'descripcion' => 'Video del desarrollo del proyecto'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes Chorrillos Plaza',
                        'path' => '/storage/projects/chorrillos-plaza/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes mixtos'
                    ],
                    [
                        'title' => 'Plano de Lotes Mixtos',
                        'path' => '/storage/projects/chorrillos-plaza/plano-lotes.pdf',
                        'descripcion' => 'Plano general de lotes mixtos'
                    ],
                    [
                        'title' => 'Estudio de Mercado',
                        'path' => '/storage/projects/chorrillos-plaza/estudio-mercado.pdf',
                        'descripcion' => 'Estudio de mercado del proyecto'
                    ]
                ],
            ],
            [
                'name' => 'Lotes San Borja Center',
                'description' => 'Lotes comerciales en San Borja, perfectos para construir edificios empresariales y centros de negocios.',
                'project_type' => 'lotes',
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
                'path_image_portada' => '/storage/projects/san-borja-center/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/san-borja-center/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista de Lotes Comerciales',
                        'path' => '/storage/projects/san-borja-center/lotes-comerciales.jpg',
                        'descripcion' => 'Vista de los lotes comerciales disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes Empresariales',
                        'path' => '/storage/projects/san-borja-center/plano-lotes.jpg',
                        'descripcion' => 'Plano de lotes empresariales'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Video de Lotes Empresariales',
                        'path' => '/storage/projects/san-borja-center/video-lotes.mp4',
                        'descripcion' => 'Tour por los lotes empresariales'
                    ],
                    [
                        'title' => 'Video de Ventajas del Proyecto',
                        'path' => '/storage/projects/san-borja-center/ventajas.mp4',
                        'descripcion' => 'Video explicativo de las ventajas'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes San Borja Center',
                        'path' => '/storage/projects/san-borja-center/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes empresariales'
                    ],
                    [
                        'title' => 'Plano de Lotes Empresariales',
                        'path' => '/storage/projects/san-borja-center/plano-lotes.pdf',
                        'descripcion' => 'Plano de lotes empresariales'
                    ],
                    [
                        'title' => 'Servicios Incluidos',
                        'path' => '/storage/projects/san-borja-center/servicios-incluidos.pdf',
                        'descripcion' => 'Lista de servicios incluidos'
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
