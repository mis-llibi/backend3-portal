<?php

namespace App\Models\Dental_insurance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class members extends Model
{
    use HasFactory;

    protected $table = 'dental_insurance_members';

    protected $fillable = [
        'member_id',
        'department',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'relation',
        'plan',
        'per_surface_light_cure',
        'per_tooth_light_cure',
        'addition_oral_prophylaxis',
        'peri_apical_x_ray',
        'panoramic_dental_x_ray',
        'surgical_tooth_extraction',
        'root_canal',
    ];

}
