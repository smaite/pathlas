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

        // ===== TFT (Expanded) =====
        $t = $this->createTest('TFT', 'Thyroid Function Test (TFT)', $endo, 600);
        $this->addParam($t, 'T3', 'T3', 'ng/dL', 80, 200, null, null, 'Thyroid');
        $this->addParam($t, 'T4', 'T4', 'µg/dL', 4.5, 12.5, null, null, 'Thyroid');
        $this->addParam($t, 'TSH', 'TSH', 'µIU/mL', 0.4, 4.0, null, null, 'Thyroid');
        $this->addParam($t, 'Free T3', 'FT3', 'pg/mL', 2.0, 4.4, null, null, 'Free Hormones');
        $this->addParam($t, 'Free T4', 'FT4', 'ng/dL', 0.93, 1.7, null, null, 'Free Hormones');

        // ===== INDIVIDUAL TESTS =====
        $this->createTest('ESR', 'ESR (Westergren)', $haem, 100, 'mm/hr', 0, 20);
        $this->createTest('HB', 'Hemoglobin', $haem, 80, 'g/dL', 12, 17);
        $this->createTest('TLC', 'Total Leucocyte Count', $haem, 100, '/µL', 4000, 11000);
        $this->createTest('PLTC', 'Platelet Count', $haem, 150, 'lakh/µL', 1.5, 4.0);
        $this->createTest('AEC', 'Absolute Eosinophil Count', $haem, 120, '/µL', 40, 440);
        $this->createTest('RETIC', 'Reticulocyte Count', $haem, 180, '%', 0.5, 2.0);
        $this->createTest('MP', 'Malaria Parasite', $haem, 200);
        $this->createTest('G6PD', 'G6PD', $haem, 400, 'U/g Hb', 4.6, 13.5);
        $this->createTest('PBS', 'Peripheral Blood Smear', $haem, 250);
        $this->createTest('SICKLING', 'Sickling Test', $haem, 150);
        $this->createTest('HBE', 'HbE Electrophoresis', $haem, 500);

        // ===== Blood Sugar Panel =====
        $this->createTest('FBS', 'Fasting Blood Sugar', $biochem, 80, 'mg/dL', 70, 100);
        $this->createTest('PPBS', 'Blood Sugar PP', $biochem, 80, 'mg/dL', 70, 140);
        $this->createTest('RBS', 'Random Blood Sugar', $biochem, 80, 'mg/dL', 70, 140);
        $this->createTest('HBA1C', 'HbA1c', $biochem, 450, '%', 4.0, 5.6);
        
        // GTT - Glucose Tolerance Test
        $t = $this->createTest('GTT', 'Glucose Tolerance Test (GTT)', $biochem, 350);
        $this->addParam($t, 'Fasting', 'GTT0', 'mg/dL', 70, 100, null, null, 'GTT');
        $this->addParam($t, '1 Hour', 'GTT1', 'mg/dL', null, 180, null, null, 'GTT');
        $this->addParam($t, '2 Hour', 'GTT2', 'mg/dL', null, 140, null, null, 'GTT');
        $this->addParam($t, '3 Hour', 'GTT3', 'mg/dL', 70, 120, null, null, 'GTT');

        // Enzymes
        $this->createTest('AMYL', 'Amylase', $biochem, 300, 'U/L', 28, 100);
        $this->createTest('LIPASE', 'Lipase', $biochem, 350, 'U/L', 0, 60);
        $this->createTest('CPKMB', 'CPK-MB', $biochem, 450, 'U/L', 0, 25);
        $this->createTest('TROP', 'Troponin I', $biochem, 600, 'ng/mL', 0, 0.04);
        $this->createTest('DDIMER', 'D-Dimer', $biochem, 700, 'ng/mL', 0, 500);
        $this->createTest('PROTHROMB', 'Prothrombin Time', $haem, 300, 'sec', 11, 13.5);

        // Vitamins
        $this->createTest('VITD', 'Vitamin D3', $biochem, 800, 'ng/mL', 30, 100);
        $this->createTest('VITB12', 'Vitamin B12', $biochem, 700, 'pg/mL', 200, 900);
        $this->createTest('FERRI', 'Ferritin', $biochem, 450, 'ng/mL', 20, 250);

        // ===== WIDAL TEST (with titers) =====
        $t = $this->createTest('WIDALP', 'Widal Test (Panel)', $serol, 200);
        $this->addParam($t, 'Salmonella typhi O', 'STO', '', null, null, null, null, 'S. typhi');
        $this->addParam($t, 'Salmonella typhi H', 'STH', '', null, null, null, null, 'S. typhi');
        $this->addParam($t, 'Salmonella paratyphi AO', 'SAO', '', null, null, null, null, 'S. paratyphi A');
        $this->addParam($t, 'Salmonella paratyphi AH', 'SAH', '', null, null, null, null, 'S. paratyphi A');
        $this->addParam($t, 'Salmonella paratyphi BO', 'SBO', '', null, null, null, null, 'S. paratyphi B');
        $this->addParam($t, 'Salmonella paratyphi BH', 'SBH', '', null, null, null, null, 'S. paratyphi B');

        // ===== DENGUE PANEL =====
        $t = $this->createTest('DENGUEP', 'Dengue Panel', $serol, 800);
        $this->addParam($t, 'NS1 Antigen', 'DNS1', '', null, null, null, null, 'Antigen');
        $this->addParam($t, 'IgM Antibody', 'DIGM', '', null, null, null, null, 'Antibody');
        $this->addParam($t, 'IgG Antibody', 'DIGG', '', null, null, null, null, 'Antibody');

        // Serology (Individual)
        $this->createTest('HBSAG', 'HBsAg', $serol, 250);
        $this->createTest('HCV', 'Hepatitis C (HCV)', $serol, 400);
        $this->createTest('HIV', 'HIV', $serol, 300);
        $this->createTest('VDRL', 'VDRL', $serol, 150);
        $this->createTest('WIDAL', 'Widal Test', $serol, 200);
        $this->createTest('ASO', 'ASO Titer', $serol, 300, 'IU/mL', 0, 200);
        $this->createTest('CRP', 'C-Reactive Protein', $serol, 350, 'mg/L', 0, 6);
        $this->createTest('RA', 'Rheumatoid Factor', $serol, 350, 'IU/mL', 0, 14);
        $this->createTest('ANA', 'ANA', $serol, 800);
        $this->createTest('ANTIDS', 'Anti-dsDNA', $serol, 1000, 'IU/mL', 0, 25);
        $this->createTest('DENGUE', 'Dengue NS1', $serol, 400);
        $this->createTest('PSA', 'Total PSA', $serol, 500, 'ng/mL', 0, 4);
        $this->createTest('FPSA', 'Free PSA', $serol, 600, 'ng/mL');
        $this->createTest('BHCG', 'Beta HCG', $serol, 500, 'mIU/mL');
        $this->createTest('TESTO', 'Testosterone', $serol, 600, 'ng/dL', 270, 1070);

        // ===== URINE ROUTINE (Complete) =====
        $t = $this->createTest('URE', 'Urine Routine', $clin, 100, null, null, null, 'urine');
        $this->addParam($t, 'Color', 'UCOL', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Appearance', 'UAPP', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Specific Gravity', 'USG', '', 1.005, 1.030, null, null, 'Physical');
        $this->addParam($t, 'pH', 'UPH', '', 4.5, 8.0, null, null, 'Chemical');
        $this->addParam($t, 'Protein', 'UPRO', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Glucose', 'UGLU', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Ketones', 'UKET', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Blood', 'UBLD', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Bilirubin', 'UBIL', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Urobilinogen', 'UURO', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Nitrite', 'UNIT', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Leucocyte Esterase', 'ULEU', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Pus Cells', 'UPUS', '/HPF', 0, 5, null, null, 'Microscopy');
        $this->addParam($t, 'RBC', 'URBC', '/HPF', 0, 2, null, null, 'Microscopy');
        $this->addParam($t, 'Epithelial Cells', 'UEPI', '/HPF', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Casts', 'UCAST', '/LPF', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Crystals', 'UCRYS', '', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Bacteria', 'UBACT', '', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Yeast Cells', 'UYEAST', '', null, null, null, null, 'Microscopy');

        // Urine Microalbumin
        $this->createTest('UMALB', 'Urine Microalbumin', $clin, 400, 'mg/L', 0, 20, 'urine');
        $this->createTest('UPCR', 'Urine Protein Creatinine Ratio', $clin, 500, 'mg/g', 0, 200, 'urine');
        $this->createTest('U24PRO', '24hr Urine Protein', $clin, 350, 'mg/24hr', 0, 150, 'urine');
        $this->createTest('U24CRE', '24hr Urine Creatinine', $clin, 400, 'g/24hr', 0.8, 2.0, 'urine');

        $this->createTest('UPT', 'Urine Pregnancy Test', $clin, 150, null, null, null, 'urine');
        
        // ===== STOOL ROUTINE (Complete) =====
        $t = $this->createTest('STOOL', 'Stool Routine', $clin, 120, null, null, null, 'stool');
        $this->addParam($t, 'Color', 'STCOL', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Consistency', 'STCON', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Mucus', 'STMUC', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Occult Blood', 'STOBT', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Reducing Substances', 'STRED', '', null, null, null, null, 'Chemical');
        $this->addParam($t, 'Fat Globules', 'STFAT', '', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Pus Cells', 'STPUS', '/HPF', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'RBC', 'STRBC', '/HPF', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Ova/Cyst/Parasite', 'STOVA', '', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Yeast Cells', 'STYEAST', '', null, null, null, null, 'Microscopy');

        $this->createTest('OBT', 'Occult Blood Test', $clin, 150, null, null, null, 'stool');

        // ===== CSF ANALYSIS =====
        $t = $this->createTest('CSF', 'CSF Analysis', $clin, 600, null, null, null, 'other');
        $this->addParam($t, 'Appearance', 'CSFAPP', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Color', 'CSFCOL', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Total Cell Count', 'CSFTCC', '/µL', 0, 5, null, null, 'Cell Count');
        $this->addParam($t, 'Polymorphs', 'CSFPOLY', '%', null, null, null, null, 'Differential');
        $this->addParam($t, 'Lymphocytes', 'CSFLYM', '%', null, null, null, null, 'Differential');
        $this->addParam($t, 'RBC', 'CSFRBC', '/µL', null, null, null, null, 'Cell Count');
        $this->addParam($t, 'Glucose', 'CSFGLU', 'mg/dL', 40, 70, null, null, 'Biochemistry');
        $this->addParam($t, 'Protein', 'CSFPRO', 'mg/dL', 15, 45, null, null, 'Biochemistry');
        $this->addParam($t, 'Chloride', 'CSFCL', 'mEq/L', 118, 132, null, null, 'Biochemistry');
        $this->addParam($t, 'ADA', 'CSFADA', 'U/L', 0, 9, null, null, 'Biochemistry');
        $this->addParam($t, 'Gram Stain', 'CSFGRAM', '', null, null, null, null, 'Microbiology');

        // ===== PLEURAL/PERITONEAL FLUID =====
        $t = $this->createTest('PLEURAL', 'Pleural Fluid Analysis', $clin, 500, null, null, null, 'other');
        $this->addParam($t, 'Appearance', 'PLAPP', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Color', 'PLCOL', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Total Cell Count', 'PLTCC', '/µL', null, null, null, null, 'Cell Count');
        $this->addParam($t, 'Polymorphs', 'PLPOLY', '%', null, null, null, null, 'Differential');
        $this->addParam($t, 'Lymphocytes', 'PLLYM', '%', null, null, null, null, 'Differential');
        $this->addParam($t, 'Total Protein', 'PLPRO', 'g/dL', null, null, null, null, 'Biochemistry');
        $this->addParam($t, 'Glucose', 'PLGLU', 'mg/dL', null, null, null, null, 'Biochemistry');
        $this->addParam($t, 'LDH', 'PLLDH', 'U/L', null, null, null, null, 'Biochemistry');
        $this->addParam($t, 'ADA', 'PLADA', 'U/L', 0, 40, null, null, 'Biochemistry');

        // ===== SYNOVIAL FLUID =====
        $t = $this->createTest('SYNOV', 'Synovial Fluid Analysis', $clin, 500, null, null, null, 'other');
        $this->addParam($t, 'Appearance', 'SYNAPP', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Viscosity', 'SYNVIS', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Mucin Clot', 'SYNMUC', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'Total Cell Count', 'SYNTCC', '/µL', null, null, null, null, 'Cell Count');
        $this->addParam($t, 'Polymorphs', 'SYNPOLY', '%', null, null, null, null, 'Differential');
        $this->addParam($t, 'Uric Acid Crystals', 'SYNUA', '', null, null, null, null, 'Microscopy');
        $this->addParam($t, 'Calcium Pyrophosphate', 'SYNCPP', '', null, null, null, null, 'Microscopy');

        // ===== CULTURES =====
        $this->createTest('BLDCS', 'Blood Culture', $micro, 800);
        $this->createTest('URCS', 'Urine Culture', $micro, 500, null, null, null, 'urine');
        $this->createTest('STCS', 'Stool Culture', $micro, 500, null, null, null, 'stool');
        $this->createTest('PUSCS', 'Pus Culture', $micro, 500, null, null, null, 'swab');
        $this->createTest('SPCS', 'Sputum Culture', $micro, 500, null, null, null, 'other');
        $this->createTest('THCS', 'Throat Swab Culture', $micro, 400, null, null, null, 'swab');
        $this->createTest('ESWAB', 'Ear Swab Culture', $micro, 400, null, null, null, 'swab');
        $this->createTest('WSWAB', 'Wound Swab Culture', $micro, 450, null, null, null, 'swab');
        $this->createTest('HVS', 'High Vaginal Swab', $micro, 450, null, null, null, 'swab');
        $this->createTest('AFB', 'AFB Stain', $micro, 150, null, null, null, 'other');
        $this->createTest('AFBCS', 'AFB Culture', $micro, 1000, null, null, null, 'other');
        $this->createTest('GRAM', 'Gram Stain', $micro, 100);
        $this->createTest('KOH', 'KOH Mount', $micro, 100);
        $this->createTest('WETMOUNT', 'Wet Mount', $micro, 100);
        $this->createTest('GENEXPERT', 'GeneXpert MTB/RIF', $micro, 2500, null, null, null, 'other');

        // ===== HEPATITIS PANEL =====
        $t = $this->createTest('HEPPAN', 'Hepatitis Panel', $serol, 1500);
        $this->addParam($t, 'HBsAg', 'HBS', '', null, null, null, null, 'Hepatitis B');
        $this->addParam($t, 'Anti-HBs', 'AHBS', 'mIU/mL', null, null, null, null, 'Hepatitis B');
        $this->addParam($t, 'Anti-HBc Total', 'AHBC', '', null, null, null, null, 'Hepatitis B');
        $this->addParam($t, 'Anti-HBc IgM', 'AHBCM', '', null, null, null, null, 'Hepatitis B');
        $this->addParam($t, 'HBeAg', 'HBE', '', null, null, null, null, 'Hepatitis B');
        $this->addParam($t, 'Anti-HBe', 'AHBE', '', null, null, null, null, 'Hepatitis B');
        $this->addParam($t, 'Anti-HCV', 'AHCV', '', null, null, null, null, 'Hepatitis C');
        $this->addParam($t, 'Anti-HAV IgM', 'AHAV', '', null, null, null, null, 'Hepatitis A');

        // ===== ELECTROLYTE PANEL =====
        $t = $this->createTest('ELEC', 'Electrolyte Panel', $biochem, 400);
        $this->addParam($t, 'Sodium', 'NA', 'mEq/L', 136, 145, null, null, 'Electrolytes');
        $this->addParam($t, 'Potassium', 'K', 'mEq/L', 3.5, 5.0, null, null, 'Electrolytes');
        $this->addParam($t, 'Chloride', 'CL', 'mEq/L', 98, 106, null, null, 'Electrolytes');
        $this->addParam($t, 'Bicarbonate', 'HCO3', 'mEq/L', 22, 28, null, null, 'Electrolytes');
        $this->addParam($t, 'Calcium', 'CA', 'mg/dL', 8.5, 10.5, null, null, 'Minerals');
        $this->addParam($t, 'Magnesium', 'MG', 'mg/dL', 1.7, 2.4, null, null, 'Minerals');
        $this->addParam($t, 'Phosphorus', 'PHOS', 'mg/dL', 2.5, 4.5, null, null, 'Minerals');

        // ===== BONE PROFILE =====
        $t = $this->createTest('BONE', 'Bone Profile', $biochem, 1200);
        $this->addParam($t, 'Calcium', 'CA', 'mg/dL', 8.5, 10.5, null, null, 'Minerals');
        $this->addParam($t, 'Phosphorus', 'PHOS', 'mg/dL', 2.5, 4.5, null, null, 'Minerals');
        $this->addParam($t, 'Alkaline Phosphatase', 'ALP', 'U/L', 44, 147, null, null, 'Enzymes');
        $this->addParam($t, 'Vitamin D', 'VITD', 'ng/mL', 30, 100, null, null, 'Vitamins');
        $this->addParam($t, 'PTH', 'PTH', 'pg/mL', 15, 65, null, null, 'Hormones');

        // ===== AUTOIMMUNE PANEL =====
        $t = $this->createTest('AUTO', 'Autoimmune Panel', $serol, 3500);
        $this->addParam($t, 'ANA', 'ANA', '', null, null, null, null, 'Antibodies');
        $this->addParam($t, 'Anti-dsDNA', 'ADNA', 'IU/mL', 0, 25, null, null, 'Antibodies');
        $this->addParam($t, 'Anti-Smith', 'ASMITH', '', null, null, null, null, 'Antibodies');
        $this->addParam($t, 'Anti-RNP', 'ARNP', '', null, null, null, null, 'Antibodies');
        $this->addParam($t, 'Anti-SSA (Ro)', 'ASSA', '', null, null, null, null, 'Antibodies');
        $this->addParam($t, 'Anti-SSB (La)', 'ASSB', '', null, null, null, null, 'Antibodies');
        $this->addParam($t, 'Anti-Scl-70', 'ASCL', '', null, null, null, null, 'Antibodies');
        $this->addParam($t, 'Anti-Jo-1', 'AJO1', '', null, null, null, null, 'Antibodies');

        // Arthritis Panel
        $t = $this->createTest('ARTH', 'Arthritis Panel', $serol, 2000);
        $this->addParam($t, 'Rheumatoid Factor', 'RF', 'IU/mL', 0, 14, null, null, 'Marker');
        $this->addParam($t, 'Anti-CCP', 'ACCP', 'U/mL', 0, 20, null, null, 'Marker');
        $this->addParam($t, 'CRP', 'CRP', 'mg/L', 0, 6, null, null, 'Inflammation');
        $this->addParam($t, 'ESR', 'ESR', 'mm/hr', 0, 20, null, null, 'Inflammation');
        $this->addParam($t, 'Uric Acid', 'UA', 'mg/dL', 3.5, 7.2, null, null, 'Gout');

        // ===== IRON STUDIES =====
        $t = $this->createTest('IRON', 'Iron Studies', $biochem, 650);
        $this->addParam($t, 'Serum Iron', 'FE', 'µg/dL', 60, 170, 50, 150, 'Iron');
        $this->addParam($t, 'TIBC', 'TIBC', 'µg/dL', 250, 400, null, null, 'Iron');
        $this->addParam($t, 'Transferrin Saturation', 'TSAT', '%', 20, 50, null, null, 'Iron', '({FE} / {TIBC}) * 100');
        $this->addParam($t, 'Serum Ferritin', 'FERR', 'ng/mL', 30, 300, 15, 150, 'Iron');

        // ===== COAGULATION PROFILE =====
        $t = $this->createTest('COAG', 'Coagulation Profile', $haem, 550);
        $this->addParam($t, 'PT (Prothrombin Time)', 'PT', 'sec', 11, 13.5, null, null, 'Coagulation');
        $this->addParam($t, 'PT Control', 'PTC', 'sec', 11, 13, null, null, 'Coagulation');
        $this->addParam($t, 'INR', 'INR', '', 0.8, 1.2, null, null, 'Coagulation');
        $this->addParam($t, 'APTT', 'APTT', 'sec', 25, 35, null, null, 'Coagulation');
        $this->addParam($t, 'APTT Control', 'APTTC', 'sec', 25, 35, null, null, 'Coagulation');
        $this->addParam($t, 'Bleeding Time', 'BT', 'min', 1, 5, null, null, 'Bleeding');
        $this->addParam($t, 'Clotting Time', 'CT', 'min', 4, 10, null, null, 'Bleeding');

        // Blood Group
        $this->createTest('BGRP', 'Blood Group & Rh', $haem, 100);
        $this->createTest('COOMBS', 'Coombs Test (Direct)', $haem, 250);
        $this->createTest('ICOOMBS', 'Coombs Test (Indirect)', $haem, 300);

        // ===== HORMONES =====
        $this->createTest('FSH', 'FSH', $endo, 500, 'mIU/mL', 1.5, 12.4);
        $this->createTest('LH', 'LH', $endo, 500, 'mIU/mL', 1.7, 8.6);
        $this->createTest('PROL', 'Prolactin', $endo, 550, 'ng/mL', 4.0, 15.2);
        $this->createTest('CORT', 'Cortisol (AM)', $endo, 600, 'µg/dL', 6.2, 19.4);
        $this->createTest('INS', 'Fasting Insulin', $endo, 700, 'µIU/mL', 2.6, 24.9);
        $this->createTest('ESTRO', 'Estradiol (E2)', $endo, 650, 'pg/mL');
        $this->createTest('PROG', 'Progesterone', $endo, 550, 'ng/mL');
        $this->createTest('PTH', 'Parathyroid Hormone', $endo, 900, 'pg/mL', 15, 65);
        $this->createTest('DHEAS', 'DHEA-S', $endo, 750, 'µg/dL');

        // ===== CARDIAC MARKERS =====
        $t = $this->createTest('CARDIAC', 'Cardiac Markers Panel', $biochem, 1200);
        $this->addParam($t, 'Troponin I', 'TROP', 'ng/mL', 0, 0.04, null, null, 'Cardiac');
        $this->addParam($t, 'CK-MB', 'CKMB', 'U/L', 0, 25, null, null, 'Cardiac');
        $this->addParam($t, 'Total CK', 'CK', 'U/L', 30, 200, 30, 150, 'Cardiac');
        $this->addParam($t, 'LDH', 'LDH', 'U/L', 140, 280, null, null, 'Cardiac');
        $this->addParam($t, 'Myoglobin', 'MYOG', 'ng/mL', 0, 90, 0, 60, 'Cardiac');

        // Individual cardiac tests
        $this->createTest('LDH', 'LDH', $biochem, 300, 'U/L', 140, 280);
        $this->createTest('PROCALC', 'Procalcitonin', $biochem, 1500, 'ng/mL', 0, 0.5);
        $this->createTest('LACTAT', 'Lactate', $biochem, 400, 'mmol/L', 0.5, 2.2);
        $this->createTest('AMMONIA', 'Ammonia', $biochem, 500, 'µmol/L', 15, 45);

        // ===== SEMEN ANALYSIS =====
        $t = $this->createTest('SEMEN', 'Semen Analysis', $clin, 400, null, null, null, 'other');
        $this->addParam($t, 'Volume', 'SVOL', 'mL', 1.5, 5.0, null, null, 'Physical');
        $this->addParam($t, 'Liquefaction Time', 'SLIQ', 'min', null, 30, null, null, 'Physical');
        $this->addParam($t, 'Color', 'SCOL', '', null, null, null, null, 'Physical');
        $this->addParam($t, 'pH', 'SPH', '', 7.2, 8.0, null, null, 'Physical');
        $this->addParam($t, 'Sperm Count', 'SCNT', 'million/mL', 15, null, null, null, 'Count');
        $this->addParam($t, 'Total Sperm Count', 'STOT', 'million', 39, null, null, null, 'Count');
        $this->addParam($t, 'Motility (Progressive)', 'SMOT', '%', 32, null, null, null, 'Motility');
        $this->addParam($t, 'Total Motility', 'STMOT', '%', 40, null, null, null, 'Motility');
        $this->addParam($t, 'Morphology (Normal)', 'SMOR', '%', 4, null, null, null, 'Morphology');
        $this->addParam($t, 'Pus Cells', 'SPUS', '/HPF', 0, 5, null, null, 'Microscopy');

        // ===== ADDITIONAL TESTS =====
        $this->createTest('MAGN', 'Magnesium', $biochem, 350, 'mg/dL', 1.7, 2.4);
        $this->createTest('PHOS', 'Phosphorus', $biochem, 200, 'mg/dL', 2.5, 4.5);
        $this->createTest('ZINC', 'Zinc', $biochem, 600, 'µg/dL', 60, 120);
        $this->createTest('COPPER', 'Copper', $biochem, 700, 'µg/dL', 70, 140);
        $this->createTest('FOLATE', 'Folate', $biochem, 650, 'ng/mL', 3.0, 17.0);
        $this->createTest('HOMOC', 'Homocysteine', $biochem, 800, 'µmol/L', 5, 15);

        // Tumor Markers
        $this->createTest('CEA', 'CEA', $serol, 800, 'ng/mL', 0, 3);
        $this->createTest('AFP', 'Alpha Fetoprotein', $serol, 750, 'ng/mL', 0, 10);
        $this->createTest('CA125', 'CA-125', $serol, 1200, 'U/mL', 0, 35);
        $this->createTest('CA199', 'CA 19-9', $serol, 1200, 'U/mL', 0, 37);
        $this->createTest('CA153', 'CA 15-3', $serol, 1200, 'U/mL', 0, 30);

        // Allergy/Immunity
        $this->createTest('IGE', 'Total IgE', $serol, 600, 'IU/mL', 0, 100);
        $this->createTest('IGA', 'IgA', $serol, 500, 'mg/dL', 70, 400);
        $this->createTest('IGG', 'IgG', $serol, 500, 'mg/dL', 700, 1600);
        $this->createTest('IGM', 'IgM', $serol, 500, 'mg/dL', 40, 230);
        $this->createTest('C3', 'Complement C3', $serol, 600, 'mg/dL', 90, 180);
        $this->createTest('C4', 'Complement C4', $serol, 600, 'mg/dL', 10, 40);

        // ===== TORCH PANEL =====
        $t = $this->createTest('TORCH', 'TORCH Panel', $serol, 2500);
        $this->addParam($t, 'Toxoplasma IgG', 'TOXOG', 'IU/mL', null, null, null, null, 'Toxoplasma');
        $this->addParam($t, 'Toxoplasma IgM', 'TOXOM', '', null, null, null, null, 'Toxoplasma');
        $this->addParam($t, 'Rubella IgG', 'RUBG', 'IU/mL', null, null, null, null, 'Rubella');
        $this->addParam($t, 'Rubella IgM', 'RUBM', '', null, null, null, null, 'Rubella');
        $this->addParam($t, 'CMV IgG', 'CMVG', 'AU/mL', null, null, null, null, 'CMV');
        $this->addParam($t, 'CMV IgM', 'CMVM', '', null, null, null, null, 'CMV');
        $this->addParam($t, 'HSV-1 IgG', 'HSV1G', '', null, null, null, null, 'HSV');
        $this->addParam($t, 'HSV-2 IgG', 'HSV2G', '', null, null, null, null, 'HSV');

        // ===== ABG (Arterial Blood Gas) =====
        $t = $this->createTest('ABG', 'Arterial Blood Gas', $biochem, 800);
        $this->addParam($t, 'pH', 'ABGPH', '', 7.35, 7.45, null, null, 'Blood Gas');
        $this->addParam($t, 'pCO2', 'PCO2', 'mmHg', 35, 45, null, null, 'Blood Gas');
        $this->addParam($t, 'pO2', 'PO2', 'mmHg', 80, 100, null, null, 'Blood Gas');
        $this->addParam($t, 'HCO3', 'ABGHCO3', 'mEq/L', 22, 28, null, null, 'Calculated');
        $this->addParam($t, 'Base Excess', 'BE', 'mEq/L', -2, 2, null, null, 'Calculated');
        $this->addParam($t, 'O2 Saturation', 'SO2', '%', 95, 100, null, null, 'Oxygen');
        $this->addParam($t, 'Lactate', 'ABGLAC', 'mmol/L', 0.5, 2.2, null, null, 'Metabolite');
        $this->addParam($t, 'Sodium', 'ABGNA', 'mEq/L', 136, 145, null, null, 'Electrolytes');
        $this->addParam($t, 'Potassium', 'ABGK', 'mEq/L', 3.5, 5.0, null, null, 'Electrolytes');
        $this->addParam($t, 'Ionized Calcium', 'ICA', 'mmol/L', 1.12, 1.32, null, null, 'Electrolytes');

        // ===== ANEMIA PANEL =====
        $t = $this->createTest('ANEMIA', 'Anemia Panel', $haem, 1500);
        $this->addParam($t, 'Hemoglobin', 'HB', 'g/dL', 13, 17, 12, 15, 'CBC');
        $this->addParam($t, 'MCV', 'MCV', 'fL', 80, 100, null, null, 'Indices');
        $this->addParam($t, 'MCH', 'MCH', 'pg', 27, 32, null, null, 'Indices');
        $this->addParam($t, 'Reticulocyte Count', 'RETIC', '%', 0.5, 2.0, null, null, 'RBC');
        $this->addParam($t, 'Serum Iron', 'FE', 'µg/dL', 60, 170, 50, 150, 'Iron');
        $this->addParam($t, 'TIBC', 'TIBC', 'µg/dL', 250, 400, null, null, 'Iron');
        $this->addParam($t, 'Transferrin Saturation', 'TSAT', '%', 20, 50, null, null, 'Iron', '({FE} / {TIBC}) * 100');
        $this->addParam($t, 'Ferritin', 'FERR', 'ng/mL', 30, 300, 15, 150, 'Iron');
        $this->addParam($t, 'Vitamin B12', 'B12', 'pg/mL', 200, 900, null, null, 'Vitamins');
        $this->addParam($t, 'Folate', 'FOL', 'ng/mL', 3.0, 17.0, null, null, 'Vitamins');

        // ===== FEMALE FERTILITY PANEL =====
        $t = $this->createTest('FFERT', 'Female Fertility Panel', $endo, 3000);
        $this->addParam($t, 'FSH', 'FSH', 'mIU/mL', 3.5, 12.5, null, null, 'Gonadotropins');
        $this->addParam($t, 'LH', 'LH', 'mIU/mL', 2.4, 12.6, null, null, 'Gonadotropins');
        $this->addParam($t, 'Estradiol (E2)', 'E2', 'pg/mL', null, null, null, null, 'Steroids');
        $this->addParam($t, 'Progesterone', 'PROG', 'ng/mL', null, null, null, null, 'Steroids');
        $this->addParam($t, 'Prolactin', 'PRL', 'ng/mL', 4.0, 23.0, null, null, 'Pituitary');
        $this->addParam($t, 'AMH', 'AMH', 'ng/mL', 1.0, 3.5, null, null, 'Ovarian Reserve');
        $this->addParam($t, 'TSH', 'TSH', 'µIU/mL', 0.4, 4.0, null, null, 'Thyroid');

        // ===== MALE FERTILITY PANEL =====
        $t = $this->createTest('MFERT', 'Male Fertility Panel', $endo, 2500);
        $this->addParam($t, 'FSH', 'FSH', 'mIU/mL', 1.5, 12.4, null, null, 'Gonadotropins');
        $this->addParam($t, 'LH', 'LH', 'mIU/mL', 1.7, 8.6, null, null, 'Gonadotropins');
        $this->addParam($t, 'Testosterone', 'TESTO', 'ng/dL', 270, 1070, null, null, 'Steroids');
        $this->addParam($t, 'Free Testosterone', 'FTESTO', 'pg/mL', 9, 30, null, null, 'Steroids');
        $this->addParam($t, 'Prolactin', 'PRL', 'ng/mL', 4.0, 15.2, null, null, 'Pituitary');
        $this->addParam($t, 'Estradiol', 'E2', 'pg/mL', 10, 40, null, null, 'Steroids');

        // ===== ALLERGY PANEL (with common allergens) =====
        $t = $this->createTest('ALLERGY', 'Allergy Panel', $serol, 3000);
        $this->addParam($t, 'Total IgE', 'IGE', 'IU/mL', 0, 100, null, null, 'Total');
        $this->addParam($t, 'House Dust Mite', 'ADUST', 'kU/L', 0, 0.35, null, null, 'Inhalants');
        $this->addParam($t, 'Cat Dander', 'ACAT', 'kU/L', 0, 0.35, null, null, 'Inhalants');
        $this->addParam($t, 'Dog Dander', 'ADOG', 'kU/L', 0, 0.35, null, null, 'Inhalants');
        $this->addParam($t, 'Grass Pollen', 'AGRASS', 'kU/L', 0, 0.35, null, null, 'Inhalants');
        $this->addParam($t, 'Milk', 'AMILK', 'kU/L', 0, 0.35, null, null, 'Food');
        $this->addParam($t, 'Egg White', 'AEGG', 'kU/L', 0, 0.35, null, null, 'Food');
        $this->addParam($t, 'Wheat', 'AWHEAT', 'kU/L', 0, 0.35, null, null, 'Food');
        $this->addParam($t, 'Peanut', 'APNUT', 'kU/L', 0, 0.35, null, null, 'Food');
        $this->addParam($t, 'Soy', 'ASOY', 'kU/L', 0, 0.35, null, null, 'Food');

        // ===== PANCREATIC PANEL =====
        $t = $this->createTest('PANC', 'Pancreatic Panel', $biochem, 600);
        $this->addParam($t, 'Amylase', 'AMYL', 'U/L', 28, 100, null, null, 'Enzymes');
        $this->addParam($t, 'Lipase', 'LIPASE', 'U/L', 0, 60, null, null, 'Enzymes');
        $this->addParam($t, 'Glucose Fasting', 'FBS', 'mg/dL', 70, 100, null, null, 'Sugar');
        $this->addParam($t, 'HbA1c', 'HBA1C', '%', 4.0, 5.6, null, null, 'Sugar');

        // ===== LIVER DISEASE PANEL =====
        $t = $this->createTest('LIVDIS', 'Liver Disease Panel', $biochem, 2000);
        $this->addParam($t, 'Total Bilirubin', 'TBIL', 'mg/dL', 0.2, 1.2, null, null, 'Bilirubin');
        $this->addParam($t, 'Direct Bilirubin', 'DBIL', 'mg/dL', 0.0, 0.3, null, null, 'Bilirubin');
        $this->addParam($t, 'SGOT (AST)', 'AST', 'U/L', 0, 40, null, null, 'Enzymes');
        $this->addParam($t, 'SGPT (ALT)', 'ALT', 'U/L', 0, 40, null, null, 'Enzymes');
        $this->addParam($t, 'ALP', 'ALP', 'U/L', 44, 147, null, null, 'Enzymes');
        $this->addParam($t, 'GGT', 'GGT', 'U/L', 0, 55, null, null, 'Enzymes');
        $this->addParam($t, 'Albumin', 'ALB', 'g/dL', 3.5, 5.0, null, null, 'Proteins');
        $this->addParam($t, 'PT/INR', 'INR', '', 0.8, 1.2, null, null, 'Coagulation');
        $this->addParam($t, 'Ammonia', 'NH3', 'µmol/L', 15, 45, null, null, 'Metabolites');
        $this->addParam($t, 'AFP', 'AFP', 'ng/mL', 0, 10, null, null, 'Tumor Marker');

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
