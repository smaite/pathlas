<?php

namespace Database\Seeders;

use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestParameter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveTestsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $this->seedAllTests();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('All tests seeded successfully!');
    }

    private function seedAllTests()
    {
        $haem = TestCategory::where('code', 'HAEM')->first()?->id ?? 1;
        $biochem = TestCategory::where('code', 'BIOCHEM')->first()?->id ?? 2;
        $serol = TestCategory::where('code', 'SEROL')->first()?->id ?? 3;
        $clin = TestCategory::where('code', 'CLIN')->first()?->id ?? 4;
        $micro = TestCategory::where('code', 'MICRO')->first()?->id ?? 6;
        $endo = TestCategory::where('code', 'ENDO')->first()?->id ?? 7;

        // ===== CBC =====
        $t = $this->createTest('CBC', 'Complete Blood Count (CBC)', $haem, 350);
        $this->addParam($t, 'Hemoglobin', 'HB', 'g/dL', 13, 17, 12, 15, 'RBC');
        $this->addParam($t, 'RBC Count', 'RBC', 'million/µL', 4.5, 5.5, 4.0, 5.0, 'RBC');
        $this->addParam($t, 'Hematocrit', 'HCT', '%', 40, 50, 36, 44, 'RBC');
        $this->addParam($t, 'MCV', 'MCV', 'fL', 80, 100, null, null, 'Indices');
        $this->addParam($t, 'MCH', 'MCH', 'pg', 27, 32, null, null, 'Indices');
        $this->addParam($t, 'MCHC', 'MCHC', 'g/dL', 32, 36, null, null, 'Indices');
        $this->addParam($t, 'RDW', 'RDW', '%', 11.5, 14.5, null, null, 'Indices');
        $this->addParam($t, 'WBC Count', 'WBC', '/µL', 4000, 11000, null, null, 'WBC');
        $this->addParam($t, 'Neutrophils', 'NEUT', '%', 40, 70, null, null, 'DLC');
        $this->addParam($t, 'Lymphocytes', 'LYMPH', '%', 20, 40, null, null, 'DLC');
        $this->addParam($t, 'Monocytes', 'MONO', '%', 2, 8, null, null, 'DLC');
        $this->addParam($t, 'Eosinophils', 'EOS', '%', 1, 4, null, null, 'DLC');
        $this->addParam($t, 'Basophils', 'BASO', '%', 0, 1, null, null, 'DLC');
        $this->addParam($t, 'Platelet Count', 'PLT', 'lakh/µL', 1.5, 4.0, null, null, 'PLT');

        // ===== LFT =====
        $t = $this->createTest('LFT', 'Liver Function Test (LFT)', $biochem, 450);
        $this->addParam($t, 'Total Bilirubin', 'T.Bil', 'mg/dL', 0.2, 1.2, null, null, 'Bilirubin');
        $this->addParam($t, 'Direct Bilirubin', 'D.Bil', 'mg/dL', 0.0, 0.3, null, null, 'Bilirubin');
        $this->addParam($t, 'Indirect Bilirubin', 'I.Bil', 'mg/dL', 0.1, 0.9, null, null, 'Bilirubin', '{T.Bil} - {D.Bil}');
        $this->addParam($t, 'SGOT (AST)', 'SGOT', 'U/L', 0, 40, null, null, 'Enzymes');
        $this->addParam($t, 'SGPT (ALT)', 'SGPT', 'U/L', 0, 40, null, null, 'Enzymes');
        $this->addParam($t, 'Alkaline Phosphatase', 'ALP', 'U/L', 44, 147, null, null, 'Enzymes');
        $this->addParam($t, 'GGT', 'GGT', 'U/L', 0, 55, 0, 38, 'Enzymes');
        $this->addParam($t, 'Total Protein', 'T.Prot', 'g/dL', 6.0, 8.3, null, null, 'Proteins');
        $this->addParam($t, 'Albumin', 'Alb', 'g/dL', 3.5, 5.0, null, null, 'Proteins');
        $this->addParam($t, 'Globulin', 'Glob', 'g/dL', 2.0, 3.5, null, null, 'Proteins', '{T.Prot} - {Alb}');
        $this->addParam($t, 'A/G Ratio', 'AG', '', 1.0, 2.5, null, null, 'Proteins', '{Alb} / {Glob}');

        // ===== KFT =====
        $t = $this->createTest('KFT', 'Kidney Function Test (KFT)', $biochem, 400);
        $this->addParam($t, 'Blood Urea', 'Urea', 'mg/dL', 15, 40, null, null, 'Renal');
        $this->addParam($t, 'BUN', 'BUN', 'mg/dL', 7, 20, null, null, 'Renal', '{Urea} * 0.467');
        $this->addParam($t, 'Serum Creatinine', 'Creat', 'mg/dL', 0.7, 1.3, 0.6, 1.1, 'Renal');
        $this->addParam($t, 'Uric Acid', 'UA', 'mg/dL', 3.5, 7.2, 2.5, 6.0, 'Renal');
        $this->addParam($t, 'Sodium', 'Na', 'mEq/L', 136, 145, null, null, 'Electrolytes');
        $this->addParam($t, 'Potassium', 'K', 'mEq/L', 3.5, 5.0, null, null, 'Electrolytes');
        $this->addParam($t, 'Chloride', 'Cl', 'mEq/L', 98, 106, null, null, 'Electrolytes');
        $this->addParam($t, 'Calcium', 'Ca', 'mg/dL', 8.5, 10.5, null, null, 'Minerals');
        $this->addParam($t, 'Phosphorus', 'Phos', 'mg/dL', 2.5, 4.5, null, null, 'Minerals');

        // ===== LIPID =====
        $t = $this->createTest('LIPID', 'Lipid Profile', $biochem, 500);
        $this->addParam($t, 'Total Cholesterol', 'T.Cho', 'mg/dL', 0, 200, null, null, 'Lipids');
        $this->addParam($t, 'Triglycerides', 'TG', 'mg/dL', 0, 150, null, null, 'Lipids');
        $this->addParam($t, 'HDL Cholesterol', 'HDL', 'mg/dL', 40, 60, null, null, 'Lipids');
        $this->addParam($t, 'VLDL', 'VLDL', 'mg/dL', 5, 40, null, null, 'Calculated', '{TG} / 5');
        $this->addParam($t, 'LDL Cholesterol', 'LDL', 'mg/dL', 0, 100, null, null, 'Calculated', '{T.Cho} - {HDL} - {VLDL}');
        $this->addParam($t, 'Non-HDL', 'NonHDL', 'mg/dL', 0, 130, null, null, 'Calculated', '{T.Cho} - {HDL}');
        $this->addParam($t, 'TC/HDL Ratio', 'TCHDL', '', 0, 5, null, null, 'Ratios', '{T.Cho} / {HDL}');

        // ===== TFT =====
        $t = $this->createTest('TFT', 'Thyroid Function Test (TFT)', $endo, 600);
        $this->addParam($t, 'T3', 'T3', 'ng/dL', 80, 200, null, null, 'Thyroid');
        $this->addParam($t, 'T4', 'T4', 'µg/dL', 4.5, 12.5, null, null, 'Thyroid');
        $this->addParam($t, 'TSH', 'TSH', 'µIU/mL', 0.4, 4.0, null, null, 'Thyroid');

        // ===== INDIVIDUAL TESTS =====
        $this->createTest('ESR', 'ESR (Westergren)', $haem, 100, 'mm/hr', 0, 20);
        $this->createTest('HB', 'Hemoglobin', $haem, 80, 'g/dL', 12, 17);
        $this->createTest('TLC', 'Total Leucocyte Count', $haem, 100, '/µL', 4000, 11000);
        $this->createTest('PLTC', 'Platelet Count', $haem, 150, 'lakh/µL', 1.5, 4.0);
        $this->createTest('AEC', 'Absolute Eosinophil Count', $haem, 120, '/µL', 40, 440);
        $this->createTest('RETIC', 'Reticulocyte Count', $haem, 180, '%', 0.5, 2.0);
        $this->createTest('MP', 'Malaria Parasite', $haem, 200);
        $this->createTest('G6PD', 'G6PD', $haem, 400, 'U/g Hb', 4.6, 13.5);

        // Blood Sugar
        $this->createTest('FBS', 'Fasting Blood Sugar', $biochem, 80, 'mg/dL', 70, 100);
        $this->createTest('PPBS', 'Blood Sugar PP', $biochem, 80, 'mg/dL', 70, 140);
        $this->createTest('RBS', 'Random Blood Sugar', $biochem, 80, 'mg/dL', 70, 140);
        $this->createTest('HBA1C', 'HbA1c', $biochem, 450, '%', 4.0, 5.6);

        // Enzymes
        $this->createTest('AMYL', 'Amylase', $biochem, 300, 'U/L', 28, 100);
        $this->createTest('LIPASE', 'Lipase', $biochem, 350, 'U/L', 0, 60);
        $this->createTest('CPKMB', 'CPK-MB', $biochem, 450, 'U/L', 0, 25);
        $this->createTest('TROP', 'Troponin I', $biochem, 600, 'ng/mL', 0, 0.04);
        $this->createTest('DDIMER', 'D-Dimer', $biochem, 700, 'ng/mL', 0, 500);

        // Vitamins
        $this->createTest('VITD', 'Vitamin D3', $biochem, 800, 'ng/mL', 30, 100);
        $this->createTest('VITB12', 'Vitamin B12', $biochem, 700, 'pg/mL', 200, 900);
        $this->createTest('FERRI', 'Ferritin', $biochem, 450, 'ng/mL', 20, 250);

        // Serology
        $this->createTest('HBSAG', 'HBsAg', $serol, 250);
        $this->createTest('HCV', 'Hepatitis C (HCV)', $serol, 400);
        $this->createTest('HIV', 'HIV', $serol, 300);
        $this->createTest('VDRL', 'VDRL', $serol, 150);
        $this->createTest('WIDAL', 'Widal Test', $serol, 200);
        $this->createTest('ASO', 'ASO Titer', $serol, 300, 'IU/mL', 0, 200);
        $this->createTest('CRP', 'C-Reactive Protein', $serol, 350, 'mg/L', 0, 6);
        $this->createTest('RA', 'Rheumatoid Factor', $serol, 350, 'IU/mL', 0, 14);
        $this->createTest('ANA', 'ANA', $serol, 800);
        $this->createTest('DENGUE', 'Dengue NS1', $serol, 400);
        $this->createTest('PSA', 'Total PSA', $serol, 500, 'ng/mL', 0, 4);
        $this->createTest('BHCG', 'Beta HCG', $serol, 500, 'mIU/mL');
        $this->createTest('TESTO', 'Testosterone', $serol, 600, 'ng/dL', 270, 1070);

        // Urine
        $t = $this->createTest('URE', 'Urine Routine', $clin, 100, null, null, null, 'urine');
        $this->addParam($t, 'Color', 'UCOL', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Appearance', 'UAPP', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Specific Gravity', 'USG', '', 1.005, 1.025, null, null, 'Physical');
        $this->addParam($t, 'pH', 'UPH', '', 5.0, 8.0, null, null, 'Chemical');
        $this->addParam($t, 'Protein', 'UPRO', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Glucose', 'UGLU', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Pus Cells', 'UPUS', '/HPF', 0, 5, null, null, 'Microscopy');
        $this->addParam($t, 'RBC', 'URBC', '/HPF', 0, 5, null, null, 'Microscopy');
        $this->addParam($t, 'Epithelial Cells', 'UEPI', '/HPF', null, null, null, null, 'Microscopy');

        $this->createTest('UPT', 'Urine Pregnancy Test', $clin, 150, null, null, null, 'urine');
        
        // Stool
        $t = $this->createTest('STOOL', 'Stool Routine', $clin, 120, null, null, null, 'stool');
        $this->addParam($t, 'Color', 'STCOL', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Consistency', 'STCON', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Mucus', 'STMUC', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Pus Cells', 'STPUS', '/HPF', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Ova/Cyst', 'STOVA', '', null, null, null, null, 'Microscopy');

        $this->createTest('OBT', 'Occult Blood Test', $clin, 150, null, null, null, 'stool');

        // Cultures
        $this->createTest('BLDCS', 'Blood Culture', $micro, 800);
        $this->createTest('URCS', 'Urine Culture', $micro, 500, null, null, null, 'urine');
        $this->createTest('STCS', 'Stool Culture', $micro, 500, null, null, null, 'stool');
        $this->createTest('PUSCS', 'Pus Culture', $micro, 500, null, null, null, 'swab');
        $this->createTest('AFB', 'AFB Stain', $micro, 150, null, null, null, 'other');
        $this->createTest('GRAM', 'Gram Stain', $micro, 100);

        $this->command->info('All tests with parameters created!');
    }

    private function createTest($code, $name, $catId, $price, $unit = null, $min = null, $max = null, $sample = 'blood')
    {
        return Test::updateOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'category_id' => $catId,
                'price' => $price,
                'unit' => $unit,
                'normal_min' => $min,
                'normal_max' => $max,
                'sample_type' => $sample,
                'is_active' => true,
            ]
        );
    }

    private function addParam($test, $name, $code, $unit, $minM, $maxM, $minF = null, $maxF = null, $group = null, $formula = null)
    {
        static $order = [];
        $tid = $test->id;
        if (!isset($order[$tid])) $order[$tid] = 0;
        $order[$tid]++;

        TestParameter::updateOrCreate(
            ['test_id' => $tid, 'code' => $code],
            [
                'name' => $name,
                'unit' => $unit,
                'normal_min_male' => $minM,
                'normal_max_male' => $maxM,
                'normal_min_female' => $minF ?? $minM,
                'normal_max_female' => $maxF ?? $maxM,
                'group_name' => $group,
                'formula' => $formula,
                'is_calculated' => !empty($formula),
                'sort_order' => $order[$tid],
                'is_active' => true,
            ]
        );
    }
}
