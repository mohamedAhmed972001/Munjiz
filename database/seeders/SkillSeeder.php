<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Laravel', 'PHP', 'Vue.js', 'React', 'MySQL', 
            'PostgreSQL', 'Docker', 'Git', 'UI/UX Design', 
            'Flutter', 'Python', 'DevOps', 'AWS', 'JavaScript'
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(['name' => $skill]);
        }
    }
}