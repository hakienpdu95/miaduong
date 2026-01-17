<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ để đảm bảo seeder chạy sạch
        Template::truncate();

        // Danh sách templates với thông tin ánh xạ
        $templates = [
            [
                'name' => 'Default Post',
                'blade_path' => 'livewire.frontend.templates.blog-default',
                'item_type' => null, // Template mặc định, không gắn với item_type cụ thể
                'is_active' => true,
            ],
            [
                'name' => 'Standard Post',
                'blade_path' => 'livewire.frontend.templates.blog-standard',
                'item_type' => 'basic',
                'is_active' => true,
            ],
            [
                'name' => 'Redirect Post',
                'blade_path' => 'livewire.frontend.templates.blog-redirect',
                'item_type' => 'redirect',
                'is_active' => true,
            ],
            [
                'name' => 'Review Post',
                'blade_path' => 'livewire.frontend.templates.blog-review',
                'item_type' => 'review',
                'is_active' => true,
            ],
            [
                'name' => 'School Post',
                'blade_path' => 'livewire.frontend.templates.blog-school',
                'item_type' => 'school',
                'is_active' => true,
            ],
            [
                'name' => 'Quote Post',
                'blade_path' => 'livewire.frontend.templates.blog-quote',
                'item_type' => 'quote',
                'is_active' => true,
            ],
            [
                'name' => 'Full Width',
                'blade_path' => 'livewire.frontend.templates.full-width',
                'item_type' => null, // Template chung, không gắn với item_type
                'is_active' => true,
            ],
        ];

        // Tạo các bản ghi template
        foreach ($templates as $template) {
            Template::firstOrCreate(
                ['blade_path' => $template['blade_path']],
                [
                    'name' => $template['name'],
                    'item_type' => $template['item_type'],
                    'is_active' => $template['is_active'],
                ]
            );
        }
    }
}