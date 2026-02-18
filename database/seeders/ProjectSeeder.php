<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

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

        $faker = Faker::create('es_PE');
        $projects = [
            [
                'name' => 'Lotes Miraflores Park',
                'description' => 'Exclusivos lotes residenciales en el corazón de Miraflores, con ubicación privilegiada y amenidades premium.',
                'project_type' => 'lotes',
                'is_published' => true,
                'lote_type' => 'normal',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Arequipa 1234',
                'district' => 'Miraflores',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.1194,-77.0333',
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
                'is_published' => true,
                'lote_type' => 'express',
                'stage' => 'lanzamiento',
                'legal_status' => 'habilitado',
                'address' => 'Av. Javier Prado 2345',
                'district' => 'San Isidro',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.0972,-77.0267',
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
                'is_published' => true,
                'lote_type' => 'normal',
                'stage' => 'preventa',
                'legal_status' => 'con_titulo',
                'address' => 'Av. Costanera 3456',
                'district' => 'Barranco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.1419,-77.0217',
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
                'is_published' => false,
                'lote_type' => 'normal',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Benavides 4567',
                'district' => 'Surco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.1583,-76.9933',
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
                'is_published' => true,
                'lote_type' => 'express',
                'stage' => 'lanzamiento',
                'legal_status' => 'en_tramite',
                'address' => 'Av. Primavera 5678',
                'district' => 'Chorrillos',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.1750,-76.9917',
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
                'is_published' => true,
                'lote_type' => 'normal',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Aviación 6789',
                'district' => 'San Borja',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.1083,-76.9917',
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
            [
                'name' => 'Lotes Express La Molina',
                'description' => 'Lotes express en La Molina con entrega inmediata y documentación lista. Ideal para inversión rápida.',
                'project_type' => 'lotes',
                'is_published' => true,
                'lote_type' => 'express',
                'stage' => 'venta_activa',
                'legal_status' => 'habilitado',
                'address' => 'Av. La Molina 7890',
                'district' => 'La Molina',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.0750,-76.9500',
                'total_units' => 40,
                'available_units' => 15,
                'reserved_units' => 10,
                'sold_units' => 15,
                'blocked_units' => 0,
                'start_date' => '2024-01-01',
                'end_date' => '2025-12-31',
                'delivery_date' => '2025-06-30',
                'status' => 'activo',
                'path_image_portada' => '/storage/projects/la-molina-express/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/la-molina-express/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista de Lotes Express',
                        'path' => '/storage/projects/la-molina-express/vista-lotes.jpg',
                        'descripcion' => 'Vista de los lotes express disponibles'
                    ],
                    [
                        'title' => 'Plano de Lotes Express',
                        'path' => '/storage/projects/la-molina-express/plano-lotes.jpg',
                        'descripcion' => 'Plano detallado de lotes express'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Video de Lotes Express',
                        'path' => '/storage/projects/la-molina-express/video-lotes.mp4',
                        'descripcion' => 'Tour por los lotes express'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes Express La Molina',
                        'path' => '/storage/projects/la-molina-express/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes express'
                    ],
                    [
                        'title' => 'Documentación Lista',
                        'path' => '/storage/projects/la-molina-express/documentacion.pdf',
                        'descripcion' => 'Documentación lista para entrega inmediata'
                    ]
                ],
            ],
            [
                'name' => 'Lotes Express Surco Premium',
                'description' => 'Lotes express premium en Surco con ubicación estratégica y documentación completa. Entrega inmediata.',
                'project_type' => 'lotes',
                'is_published' => true,
                'lote_type' => 'express',
                'stage' => 'lanzamiento',
                'legal_status' => 'habilitado',
                'address' => 'Av. Caminos del Inca 8901',
                'district' => 'Surco',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.1400,-76.9900',
                'total_units' => 30,
                'available_units' => 25,
                'reserved_units' => 3,
                'sold_units' => 2,
                'blocked_units' => 0,
                'start_date' => '2024-03-15',
                'end_date' => '2026-03-31',
                'delivery_date' => '2025-09-30',
                'status' => 'activo',
                'path_image_portada' => '/storage/projects/surco-express/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/surco-express/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista Aérea Lotes Express',
                        'path' => '/storage/projects/surco-express/vista-aerea.jpg',
                        'descripcion' => 'Vista aérea de los lotes express'
                    ],
                    [
                        'title' => 'Plano de Lotes Premium',
                        'path' => '/storage/projects/surco-express/plano-lotes.jpg',
                        'descripcion' => 'Plano de lotes express premium'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Video Promocional Express',
                        'path' => '/storage/projects/surco-express/video-promo.mp4',
                        'descripcion' => 'Video promocional de lotes express'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes Express Surco',
                        'path' => '/storage/projects/surco-express/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes express premium'
                    ],
                    [
                        'title' => 'Documentación Completa',
                        'path' => '/storage/projects/surco-express/documentacion.pdf',
                        'descripcion' => 'Documentación completa lista'
                    ]
                ],
            ],
            [
                'name' => 'Lotes Express San Isidro Business',
                'description' => 'Lotes express comerciales en San Isidro, documentación lista y entrega inmediata para proyectos empresariales.',
                'project_type' => 'lotes',
                'is_published' => false,
                'lote_type' => 'express',
                'stage' => 'preventa',
                'legal_status' => 'habilitado',
                'address' => 'Av. Las Begonias 9012',
                'district' => 'San Isidro',
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=-12.1000,-77.0300',
                'total_units' => 25,
                'available_units' => 20,
                'reserved_units' => 3,
                'sold_units' => 2,
                'blocked_units' => 0,
                'start_date' => '2024-05-01',
                'end_date' => '2026-12-31',
                'delivery_date' => '2025-12-31',
                'status' => 'activo',
                'path_image_portada' => '/storage/projects/san-isidro-express/portada-lotes.jpg',
                'path_video_portada' => '/storage/projects/san-isidro-express/video-lotes.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista de Lotes Comerciales Express',
                        'path' => '/storage/projects/san-isidro-express/vista-lotes.jpg',
                        'descripcion' => 'Vista de lotes comerciales express'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Video de Lotes Express',
                        'path' => '/storage/projects/san-isidro-express/video-lotes.mp4',
                        'descripcion' => 'Video de lotes express comerciales'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Lotes Express San Isidro',
                        'path' => '/storage/projects/san-isidro-express/brochure-lotes.pdf',
                        'descripcion' => 'Catálogo de lotes express comerciales'
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

        // Crear proyectos adicionales (dataset grande)
        $this->createExtraProjects($admin, $faker);

        $this->command->info('Proyectos creados exitosamente');
    }

    private function createExtraProjects(User $admin, $faker): void
    {
        $districts = ['Miraflores', 'San Isidro', 'Barranco', 'Surco', 'Chorrillos', 'La Molina', 'San Borja', 'Pueblo Libre', 'Magdalena', 'Jesús María'];
        $stages = ['preventa', 'lanzamiento', 'venta_activa', 'cierre'];
        $legalStatuses = ['con_titulo', 'en_tramite', 'habilitado'];
        $statuses = ['activo', 'inactivo', 'suspendido', 'finalizado'];
        $loteTypes = ['normal', 'express'];
        $estadoLegal = ['Derecho Posesorio', 'Compra y Venta', 'Juez de Paz', 'Titulo de propiedad'];
        $tipoProyecto = ['propio', 'tercero'];
        $tipoFinanciamiento = ['contado', 'financiado'];
        $tipoCuenta = ['cuenta corriente', 'cuenta vista', 'cuenta ahorro'];

        $extraProjects = 2;
        for ($i = 0; $i < $extraProjects; $i++) {
            $totalUnits = rand(80, 250);
            $soldUnits = rand(0, (int) ($totalUnits * 0.5));
            $reservedUnits = rand(0, (int) ($totalUnits * 0.2));
            $blockedUnits = rand(0, (int) ($totalUnits * 0.05));
            $availableUnits = max(0, $totalUnits - $soldUnits - $reservedUnits - $blockedUnits);

            Project::create([
                'name' => 'Lotes ' . $faker->unique()->company() . ' ' . strtoupper($faker->lexify('??')),
                'description' => $faker->paragraphs(2, true),
                'project_type' => 'lotes',
                'is_published' => $faker->boolean(70),
                'lote_type' => $faker->randomElement($loteTypes),
                'stage' => $faker->randomElement($stages),
                'legal_status' => $faker->randomElement($legalStatuses),
                'address' => $faker->streetAddress(),
                'district' => $faker->randomElement($districts),
                'province' => 'Lima',
                'region' => 'Lima Metropolitana',
                'country' => 'Perú',
                'ubicacion' => 'https://maps.google.com/?q=' . $faker->latitude() . ',' . $faker->longitude(),
                'total_units' => $totalUnits,
                'available_units' => $availableUnits,
                'reserved_units' => $reservedUnits,
                'sold_units' => $soldUnits,
                'blocked_units' => $blockedUnits,
                'start_date' => $faker->dateTimeBetween('-2 years', '+3 months'),
                'end_date' => $faker->dateTimeBetween('+6 months', '+3 years'),
                'delivery_date' => $faker->dateTimeBetween('+1 year', '+4 years'),
                'status' => $faker->randomElement($statuses),
                'path_image_portada' => null,
                'path_video_portada' => null,
                'path_images' => null,
                'path_videos' => null,
                'path_documents' => null,
                'estado_legal' => $faker->randomElement($estadoLegal),
                'tipo_proyecto' => $faker->randomElement($tipoProyecto),
                'tipo_financiamiento' => $faker->randomElement($tipoFinanciamiento),
                'banco' => $faker->randomElement(['BCP', 'BBVA', 'Interbank', 'Scotiabank', null]),
                'tipo_cuenta' => $faker->randomElement($tipoCuenta),
                'cuenta_bancaria' => $faker->numerify('############'),
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }
    }
}
