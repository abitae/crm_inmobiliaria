<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Roles y Permisos (base - sin dependencias)
            RolePermissionSeeder::class,

            // 2. Usuarios con Jerarquías (base - sin dependencias)
            UserSeeder::class,

            // 3. Jerarquías y Equipos (depende de Users)
            HierarchySeeder::class,

            // 4. Entidades Principales (dependen de Users)
            ClientSeeder::class,
            ProjectSeeder::class,

            // 5. Entidades Secundarias (dependen de Projects y Users)
            UnitSeeder::class,

            // 6. Entidades de Negocio (dependen de Clients, Projects, Units y Users)
            OpportunitySeeder::class,
            ReservationSeeder::class,

            // 7. Entidades de Comisiones (dependen de Users, Projects, Units y Opportunities)
            CommissionSeeder::class,

            // 8. Entidades de Seguimiento (dependen de múltiples entidades)
            ActivitySeeder::class,
            TaskSeeder::class,
            DocumentSeeder::class,

            // 9. Relaciones Many-to-Many y Precios (dependen de todas las entidades anteriores)
            RelationshipSeeder::class,

            // 10. Métricas de Equipo (depende de todas las entidades anteriores)
            TeamMetricsSeeder::class,
        ]);

        $this->command->info('¡Base de datos poblada exitosamente con jerarquías!');
        $this->command->info('');
        $this->command->info('🔐 USUARIOS DE PRUEBA:');
        $this->command->info('👑 Admin: abel.arana@hotmail.com / lobomalo123');
        $this->command->info('👥 Líderes fijos: maria.gonzalez@crm.com, carlos.rodriguez@crm.com / password');
        $this->command->info('💼 Vendedores/Dateros: usuarios generados con password "password"');
        $this->command->info('');
        $this->command->info('🏢 JERARQUÍAS ESTABLECIDAS:');
        $this->command->info('├── Admin (Abel Arana)');
        $this->command->info('│   ├── Líder 1 (María González)');
        $this->command->info('│   │   ├── Vendedor 1 (Ana Martínez)');
        $this->command->info('│   │   │   └── Datero 1 (Pedro Ramírez)');
        $this->command->info('│   │   └── Vendedor 2 (Luis Pérez)');
        $this->command->info('│   │       └── Datero 2 (Laura Jiménez)');
        $this->command->info('│   └── Líder 2 (Carlos Rodríguez)');
        $this->command->info('│       ├── Vendedor 3 (Sofia López)');
        $this->command->info('│       │   └── Datero 3 (Diego Morales)');
        $this->command->info('│       └── Vendedor 4 (Roberto Silva)');
        $this->command->info('├── Vendedor 5 (Miguel Torres) - Sin líder');
        $this->command->info('│   └── Datero 4 (Carmen García)');
        $this->command->info('└── Datero 5 (Juan Pérez) - Sin vendedor');
    }
}
