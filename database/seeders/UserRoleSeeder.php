<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'title' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'title' => 'Admin',
                'description' => 'Administrative access with user management',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'title' => 'Telecaller',
                'description' => 'Telecaller access for lead management',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'title' => 'Admission Counsellor',
                'description' => 'Admission Counsellor access for Converted lead management',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'title' => 'Academic Assistant',
                'description' => 'Academic Assistant access for Converted lead management',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'title' => 'Finance',
                'description' => 'Finance department access for financial management',
                'is_active' => true,
            ],
            [
                'id' => 7,
                'title' => 'Post-sales',
                'description' => 'Post-sales department access for customer support',
                'is_active' => true,
            ],
            [
                'id' => 8,
                'title' => 'Support Team',
                'description' => 'Support Team access for customer support and assistance',
                'is_active' => true,
            ],
            [
                'id' => 9,
                'title' => 'Mentor',
                'description' => 'Mentor access for guidance and mentoring',
                'is_active' => true,
            ],
            [
                'id' => 10,
                'title' => 'Teacher',
                'description' => 'Teacher access for teaching and student management',
                'is_active' => true,
            ],
            [
                'id' => 11,
                'title' => 'General Manager',
                'description' => 'General Manager with extended access over leads and teams',
                'is_active' => true,
            ],
            [
                'id' => 12,
                'title' => 'Auditor',
                'description' => 'Auditor access with read-only permissions for leads, converted leads, reports and tracking',
                'is_active' => true,
            ],
            [
                'id' => 13,
                'title' => 'Marketing',
                'description' => 'Marketing team access for marketing activities',
                'is_active' => true,
            ],
            [
                'id' => 14,
                'title' => 'HOD',
                'description' => 'Head of Department access',
                'is_active' => true,
            ],
            [
                'id' => 15,
                'title' => 'Placement Manager',
                'description' => 'Placement Manager role with placement list access',
                'is_active' => true,
            ],
            [
                'id' => 16,
                'title' => 'Faculty',
                'description' => 'Faculty role with mentor converted-leads access',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            UserRole::updateOrCreate(
                ['id' => $role['id']],
                $role
            );
        }
    }
}
