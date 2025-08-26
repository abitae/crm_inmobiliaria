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
                'path_image_portada' => '/storage/projects/miraflores-park/portada-principal.jpg',
                'path_video_portada' => '/storage/projects/miraflores-park/video-portada.mp4',
                'path_images' => [
                    [
                        'title' => 'Portada del Proyecto',
                        'path' => '/storage/projects/miraflores-park/portada.jpg',
                        'descripcion' => 'Imagen principal que representa el proyecto'
                    ],
                    [
                        'title' => 'Interior de Departamento Modelo',
                        'path' => '/storage/projects/miraflores-park/interior-1.jpg',
                        'descripcion' => 'Vista del interior de un departamento modelo'
                    ],
                    [
                        'title' => 'Vista Exterior del Edificio',
                        'path' => '/storage/projects/miraflores-park/exterior-1.jpg',
                        'descripcion' => 'Fachada principal del edificio residencial'
                    ],
                ],
                'path_videos' => [
                    [
                        'title' => 'Tour Virtual del Proyecto',
                        'path' => '/storage/projects/miraflores-park/tour-virtual.mp4',
                        'descripcion' => 'Recorrido virtual por todas las instalaciones'
                    ],
                    [
                        'title' => 'Video de Amenidades',
                        'path' => '/storage/projects/miraflores-park/amenidades.mp4',
                        'descripcion' => 'Presentación de las amenidades disponibles'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Residencial Miraflores Park',
                        'path' => '/storage/projects/miraflores-park/brochure.pdf',
                        'descripcion' => 'Catálogo completo del proyecto residencial'
                    ],
                    [
                        'title' => 'Plano General del Proyecto',
                        'path' => '/storage/projects/miraflores-park/plano-general.pdf',
                        'descripcion' => 'Plano arquitectónico del proyecto completo'
                    ],
                    [
                        'title' => 'Contrato de Compra Venta',
                        'path' => '/storage/projects/miraflores-park/contrato-modelo.pdf',
                        'descripcion' => 'Contrato modelo para la compra de unidades'
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
                'path_image_portada' => '/storage/projects/san-isidro-business/portada-corporativa.jpg',
                'path_video_portada' => '/storage/projects/san-isidro-business/video-corporativo.mp4',
                'path_images' => [
                    [
                        'title' => 'Fachada Principal del Edificio',
                        'path' => '/storage/projects/san-isidro-business/fachada.jpg',
                        'descripcion' => 'Vista frontal del edificio corporativo'
                    ],
                    [
                        'title' => 'Oficina Modelo',
                        'path' => '/storage/projects/san-isidro-business/oficina-modelo.jpg',
                        'descripcion' => 'Oficina modelo completamente equipada'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Presentación del Proyecto',
                        'path' => '/storage/projects/san-isidro-business/presentacion.mp4',
                        'descripcion' => 'Video corporativo de presentación'
                    ],
                    [
                        'title' => 'Recorrido por las Instalaciones',
                        'path' => '/storage/projects/san-isidro-business/recorrido.mp4',
                        'descripcion' => 'Tour completo por todas las instalaciones'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Corporativo Torres San Isidro',
                        'path' => '/storage/projects/san-isidro-business/brochure-corporativo.pdf',
                        'descripcion' => 'Catálogo corporativo del proyecto'
                    ],
                    [
                        'title' => 'Plano de Oficinas Disponibles',
                        'path' => '/storage/projects/san-isidro-business/plano-oficinas.pdf',
                        'descripcion' => 'Plano detallado de oficinas disponibles'
                    ],
                    [
                        'title' => 'Especificaciones Técnicas',
                        'path' => '/storage/projects/san-isidro-business/especificaciones-tecnicas.pdf',
                        'descripcion' => 'Documento técnico con especificaciones'
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
                'path_image_portada' => '/storage/projects/surco-family/portada-familiar.jpg',
                'path_video_portada' => '/storage/projects/surco-family/video-familiar.mp4',
                'path_images' => [
                    [
                        'title' => 'Casa Modelo',
                        'path' => '/storage/projects/surco-family/casa-modelo.jpg',
                        'descripcion' => 'Vista frontal de la casa modelo'
                    ],
                    [
                        'title' => 'Jardín de la Casa',
                        'path' => '/storage/projects/surco-family/jardin.jpg',
                        'descripcion' => 'Jardín trasero de la casa'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Tour de la Casa',
                        'path' => '/storage/projects/surco-family/tour-casa.mp4',
                        'descripcion' => 'Recorrido completo por la casa'
                    ],
                    [
                        'title' => 'Video del Entorno',
                        'path' => '/storage/projects/surco-family/entorno.mp4',
                        'descripcion' => 'Video del entorno y vecindario'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Casas Surco Family',
                        'path' => '/storage/projects/surco-family/brochure-familiar.pdf',
                        'descripcion' => 'Catálogo familiar del proyecto'
                    ],
                    [
                        'title' => 'Plano de Casas Disponibles',
                        'path' => '/storage/projects/surco-family/plano-casas.pdf',
                        'descripcion' => 'Plano de casas disponibles'
                    ],
                    [
                        'title' => 'Opciones de Financiamiento',
                        'path' => '/storage/projects/surco-family/opciones-financiamiento.pdf',
                        'descripcion' => 'Documento con opciones de financiamiento'
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
                'path_image_portada' => '/storage/projects/chorrillos-plaza/portada-mixto.jpg',
                'path_video_portada' => '/storage/projects/chorrillos-plaza/video-mixto.mp4',
                'path_images' => [
                    [
                        'title' => 'Maqueta del Proyecto',
                        'path' => '/storage/projects/chorrillos-plaza/maqueta.jpg',
                        'descripcion' => 'Maqueta arquitectónica del proyecto'
                    ],
                    [
                        'title' => 'Plano General del Proyecto',
                        'path' => '/storage/projects/chorrillos-plaza/plano-general.jpg',
                        'descripcion' => 'Plano general del proyecto mixto'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Video del Concepto',
                        'path' => '/storage/projects/chorrillos-plaza/concepto.mp4',
                        'descripcion' => 'Video explicativo del concepto'
                    ],
                    [
                        'title' => 'Video del Desarrollo',
                        'path' => '/storage/projects/chorrillos-plaza/desarrollo.mp4',
                        'descripcion' => 'Video del desarrollo del proyecto'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Mixto Chorrillos Plaza',
                        'path' => '/storage/projects/chorrillos-plaza/brochure-mixto.pdf',
                        'descripcion' => 'Catálogo del proyecto mixto'
                    ],
                    [
                        'title' => 'Plano General del Proyecto Mixto',
                        'path' => '/storage/projects/chorrillos-plaza/plano-mixto.pdf',
                        'descripcion' => 'Plano general del proyecto'
                    ],
                    [
                        'title' => 'Estudio de Mercado',
                        'path' => '/storage/projects/chorrillos-plaza/estudio-mercado.pdf',
                        'descripcion' => 'Estudio de mercado del proyecto'
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
                'path_image_portada' => '/storage/projects/san-borja-center/portada-empresarial.jpg',
                'path_video_portada' => '/storage/projects/san-borja-center/video-empresarial.mp4',
                'path_images' => [
                    [
                        'title' => 'Vista del Edificio',
                        'path' => '/storage/projects/san-borja-center/edificio.jpg',
                        'descripcion' => 'Vista frontal del edificio empresarial'
                    ],
                    [
                        'title' => 'Espacio de Coworking',
                        'path' => '/storage/projects/san-borja-center/coworking.jpg',
                        'descripcion' => 'Área de coworking disponible'
                    ]
                ],
                'path_videos' => [
                    [
                        'title' => 'Video de las Instalaciones',
                        'path' => '/storage/projects/san-borja-center/instalaciones.mp4',
                        'descripcion' => 'Tour por todas las instalaciones'
                    ],
                    [
                        'title' => 'Video de Ventajas del Proyecto',
                        'path' => '/storage/projects/san-borja-center/ventajas.mp4',
                        'descripcion' => 'Video explicativo de las ventajas'
                    ]
                ],
                'path_documents' => [
                    [
                        'title' => 'Brochure Empresarial San Borja Center',
                        'path' => '/storage/projects/san-borja-center/brochure-empresarial.pdf',
                        'descripcion' => 'Catálogo empresarial del proyecto'
                    ],
                    [
                        'title' => 'Plano de Oficinas y Coworking',
                        'path' => '/storage/projects/san-borja-center/plano-oficinas.pdf',
                        'descripcion' => 'Plano de oficinas y espacios coworking'
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
