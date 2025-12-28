<?php

namespace Database\Seeders;

use App\Models\TestCategory;
use Illuminate\Database\Seeder;

class UpdateTestCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // 10 standard categories as requested
        $categories = [
            ['name' => 'Haematology', 'code' => 'HAEM', 'description' => 'Blood cell counts and related tests', 'sort_order' => 1],
            ['name' => 'Biochemistry', 'code' => 'BIOCHEM', 'description' => 'Blood chemistry, LFT, RFT, Lipid tests', 'sort_order' => 2],
            ['name' => 'Serology', 'code' => 'SEROL', 'description' => 'Serological and immunological tests', 'sort_order' => 3],
            ['name' => 'Clinical Pathology', 'code' => 'CLIN', 'description' => 'Urine, stool, and body fluid analysis', 'sort_order' => 4],
            ['name' => 'Cytology', 'code' => 'CYTO', 'description' => 'Cell examination and PAP smears', 'sort_order' => 5],
            ['name' => 'Microbiology', 'code' => 'MICRO', 'description' => 'Culture and sensitivity tests', 'sort_order' => 6],
            ['name' => 'Endocrinology', 'code' => 'ENDO', 'description' => 'Hormone and thyroid tests', 'sort_order' => 7],
            ['name' => 'Histopathology', 'code' => 'HISTO', 'description' => 'Tissue biopsy examination', 'sort_order' => 8],
            ['name' => 'Others', 'code' => 'OTHER', 'description' => 'Other specialized tests', 'sort_order' => 9],
            ['name' => 'Miscellaneous', 'code' => 'MISC', 'description' => 'Miscellaneous tests', 'sort_order' => 10],
        ];

        foreach ($categories as $cat) {
            TestCategory::updateOrCreate(
                ['code' => $cat['code']],
                $cat
            );
        }

        $this->command->info('10 standard test categories created/updated.');
    }
}
