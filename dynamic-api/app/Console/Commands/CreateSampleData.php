<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentType;
use App\Models\FieldDefinition;
use App\Models\ContentEntry;

class CreateSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-sample-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample content types, fields, and entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating sample data...');

        // Create Employee Content Type (or update existing)
        $employeeType = ContentType::updateOrCreate(
            ['slug' => 'employee'],
            [
                'name' => 'Employee',
                'description' => 'Employee management form with personal and job information',
                'is_active' => true,
                'require_authentication' => false,
                'api_rate_limit' => 100,
            ]
        );

        // Create Employee Fields
        FieldDefinition::updateOrCreate(
            ['content_type_id' => $employeeType->id, 'name' => 'EmployeeName'],
            ['label' => 'Employee Name', 'type' => 'string']
        );

        FieldDefinition::updateOrCreate(
            ['content_type_id' => $employeeType->id, 'name' => 'EmployeeSalary'],
            ['label' => 'Employee Salary', 'type' => 'integer']
        );

        FieldDefinition::updateOrCreate(
            ['content_type_id' => $employeeType->id, 'name' => 'EmployeeDepartment'],
            ['label' => 'Employee Department', 'type' => 'string']
        );

        // Create Student Content Type (or update existing)
        $studentType = ContentType::updateOrCreate(
            ['slug' => 'student'],
            [
                'name' => 'Student',
                'description' => 'Student registration and management form',
                'is_active' => true,
                'require_authentication' => false,
                'api_rate_limit' => 50,
            ]
        );

        // Create Student Fields
        FieldDefinition::updateOrCreate(
            ['content_type_id' => $studentType->id, 'name' => 'name'],
            ['label' => 'Student Name', 'type' => 'string']
        );

        FieldDefinition::updateOrCreate(
            ['content_type_id' => $studentType->id, 'name' => 'age'],
            ['label' => 'Age', 'type' => 'integer']
        );

        FieldDefinition::updateOrCreate(
            ['content_type_id' => $studentType->id, 'name' => 'email'],
            ['label' => 'Email Address', 'type' => 'email']
        );

        FieldDefinition::updateOrCreate(
            ['content_type_id' => $studentType->id, 'name' => 'is_active'],
            ['label' => 'Active Student', 'type' => 'boolean']
        );

        // Create sample entries for Employee (only if none exist)
        if ($employeeType->entries()->count() === 0) {
            ContentEntry::create([
                'content_type_id' => $employeeType->id,
                'data' => [
                    'EmployeeName' => 'John Smith',
                    'EmployeeSalary' => 75000,
                    'EmployeeDepartment' => 'Engineering',
                ],
            ]);

            ContentEntry::create([
                'content_type_id' => $employeeType->id,
                'data' => [
                    'EmployeeName' => 'Sarah Johnson',
                    'EmployeeSalary' => 85000,
                    'EmployeeDepartment' => 'Marketing',
                ],
            ]);

            ContentEntry::create([
                'content_type_id' => $employeeType->id,
                'data' => [
                    'EmployeeName' => 'Mike Davis',
                    'EmployeeSalary' => 65000,
                    'EmployeeDepartment' => 'Finance',
                ],
            ]);
        }

        // Create sample entries for Student (only if none exist)
        if ($studentType->entries()->count() === 0) {
            ContentEntry::create([
                'content_type_id' => $studentType->id,
                'data' => [
                    'name' => 'Alice Wilson',
                    'age' => 20,
                    'email' => 'alice@university.com',
                    'is_active' => true,
                ],
            ]);

            ContentEntry::create([
                'content_type_id' => $studentType->id,
                'data' => [
                    'name' => 'Bob Chen',
                    'age' => 22,
                    'email' => 'bob@university.com',
                    'is_active' => true,
                ],
            ]);
        }

        $this->info('Sample data created successfully!');
        $this->info('Content Types:');
        $this->info("- Employee ({$employeeType->entries()->count()} entries)");
        $this->info("- Student ({$studentType->entries()->count()} entries)");
        $this->info('');
        $this->info('You can now access the admin panel at http://localhost:8001/admin');
        $this->info('When you click "Edit" on a content type, you will see tabs for:');
        $this->info('1. Fields - Manage form fields');
        $this->info('2. Submitted Entries - View all submitted data');
        $this->info('3. Settings in the main form (Basic Information + Settings tabs)');
    }
}
