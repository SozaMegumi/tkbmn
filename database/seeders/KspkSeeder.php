<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class KspkSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old subjects first
        Subject::truncate(); 

        $subjects = [
            // MALAY LANGUAGE 
            ['komponen' => 'MALAY LANGUAGE', 'subject_code' => 'BM1', 'subject_name' => 'Listening and Speaking'],
            ['komponen' => 'MALAY LANGUAGE', 'subject_code' => 'BM2', 'subject_name' => 'Reading'],
            ['komponen' => 'MALAY LANGUAGE', 'subject_code' => 'BM3', 'subject_name' => 'Writing'],
            
            // ENGLISH LANGUAGE
            ['komponen' => 'ENGLISH LANGUAGE', 'subject_code' => 'BI1', 'subject_name' => 'Listening and Speaking'],
            ['komponen' => 'ENGLISH LANGUAGE', 'subject_code' => 'BI2', 'subject_name' => 'Reading'],
            ['komponen' => 'ENGLISH LANGUAGE', 'subject_code' => 'BI3', 'subject_name' => 'Writing'],

            // ISLAMIC EDUCATION
            ['komponen' => 'ISLAMIC EDUCATION', 'subject_code' => 'PI1', 'subject_name' => 'Al-Quran'],
            ['komponen' => 'ISLAMIC EDUCATION', 'subject_code' => 'PI2', 'subject_name' => 'Faith (Akidah)'],
            ['komponen' => 'ISLAMIC EDUCATION', 'subject_code' => 'PI3', 'subject_name' => 'Worship (Ibadah)'],
            ['komponen' => 'ISLAMIC EDUCATION', 'subject_code' => 'PI4', 'subject_name' => 'History (Sirah)'],
            ['komponen' => 'ISLAMIC EDUCATION', 'subject_code' => 'PI5', 'subject_name' => 'Morals (Akhlak)'],

            // EARLY MATHEMATICS
            ['komponen' => 'EARLY MATHEMATICS', 'subject_code' => 'MT1', 'subject_name' => 'Pre-number Experience'],
            ['komponen' => 'EARLY MATHEMATICS', 'subject_code' => 'MT2', 'subject_name' => 'Number Concept'],
            ['komponen' => 'EARLY MATHEMATICS', 'subject_code' => 'MT3', 'subject_name' => 'Number Operations'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}