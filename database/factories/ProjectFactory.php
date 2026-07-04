<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $projects = [
            'CRM System',
            'Inventory Management',
            'Payroll System',
            'HR Portal',
            'School Management',
            'Hospital Management',
            'E-Commerce Website',
            'Task Management',
            'Customer Portal',
            'Help Desk',
            'Accounting System',
            'Attendance System',
            'Billing Software',
            'Booking System',
            'CMS Website',
            'ERP System',
            'Fleet Management',
            'Gym Management',
            'Hotel Management',
            'Library Management',
            'Logistics Portal',
            'Manufacturing ERP',
            'Medical Records',
            'Online Banking',
            'POS System',
            'Project Tracker',
            'Quality Management',
            'Recruitment Portal',
            'Restaurant Management',
            'Sales Dashboard',
            'School ERP',
            'Stock Management',
            'Student Portal',
            'Supplier Portal',
            'Support Ticketing',
            'Task Scheduler',
            'Time Tracking',
            'Transport Management',
            'Warehouse Management',
            'Visitor Management',
            'Vendor Portal',
            'Vehicle Tracking',
            'Learning Management',
            'Asset Management',
            'Employee Self Service',
            'Document Management',
            'Expense Tracker',
            'Leave Management',
            'Meeting Scheduler',
            'Performance Management',
        ];

        static $index = 0;

        return [
            'title' => $projects[$index++],
        ];
    }
}
