<?php

namespace Database\Seeders;

use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestParameter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestsFromJsonSeeder extends Seeder
{
    /**
     * Map JSON file to category name
     */
    protected $categoryMap = [
        'hametology.json' => 'Haematology',
        'Biochemistry.json' => 'Biochemistry',
        'Clinical Pathology.json' => 'Clinical Pathology',
        'Serology and immunology.json' => 'Serology & Immunology',
        'endocrinology.json' => 'Endocrinology',
        'microbiology.json' => 'Microbiology',
        'panel-test.json' => 'Panel Tests',
    ];

    /**
     * Short names for common tests
     */
    protected $shortNames = [
        'complete_blood_count' => 'CBC',
        'liver_function_test' => 'LFT',
        'kidney_function_test' => 'KFT',
        'lipid_profile' => 'Lipid Panel',
        'thyroid_function_test_tft' => 'TFT',
        'free_thyroid_function_test_ft3' => 'FT3/FT4',
        'blood_sugar_fasting_pp' => 'FBS/PPBS',
        'bilirubin_total_direct_indirect' => 'S.Bili',
        'bt_ct' => 'BT/CT',
        'iron_studies' => 'Iron Panel',
        'viral_markers' => 'Viral Markers',
        'differential_leucocyte_count' => 'DLC',
        'total_leucocyte_count' => 'TLC',
        'erythrocyte_sedimentation_rate_westergren' => 'ESR (Westergren)',
        'erythrocyte_sedimentation_rate_wintrobe' => 'ESR (Wintrobe)',
        'hemoglobin' => 'Hb',
        'activated_partial_thromboplastin_time_aptt' => 'APTT',
        'prothrombin_time_pt_inr' => 'PT/INR',
        'platelet_count' => 'Plt',
        'total_rbc_count' => 'RBC',
        'fasting_blood_sugar' => 'FBS',
        'random_blood_sugar' => 'RBS',
        'blood_sugar_pp' => 'PPBS',
        'serum_creatinine' => 'S.Cr',
        'serum_urea' => 'S.Urea',
        'serum_uric_acid' => 'S.UA',
        'serum_albumin' => 'S.Alb',
        'serum_protein' => 'S.Prot',
        'serum_bilirubin_total' => 'S.Bili-T',
        'serum_bilirubin_direct' => 'S.Bili-D',
        'serum_calcium' => 'S.Ca',
        'serum_potassium' => 'S.K',
        'serum_sodium' => 'S.Na',
        'serum_chloride' => 'S.Cl',
        'sgot_ast' => 'SGOT/AST',
        'sgpt_alt' => 'SGPT/ALT',
        'total_cholesterol' => 'T.Chol',
        'triglycerides' => 'TG',
        'hdl_cholesterol' => 'HDL',
        'ldl_cholesterol' => 'LDL',
        'vldl_cholesterol' => 'VLDL',
        'thyroid_stimulating_hormone_tsh' => 'TSH',
        'hba1c' => 'HbA1c',
        'c_reactive_protein_crp_quantitative' => 'CRP',
        'rheumatoid_factor_ra_quantitative' => 'RF',
        'anti_nuclear_antibody_ana' => 'ANA',
        'antistreptolysin_o_aso_titer' => 'ASO',
        'beta_human_chorionic_gonadotropin_hcg' => 'β-hCG',
        'hiv_card_test' => 'HIV',
        'hepatitis_c_virus_hcv' => 'HCV',
        'hbsag' => 'HBsAg',
        'vdrl' => 'VDRL',
    ];

    public function run(): void
    {
        $this->command->info('Starting test import from JSON files...');

        // Clear existing tests and parameters
        $this->command->info('Clearing existing tests and parameters...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        TestParameter::truncate();
        Test::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $jsonPath = base_path('test-json');
        $files = glob($jsonPath . '/*.json');

        foreach ($files as $file) {
            $filename = basename($file);
            $categoryName = $this->categoryMap[$filename] ?? str_replace(['.json', '_', '-'], ['', ' ', ' '], $filename);

            $this->command->info("Processing: {$filename} -> Category: {$categoryName}");

            // Create or get category
            $category = TestCategory::firstOrCreate(
                ['name' => $categoryName],
                ['is_active' => true, 'sort_order' => 1]
            );

            // Parse JSON
            $data = json_decode(file_get_contents($file), true);

            if (!isset($data['tests'])) {
                $this->command->warn("No tests found in {$filename}");
                continue;
            }

            $interpretations = $data['interpretations'] ?? [];

            foreach ($data['tests'] as $testKey => $parameters) {
                $testName = $this->formatTestName($testKey);
                $shortName = $this->shortNames[$testKey] ?? null;
                $testCode = $shortName ?? strtoupper(Str::slug($testKey, '_'));

                // Get interpretation for this test
                $interpretation = $interpretations[$testKey] ?? null;
                // Also check alternate keys for interpretation
                if (!$interpretation) {
                    foreach ($interpretations as $iKey => $iValue) {
                        if (Str::contains($testKey, $iKey) || Str::contains($iKey, $testKey)) {
                            $interpretation = $iValue;
                            break;
                        }
                    }
                }

                // Create the test
                $test = Test::create([
                    'category_id' => $category->id,
                    'name' => $testName,
                    'code' => $testCode,
                    'short_name' => $shortName,
                    'price' => rand(100, 500),
                    'sample_type' => $this->guessSampleType($testName),
                    'interpretation' => $interpretation,
                    'is_active' => true,
                ]);

                $this->command->line("  Created test: {$testName}" . ($shortName ? " ({$shortName})" : ""));

                // Add parameters (handling nested groups)
                $order = 1;
                $this->addParameters($test, $parameters, null, $order);
            }
        }

        $this->command->info('Test import completed!');
        $this->command->info('Categories: ' . TestCategory::count());
        $this->command->info('Tests: ' . Test::count());
        $this->command->info('Parameters: ' . TestParameter::count());
    }

    /**
     * Add parameters recursively, handling groups
     */
    protected function addParameters($test, $parameters, $groupName, &$order)
    {
        foreach ($parameters as $paramKey => $paramData) {
            // Check if this is a nested group (contains objects, not value/unit/reference)
            if (is_array($paramData) && !isset($paramData['value']) && !isset($paramData['unit'])) {
                // This is a GROUP - DON'T create a separate header parameter
                // Just use the group name for child parameters
                $groupDisplayName = $this->formatTestName((string)$paramKey);

                // Recursively add parameters inside this group
                $this->addParameters($test, $paramData, $groupDisplayName, $order);
            } else {
                // This is a regular parameter
                $paramName = $this->formatParamName((string)$paramKey);

                // Get short name for parameter
                $paramShortName = $this->shortNames[$paramKey] ?? null;

                // Generate code - ensure it's never empty
                $code = $paramShortName ?? strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string)$paramKey));
                if (empty($code) || is_numeric($code)) {
                    $code = 'P' . $test->id . '_' . $order;
                }

                // Parse reference range
                $refRange = $paramData['reference'] ?? '';
                $normalMin = null;
                $normalMax = null;

                if (preg_match('/(\d+\.?\d*)\s*[-–]\s*(\d+\.?\d*)/', $refRange, $matches)) {
                    $normalMin = (float) $matches[1];
                    $normalMax = (float) $matches[2];
                }

                // Get short name for parameter
                $paramShortName = $this->shortNames[$paramKey] ?? null;

                DB::table('test_parameters')->insert([
                    'test_id' => $test->id,
                    'name' => $paramName,
                    'code' => $code,
                    'short_name' => $paramShortName,
                    'unit' => $paramData['unit'] ?? '',
                    'normal_range_male' => $refRange ?: null,
                    'normal_range_female' => $refRange ?: null,
                    'normal_min_male' => $normalMin,
                    'normal_max_male' => $normalMax,
                    'normal_min_female' => $normalMin,
                    'normal_max_female' => $normalMax,
                    'group_name' => $groupName,
                    'is_group_header' => false,
                    'sort_order' => $order++,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Format test key to readable name
     */
    protected function formatTestName(string $key): string
    {
        $name = str_replace(['_', '-'], ' ', $key);
        $name = ucwords($name);

        // Fix common abbreviations
        $replacements = [
            'Aptt' => 'APTT',
            'Esr' => 'ESR',
            'G6pd' => 'G6PD',
            'Pt Inr' => 'PT INR',
            'Tlc' => 'TLC',
            'Dlc' => 'DLC',
            'Cbc' => 'CBC',
            'Rbc' => 'RBC',
            'Wbc' => 'WBC',
            'Mch' => 'MCH',
            'Mcv' => 'MCV',
            'Mchc' => 'MCHC',
            'Rdw' => 'RDW',
            'Hct' => 'HCT',
            'Sgot' => 'SGOT',
            'Sgpt' => 'SGPT',
            'Ast' => 'AST',
            'Alt' => 'ALT',
            'Ggt' => 'GGT',
            'Ldh' => 'LDH',
            'Cpk' => 'CPK',
            'Bun' => 'BUN',
            'Tsh' => 'TSH',
            'T3' => 'T3',
            'T4' => 'T4',
            'Ft3' => 'FT3',
            'Ft4' => 'FT4',
            'Hba1c' => 'HbA1c',
            'Ige' => 'IgE',
            'Igg' => 'IgG',
            'Igm' => 'IgM',
            'Iga' => 'IgA',
            'Hiv' => 'HIV',
            'Hcv' => 'HCV',
            'Hbsag' => 'HBsAg',
            'Crp' => 'CRP',
            'Vdrl' => 'VDRL',
            'Tpha' => 'TPHA',
            'Rf' => 'RF',
            'Ana' => 'ANA',
            'Aso' => 'ASO',
            'Cmv' => 'CMV',
            'Ebv' => 'EBV',
            'Pcr' => 'PCR',
            'Dna' => 'DNA',
            'Rna' => 'RNA',
            'Afp' => 'AFP',
            'Psa' => 'PSA',
            'Ca 125' => 'CA-125',
            'Ca 19 9' => 'CA 19-9',
            'Cea' => 'CEA',
            'Tibc' => 'TIBC',
            'Dhea' => 'DHEA',
            'Fsh' => 'FSH',
            'Lh' => 'LH',
            'Amh' => 'AMH',
            'Tpo' => 'TPO',
            'Tgab' => 'TgAb',
            'Pp' => 'PP',
            '25 Hydroxy' => '25-Hydroxy',
            'Hdl' => 'HDL',
            'Ldl' => 'LDL',
            'Vldl' => 'VLDL',
            'Egfr' => 'eGFR',
            'Uibc' => 'UIBC',
            'Ns1' => 'NS1',
            'Afb' => 'AFB',
            'Csf' => 'CSF',
        ];

        foreach ($replacements as $search => $replace) {
            $name = str_ireplace($search, $replace, $name);
        }

        return $name;
    }

    /**
     * Format parameter key to readable name
     */
    protected function formatParamName(string $key): string
    {
        return $this->formatTestName($key);
    }

    /**
     * Guess sample type from test name
     */
    protected function guessSampleType(string $testName): string
    {
        $name = strtolower($testName);

        if (str_contains($name, 'urine') || str_contains($name, 'urinalysis')) {
            return 'urine';
        }
        if (str_contains($name, 'stool') || str_contains($name, 'fecal') || str_contains($name, 'occult')) {
            return 'stool';
        }
        if (str_contains($name, 'sputum') || str_contains($name, 'throat') || str_contains($name, 'swab')) {
            return 'swab';
        }
        if (str_contains($name, 'csf')) {
            return 'other';
        }

        return 'blood';
    }
}
