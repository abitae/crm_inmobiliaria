<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Client;
use App\Models\Document;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'abel.arana@hotmail.com')->first();
        if (!$admin) {
            throw new \Exception('No se encontró el usuario administrador. Asegúrate de ejecutar UserSeeder primero.');
        }

        $faker = Faker::create('es_PE');
        $users = User::all();
        $clients = Client::all();
        $projects = Project::all();
        $units = Unit::all();
        $opportunities = Opportunity::all();
        $activities = Activity::all();

        $documentTypes = ['contrato', 'factura', 'recibo', 'documento_legal', 'otros'];
        $categories = ['venta', 'alquiler', 'legal', 'marketing', 'otros'];
        $statuses = ['borrador', 'revisado', 'aprobado', 'firmado', 'archivado'];
        $extensions = ['pdf', 'docx', 'xlsx', 'jpg', 'png'];

        $documentCount = 5;
        for ($i = 0; $i < $documentCount; $i++) {
            $documentType = $faker->randomElement($documentTypes);
            $category = $faker->randomElement($categories);
            $status = $faker->randomElement($statuses);
            $extension = $faker->randomElement($extensions);
            $fileName = $faker->slug() . '.' . $extension;
            $filePath = '/storage/documents/' . $fileName;
            $createdBy = $users->random();

            $reviewedBy = in_array($status, ['revisado', 'aprobado', 'firmado', 'archivado']) ? $users->random() : null;
            $approvedBy = in_array($status, ['aprobado', 'firmado', 'archivado']) ? $users->random() : null;
            $signedBy = in_array($status, ['firmado', 'archivado']) ? $users->random() : null;

            Document::create([
                'title' => ucfirst($faker->words(rand(2, 4), true)),
                'description' => $faker->boolean(70) ? $faker->sentence(12) : null,
                'document_type' => $documentType,
                'category' => $category,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $faker->numberBetween(50_000, 5_000_000),
                'file_extension' => $extension,
                'mime_type' => $extension === 'pdf' ? 'application/pdf' : 'application/octet-stream',
                'version' => $faker->numberBetween(1, 3),
                'is_current_version' => $faker->boolean(85),
                'status' => $status,
                'client_id' => $clients->isNotEmpty() && $faker->boolean(60) ? $clients->random()->id : null,
                'project_id' => $projects->isNotEmpty() && $faker->boolean(50) ? $projects->random()->id : null,
                'unit_id' => $units->isNotEmpty() && $faker->boolean(40) ? $units->random()->id : null,
                'opportunity_id' => $opportunities->isNotEmpty() && $faker->boolean(35) ? $opportunities->random()->id : null,
                'activity_id' => $activities->isNotEmpty() && $faker->boolean(25) ? $activities->random()->id : null,
                'created_by' => $createdBy->id,
                'updated_by' => $faker->boolean(60) ? $users->random()->id : null,
                'reviewed_by' => $reviewedBy?->id,
                'reviewed_at' => $reviewedBy ? now()->subDays(rand(1, 60)) : null,
                'approved_by' => $approvedBy?->id,
                'approved_at' => $approvedBy ? now()->subDays(rand(1, 45)) : null,
                'signed_by' => $signedBy?->id,
                'signed_at' => $signedBy ? now()->subDays(rand(1, 30)) : null,
                'expiration_date' => $faker->boolean(30) ? now()->addDays(rand(30, 365)) : null,
                'tags' => $faker->boolean(35) ? [$faker->word(), $faker->word()] : null,
                'notes' => $faker->boolean(40) ? $faker->sentence(10) : null,
            ]);
        }

        $this->command->info('Documentos creados exitosamente');
    }
}
