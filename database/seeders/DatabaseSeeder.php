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

            // 2. Usuarios (base - sin dependencias)
            UserSeeder::class,

            // 3. Entidades Principales (dependen de Users)
            //ClientSeeder::class,
            //ProjectSeeder::class,

            // 4. Entidades Secundarias (dependen de Projects y Users)
            //UnitSeeder::class,

            // 5. Entidades de Negocio (dependen de Clients, Projects, Units y Users)
            //OpportunitySeeder::class,
           // ReservationSeeder::class,

            // 6. Entidades de Comisiones (dependen de Users, Projects, Units y Opportunities)
            //CommissionSeeder::class,

            // 7. Entidades de Seguimiento (dependen de múltiples entidades)
            //ActivitySeeder::class,

            // 8. Relaciones Many-to-Many y Precios (dependen de todas las entidades anteriores)
            //RelationshipSeeder::class,
        ]);

        $this->command->info('¡Base de datos poblada exitosamente!');
        $this->command->info('Usuarios de prueba creados:');
        $this->command->info('- Admin: abel.arana@hotmail.com / lobomalo123');
        $this->command->info('- Líderes: maria.gonzalez@crm.com, carlos.rodriguez@crm.com / password');
        $this->command->info('- Vendedores: ana.martinez@crm.com, luis.perez@crm.com, sofia.lopez@crm.com, roberto.silva@crm.com / password');
        $this->command->info('- Dateros: pedro.ramirez@crm.com, laura.jimenez@crm.com, diego.morales@crm.com / password');
        $this->command->info('- Clientes: juan.perez@cliente.com, carmen.garcia@cliente.com, miguel.torres@cliente.com / password');
    }
}
