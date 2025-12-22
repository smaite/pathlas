<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Lab;
use App\Models\TestCategory;
use App\Models\Test;
use App\Models\TestParameter;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['name' => 'superadmin', 'display_name' => 'Super Admin'],
            ['name' => 'admin', 'display_name' => 'Lab Administrator'],
            ['name' => 'receptionist', 'display_name' => 'Receptionist'],
            ['name' => 'technician', 'display_name' => 'Lab Technician'],
            ['name' => 'pathologist', 'display_name' => 'Pathologist'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create Sample Labs for Demo
        $labs = [
            [
                'name' => 'Smart Pathology Laboratory',
                'code' => 'SPL-MAIN',
                'tagline' => 'Accurate | Caring | Instant',
                'address' => '105-108, Smart Vision Complex, Healthcare Road',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'phone' => '0123456789',
                'phone2' => '9876543210',
                'email' => 'info@smartpathlab.com',
                'website' => 'www.smartpathlab.com',
                'header_color' => '#1e3a8a',
                'report_notes' => "1. All test results should be correlated clinically.\n2. If any doubts regarding results, please contact the lab.\n3. Sample will be preserved for 48 hours for re-testing.",
            ],
            [
                'name' => 'HealthCare Diagnostics',
                'code' => 'HCD-01',
                'tagline' => 'Your Health, Our Priority',
                'address' => '22 Medical Plaza, MG Road',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
                'phone' => '011-2345678',
                'phone2' => '9988776655',
                'email' => 'contact@healthcarediag.com',
                'website' => 'www.healthcarediag.com',
                'header_color' => '#059669',
                'report_notes' => "1. Report valid only with authorized signature.\n2. For home collection, call our helpline.",
            ],
            [
                'name' => 'City Lab Centre',
                'code' => 'CLC-BRANCH',
                'tagline' => 'Trust | Quality | Care',
                'address' => '45 Park Street, Central Area',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'pincode' => '700016',
                'phone' => '033-4567890',
                'email' => 'info@citylabcentre.com',
                'website' => 'www.citylabcentre.com',
                'header_color' => '#7c3aed',
            ],
        ];

        $firstLab = null;
        foreach ($labs as $labData) {
            $lab = Lab::create(array_merge($labData, [
                'is_verified' => true,
                'verified_at' => now(),
                'subscription_starts_at' => now(),
                'subscription_expires_at' => now()->addYear(),
            ]));
            if (!$firstLab) $firstLab = $lab;
        }

        // Create Super Admin (no lab - platform owner)
        $superadminRole = Role::where('name', 'superadmin')->first();
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@pathlas.com',
            'password' => 'password',
            'role_id' => $superadminRole->id,
            'lab_id' => null,
            'status' => 'active',
        ]);

        // Create Lab Admin for first demo lab
        $adminRole = Role::where('name', 'admin')->first();
        User::create([
            'name' => 'Lab Admin',
            'email' => 'admin@pathlas.com',
            'password' => 'password',
            'role_id' => $adminRole->id,
            'lab_id' => $firstLab->id,
            'status' => 'active',
        ]);

        // Create Sample Users
        User::create([
            'name' => 'Reception Staff',
            'email' => 'reception@pathlas.com',
            'password' => 'password',
            'role_id' => 2,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Lab Technician',
            'email' => 'tech@pathlas.com',
            'password' => 'password',
            'role_id' => 3,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Dr. Pathologist',
            'email' => 'doctor@pathlas.com',
            'password' => 'password',
            'role_id' => 4,
            'status' => 'active',
        ]);

        // Create Test Categories
        $categories = [
            ['name' => 'Haematology', 'code' => 'HAEM', 'description' => 'Blood cell counts and related tests'],
            ['name' => 'Biochemistry', 'code' => 'BIOCHEM', 'description' => 'Blood chemistry tests'],
            ['name' => 'Liver Function', 'code' => 'LFT', 'description' => 'Liver function panel'],
            ['name' => 'Kidney Function', 'code' => 'KFT', 'description' => 'Kidney function panel'],
            ['name' => 'Lipid Profile', 'code' => 'LIPID', 'description' => 'Cholesterol and triglycerides'],
            ['name' => 'Thyroid', 'code' => 'THYROID', 'description' => 'Thyroid function tests'],
            ['name' => 'Urine Analysis', 'code' => 'URINE', 'description' => 'Urine examination'],
            ['name' => 'Diabetes', 'code' => 'DIAB', 'description' => 'Blood sugar tests'],
        ];

        foreach ($categories as $cat) {
            TestCategory::create($cat);
        }

        // Create Sample Tests
        $tests = [
            // Haematology
            ['category_id' => 1, 'name' => 'Complete Blood Count (CBC)', 'code' => 'CBC', 'price' => 350, 'unit' => '', 'sample_type' => 'blood'],
            ['category_id' => 1, 'name' => 'Haemoglobin', 'code' => 'HB', 'price' => 100, 'unit' => 'g/dL', 'normal_min' => 12, 'normal_max' => 17, 'sample_type' => 'blood'],
            ['category_id' => 1, 'name' => 'Platelet Count', 'code' => 'PLT', 'price' => 150, 'unit' => 'lakh/µL', 'normal_min' => 1.5, 'normal_max' => 4, 'sample_type' => 'blood'],
            ['category_id' => 1, 'name' => 'ESR', 'code' => 'ESR', 'price' => 100, 'unit' => 'mm/hr', 'normal_min' => 0, 'normal_max' => 20, 'sample_type' => 'blood'],
            
            // Biochemistry
            ['category_id' => 2, 'name' => 'Blood Glucose Fasting', 'code' => 'FBS', 'price' => 80, 'unit' => 'mg/dL', 'normal_min' => 70, 'normal_max' => 100, 'sample_type' => 'blood'],
            ['category_id' => 2, 'name' => 'Blood Glucose PP', 'code' => 'PPBS', 'price' => 80, 'unit' => 'mg/dL', 'normal_min' => 70, 'normal_max' => 140, 'sample_type' => 'blood'],
            ['category_id' => 2, 'name' => 'HbA1c', 'code' => 'HBA1C', 'price' => 450, 'unit' => '%', 'normal_min' => 4, 'normal_max' => 5.6, 'sample_type' => 'blood'],
            
            // Liver Function
            ['category_id' => 3, 'name' => 'Liver Function Test', 'code' => 'LFT', 'price' => 600, 'unit' => '', 'sample_type' => 'blood'],
            ['category_id' => 3, 'name' => 'SGOT/AST', 'code' => 'SGOT', 'price' => 150, 'unit' => 'U/L', 'normal_min' => 5, 'normal_max' => 40, 'sample_type' => 'blood'],
            ['category_id' => 3, 'name' => 'SGPT/ALT', 'code' => 'SGPT', 'price' => 150, 'unit' => 'U/L', 'normal_min' => 7, 'normal_max' => 56, 'sample_type' => 'blood'],
            ['category_id' => 3, 'name' => 'Bilirubin Total', 'code' => 'TBIL', 'price' => 120, 'unit' => 'mg/dL', 'normal_min' => 0.1, 'normal_max' => 1.2, 'sample_type' => 'blood'],
            
            // Kidney Function
            ['category_id' => 4, 'name' => 'Kidney Function Test', 'code' => 'KFT', 'price' => 500, 'unit' => '', 'sample_type' => 'blood'],
            ['category_id' => 4, 'name' => 'Creatinine', 'code' => 'CREAT', 'price' => 150, 'unit' => 'mg/dL', 'normal_min' => 0.7, 'normal_max' => 1.3, 'sample_type' => 'blood'],
            ['category_id' => 4, 'name' => 'Blood Urea', 'code' => 'UREA', 'price' => 120, 'unit' => 'mg/dL', 'normal_min' => 15, 'normal_max' => 40, 'sample_type' => 'blood'],
            ['category_id' => 4, 'name' => 'Uric Acid', 'code' => 'UA', 'price' => 150, 'unit' => 'mg/dL', 'normal_min' => 3.5, 'normal_max' => 7.2, 'sample_type' => 'blood'],
            
            // Lipid Profile
            ['category_id' => 5, 'name' => 'Lipid Profile', 'code' => 'LIPID', 'price' => 500, 'unit' => '', 'sample_type' => 'blood'],
            ['category_id' => 5, 'name' => 'Total Cholesterol', 'code' => 'CHOL', 'price' => 150, 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 200, 'sample_type' => 'blood'],
            ['category_id' => 5, 'name' => 'Triglycerides', 'code' => 'TG', 'price' => 150, 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 150, 'sample_type' => 'blood'],
            ['category_id' => 5, 'name' => 'HDL Cholesterol', 'code' => 'HDL', 'price' => 150, 'unit' => 'mg/dL', 'normal_min' => 40, 'normal_max' => 60, 'sample_type' => 'blood'],
            ['category_id' => 5, 'name' => 'LDL Cholesterol', 'code' => 'LDL', 'price' => 150, 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 100, 'sample_type' => 'blood'],
            
            // Thyroid
            ['category_id' => 6, 'name' => 'Thyroid Profile', 'code' => 'THYR', 'price' => 600, 'unit' => '', 'sample_type' => 'blood'],
            ['category_id' => 6, 'name' => 'TSH', 'code' => 'TSH', 'price' => 350, 'unit' => 'µIU/mL', 'normal_min' => 0.4, 'normal_max' => 4, 'sample_type' => 'blood'],
            ['category_id' => 6, 'name' => 'T3', 'code' => 'T3', 'price' => 250, 'unit' => 'ng/dL', 'normal_min' => 80, 'normal_max' => 200, 'sample_type' => 'blood'],
            ['category_id' => 6, 'name' => 'T4', 'code' => 'T4', 'price' => 250, 'unit' => 'µg/dL', 'normal_min' => 5.1, 'normal_max' => 14.1, 'sample_type' => 'blood'],
            
            // Urine
            ['category_id' => 7, 'name' => 'Urine Routine', 'code' => 'URINE', 'price' => 100, 'unit' => '', 'sample_type' => 'urine'],
            ['category_id' => 7, 'name' => 'Urine Culture', 'code' => 'UCULT', 'price' => 400, 'unit' => '', 'sample_type' => 'urine'],
        ];

        foreach ($tests as $test) {
            Test::create($test);
        }

        // Add CBC Parameters (sub-tests)
        $cbcTest = Test::where('code', 'CBC')->first();
        if ($cbcTest) {
            $cbcParams = [
                // Main CBC Parameters
                ['name' => 'Haemoglobin (Hb)', 'code' => 'HB', 'unit' => 'g/dL', 'normal_min_male' => 13.0, 'normal_max_male' => 17.0, 'normal_min_female' => 12.0, 'normal_max_female' => 15.0, 'method' => 'Immunoturbidimetry', 'sort_order' => 1],
                ['name' => 'Total RBC count', 'code' => 'RBC', 'unit' => 'mill/cumm', 'normal_min' => 4.5, 'normal_max' => 5.5, 'method' => 'Electrical Impedance, VCS', 'sort_order' => 2],
                
                // Blood Indices Group
                ['name' => 'Packed Cell Volume (PCV)', 'code' => 'PCV', 'unit' => '%', 'normal_min' => 40, 'normal_max' => 50, 'group_name' => 'BLOOD INDICES', 'method' => 'Calculated', 'sort_order' => 3],
                ['name' => 'Mean Corpuscular Volume (MCV)', 'code' => 'MCV', 'unit' => 'fL', 'normal_min' => 83, 'normal_max' => 101, 'group_name' => 'BLOOD INDICES', 'method' => 'Calculated', 'sort_order' => 4],
                ['name' => 'MCH', 'code' => 'MCH', 'unit' => 'pg', 'normal_min' => 27, 'normal_max' => 32, 'group_name' => 'BLOOD INDICES', 'method' => 'Calculated', 'sort_order' => 5],
                ['name' => 'MCHC', 'code' => 'MCHC', 'unit' => 'g/dL', 'normal_min' => 32.5, 'normal_max' => 34.5, 'group_name' => 'BLOOD INDICES', 'method' => 'Calculated', 'sort_order' => 6],
                ['name' => 'RDW', 'code' => 'RDW', 'unit' => '%', 'normal_min' => 11.6, 'normal_max' => 14.0, 'group_name' => 'BLOOD INDICES', 'method' => 'Calculated', 'sort_order' => 7],
                
                // WBC
                ['name' => 'Total WBC count', 'code' => 'WBC', 'unit' => 'cumm', 'normal_min' => 4000, 'normal_max' => 11000, 'method' => 'Electrical Impedance, VCS', 'sort_order' => 8],
                
                // Differential WBC Count Group
                ['name' => 'Neutrophils', 'code' => 'NEUT', 'unit' => '%', 'normal_min' => 50, 'normal_max' => 62, 'group_name' => 'DIFFERENTIAL WBC COUNT', 'method' => 'Electrical Impedance, VCS', 'sort_order' => 9],
                ['name' => 'Lymphocytes', 'code' => 'LYMPH', 'unit' => '%', 'normal_min' => 20, 'normal_max' => 40, 'group_name' => 'DIFFERENTIAL WBC COUNT', 'method' => 'Electrical Impedance, VCS', 'sort_order' => 10],
                ['name' => 'Eosinophils', 'code' => 'EOS', 'unit' => '%', 'normal_min' => 0, 'normal_max' => 6, 'group_name' => 'DIFFERENTIAL WBC COUNT', 'method' => 'Electrical Impedance, VCS', 'sort_order' => 11],
                ['name' => 'Monocytes', 'code' => 'MONO', 'unit' => '%', 'normal_min' => 0, 'normal_max' => 10, 'group_name' => 'DIFFERENTIAL WBC COUNT', 'method' => 'Electrical Impedance, VCS', 'sort_order' => 12],
                ['name' => 'Basophils', 'code' => 'BASO', 'unit' => '%', 'normal_min' => 0, 'normal_max' => 2, 'group_name' => 'DIFFERENTIAL WBC COUNT', 'method' => 'Electrical Impedance, VCS', 'sort_order' => 13],
                
                // Platelets
                ['name' => 'Platelet Count', 'code' => 'PLT', 'unit' => 'cumm', 'normal_min' => 150000, 'normal_max' => 410000, 'method' => 'Electrical Impedance, VCS', 'sort_order' => 14],
            ];

            foreach ($cbcParams as $param) {
                TestParameter::create(array_merge($param, ['test_id' => $cbcTest->id]));
            }
        }

        // Add Lipid Profile Parameters
        $lipidTest = Test::where('code', 'LIPID')->first();
        if ($lipidTest) {
            $lipidParams = [
                ['name' => 'Total Cholesterol', 'code' => 'CHOL', 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 200, 'sort_order' => 1],
                ['name' => 'Triglycerides', 'code' => 'TG', 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 150, 'sort_order' => 2],
                ['name' => 'HDL Cholesterol', 'code' => 'HDL', 'unit' => 'mg/dL', 'normal_min' => 40, 'normal_max' => 60, 'sort_order' => 3],
                ['name' => 'LDL Cholesterol', 'code' => 'LDL', 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 100, 'sort_order' => 4],
                ['name' => 'VLDL', 'code' => 'VLDL', 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 40, 'sort_order' => 5],
                ['name' => 'Total/HDL Ratio', 'code' => 'RATIO', 'unit' => '', 'normal_min' => 0, 'normal_max' => 5, 'sort_order' => 6],
            ];

            foreach ($lipidParams as $param) {
                TestParameter::create(array_merge($param, ['test_id' => $lipidTest->id]));
            }
        }

        // Add Liver Function Test (LFT) Parameters
        $lftTest = Test::where('code', 'LFT')->first();
        if ($lftTest) {
            $lftParams = [
                ['name' => 'Bilirubin Total', 'code' => 'TBIL', 'unit' => 'mg/dL', 'normal_min' => 0.1, 'normal_max' => 1.2, 'sort_order' => 1],
                ['name' => 'Bilirubin Direct', 'code' => 'DBIL', 'unit' => 'mg/dL', 'normal_min' => 0, 'normal_max' => 0.3, 'sort_order' => 2],
                ['name' => 'Bilirubin Indirect', 'code' => 'IBIL', 'unit' => 'mg/dL', 'normal_min' => 0.1, 'normal_max' => 0.9, 'sort_order' => 3],
                ['name' => 'SGOT/AST', 'code' => 'SGOT', 'unit' => 'U/L', 'normal_min' => 5, 'normal_max' => 40, 'group_name' => 'LIVER ENZYMES', 'sort_order' => 4],
                ['name' => 'SGPT/ALT', 'code' => 'SGPT', 'unit' => 'U/L', 'normal_min' => 7, 'normal_max' => 56, 'group_name' => 'LIVER ENZYMES', 'sort_order' => 5],
                ['name' => 'Alkaline Phosphatase (ALP)', 'code' => 'ALP', 'unit' => 'U/L', 'normal_min' => 44, 'normal_max' => 147, 'group_name' => 'LIVER ENZYMES', 'sort_order' => 6],
                ['name' => 'Gamma GT (GGT)', 'code' => 'GGT', 'unit' => 'U/L', 'normal_min' => 0, 'normal_max' => 55, 'group_name' => 'LIVER ENZYMES', 'sort_order' => 7],
                ['name' => 'Total Protein', 'code' => 'TP', 'unit' => 'g/dL', 'normal_min' => 6.0, 'normal_max' => 8.3, 'group_name' => 'PROTEINS', 'sort_order' => 8],
                ['name' => 'Albumin', 'code' => 'ALB', 'unit' => 'g/dL', 'normal_min' => 3.5, 'normal_max' => 5.0, 'group_name' => 'PROTEINS', 'sort_order' => 9],
                ['name' => 'Globulin', 'code' => 'GLOB', 'unit' => 'g/dL', 'normal_min' => 2.0, 'normal_max' => 3.5, 'group_name' => 'PROTEINS', 'sort_order' => 10],
                ['name' => 'A/G Ratio', 'code' => 'AG', 'unit' => '', 'normal_min' => 1.0, 'normal_max' => 2.1, 'group_name' => 'PROTEINS', 'sort_order' => 11],
            ];

            foreach ($lftParams as $param) {
                TestParameter::create(array_merge($param, ['test_id' => $lftTest->id]));
            }
        }

        // Add Kidney Function Test (KFT) Parameters
        $kftTest = Test::where('code', 'KFT')->first();
        if ($kftTest) {
            $kftParams = [
                ['name' => 'Blood Urea', 'code' => 'UREA', 'unit' => 'mg/dL', 'normal_min' => 15, 'normal_max' => 40, 'sort_order' => 1],
                ['name' => 'Blood Urea Nitrogen (BUN)', 'code' => 'BUN', 'unit' => 'mg/dL', 'normal_min' => 7, 'normal_max' => 20, 'sort_order' => 2],
                ['name' => 'Creatinine', 'code' => 'CREAT', 'unit' => 'mg/dL', 'normal_min' => 0.7, 'normal_max' => 1.3, 'sort_order' => 3],
                ['name' => 'Uric Acid', 'code' => 'UA', 'unit' => 'mg/dL', 'normal_min_male' => 3.5, 'normal_max_male' => 7.2, 'normal_min_female' => 2.5, 'normal_max_female' => 6.2, 'sort_order' => 4],
                ['name' => 'BUN/Creatinine Ratio', 'code' => 'BUNCR', 'unit' => '', 'normal_min' => 10, 'normal_max' => 20, 'sort_order' => 5],
                ['name' => 'Sodium (Na+)', 'code' => 'NA', 'unit' => 'mEq/L', 'normal_min' => 136, 'normal_max' => 145, 'group_name' => 'ELECTROLYTES', 'sort_order' => 6],
                ['name' => 'Potassium (K+)', 'code' => 'K', 'unit' => 'mEq/L', 'normal_min' => 3.5, 'normal_max' => 5.0, 'group_name' => 'ELECTROLYTES', 'sort_order' => 7],
                ['name' => 'Chloride (Cl-)', 'code' => 'CL', 'unit' => 'mEq/L', 'normal_min' => 98, 'normal_max' => 106, 'group_name' => 'ELECTROLYTES', 'sort_order' => 8],
                ['name' => 'Calcium', 'code' => 'CA', 'unit' => 'mg/dL', 'normal_min' => 8.5, 'normal_max' => 10.5, 'group_name' => 'ELECTROLYTES', 'sort_order' => 9],
                ['name' => 'Phosphorus', 'code' => 'PHOS', 'unit' => 'mg/dL', 'normal_min' => 2.5, 'normal_max' => 4.5, 'group_name' => 'ELECTROLYTES', 'sort_order' => 10],
            ];

            foreach ($kftParams as $param) {
                TestParameter::create(array_merge($param, ['test_id' => $kftTest->id]));
            }
        }

        // Add Thyroid Profile Parameters
        $thyroidTest = Test::where('code', 'THYR')->first();
        if ($thyroidTest) {
            $thyroidParams = [
                ['name' => 'TSH (Thyroid Stimulating Hormone)', 'code' => 'TSH', 'unit' => 'µIU/mL', 'normal_min' => 0.4, 'normal_max' => 4.0, 'method' => 'CLIA', 'sort_order' => 1],
                ['name' => 'T3 (Triiodothyronine)', 'code' => 'T3', 'unit' => 'ng/dL', 'normal_min' => 80, 'normal_max' => 200, 'method' => 'CLIA', 'sort_order' => 2],
                ['name' => 'T4 (Thyroxine)', 'code' => 'T4', 'unit' => 'µg/dL', 'normal_min' => 5.1, 'normal_max' => 14.1, 'method' => 'CLIA', 'sort_order' => 3],
                ['name' => 'Free T3 (FT3)', 'code' => 'FT3', 'unit' => 'pg/mL', 'normal_min' => 2.0, 'normal_max' => 4.4, 'method' => 'CLIA', 'sort_order' => 4],
                ['name' => 'Free T4 (FT4)', 'code' => 'FT4', 'unit' => 'ng/dL', 'normal_min' => 0.93, 'normal_max' => 1.7, 'method' => 'CLIA', 'sort_order' => 5],
            ];

            foreach ($thyroidParams as $param) {
                TestParameter::create(array_merge($param, ['test_id' => $thyroidTest->id]));
            }
        }

        // Add Urine Routine Parameters
        $urineTest = Test::where('code', 'URINE')->first();
        if ($urineTest) {
            $urineParams = [
                ['name' => 'Color', 'code' => 'COLOR', 'unit' => '', 'sort_order' => 1, 'group_name' => 'PHYSICAL EXAMINATION'],
                ['name' => 'Appearance', 'code' => 'APPEAR', 'unit' => '', 'sort_order' => 2, 'group_name' => 'PHYSICAL EXAMINATION'],
                ['name' => 'Specific Gravity', 'code' => 'SG', 'unit' => '', 'normal_min' => 1.005, 'normal_max' => 1.030, 'sort_order' => 3, 'group_name' => 'PHYSICAL EXAMINATION'],
                ['name' => 'pH', 'code' => 'PH', 'unit' => '', 'normal_min' => 4.5, 'normal_max' => 8.0, 'sort_order' => 4, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Protein', 'code' => 'UPRO', 'unit' => '', 'sort_order' => 5, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Glucose', 'code' => 'UGLU', 'unit' => '', 'sort_order' => 6, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Ketones', 'code' => 'KET', 'unit' => '', 'sort_order' => 7, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Blood', 'code' => 'UBLD', 'unit' => '', 'sort_order' => 8, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Bilirubin', 'code' => 'UBIL', 'unit' => '', 'sort_order' => 9, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Urobilinogen', 'code' => 'URO', 'unit' => '', 'sort_order' => 10, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Nitrite', 'code' => 'NIT', 'unit' => '', 'sort_order' => 11, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'Leucocytes', 'code' => 'LEUC', 'unit' => '', 'sort_order' => 12, 'group_name' => 'CHEMICAL EXAMINATION'],
                ['name' => 'RBCs', 'code' => 'URBC', 'unit' => '/hpf', 'normal_min' => 0, 'normal_max' => 2, 'sort_order' => 13, 'group_name' => 'MICROSCOPIC EXAMINATION'],
                ['name' => 'Pus Cells', 'code' => 'PUS', 'unit' => '/hpf', 'normal_min' => 0, 'normal_max' => 5, 'sort_order' => 14, 'group_name' => 'MICROSCOPIC EXAMINATION'],
                ['name' => 'Epithelial Cells', 'code' => 'EPI', 'unit' => '/hpf', 'sort_order' => 15, 'group_name' => 'MICROSCOPIC EXAMINATION'],
                ['name' => 'Casts', 'code' => 'CAST', 'unit' => '/lpf', 'sort_order' => 16, 'group_name' => 'MICROSCOPIC EXAMINATION'],
                ['name' => 'Crystals', 'code' => 'CRYS', 'unit' => '', 'sort_order' => 17, 'group_name' => 'MICROSCOPIC EXAMINATION'],
                ['name' => 'Bacteria', 'code' => 'BACT', 'unit' => '', 'sort_order' => 18, 'group_name' => 'MICROSCOPIC EXAMINATION'],
            ];

            foreach ($urineParams as $param) {
                TestParameter::create(array_merge($param, ['test_id' => $urineTest->id]));
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@pathlas.com / password');
        $this->command->info('Reception: reception@pathlas.com / password');
        $this->command->info('Technician: tech@pathlas.com / password');
        $this->command->info('Pathologist: doctor@pathlas.com / password');
    }
}
