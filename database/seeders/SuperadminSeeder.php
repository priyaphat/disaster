<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Department;
use App\Models\Geography;
use App\Models\Image;
use App\Models\Position;
use App\Models\Prefix;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SuperadminSeeder extends Seeder
{
    /*
     * php artisan db:seed --class=SuperadminSeeder
    */
    public function run(): void
    {
        $faker = fake('th_TH');
        $type = fake()->randomElement(UserType::cases())->value;
        if ($type == UserType::INSIDE_AREA->value) {
            $districtId = Geography::where('province_name_th', 'เชียงราย')
                ->inRandomOrder()
                ->first()?->district_code;
        } else {
            $districtId = Geography::inRandomOrder()
                ->first()?->district_code;
        }

        $user = User::firstOrCreate(
            ['email' => 'support@g-eoc-th.com'],
            [
                'username' => 'geoc-th',
                'password' => Hash::make('Ya8s&lj022!oAwc'),
                'prefix_id' => Prefix::inRandomOrder()->first()?->id,
                'firstname' => 'Suport@g-eco-th.com',
                'lastname' => '.',
                'phone' => fake()->numerify('0#########'),
                'type' => $type,
                'district_id' => $districtId,
                'department_id' => Department::inRandomOrder()->first()?->id,
                'position_id' => Position::inRandomOrder()->first()?->id,
                'remember_token' => Str::random(10),
            ]
        );

        $user->approval()->create([
            'is_approved' => 1,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $user->image()->create([
            'original_name' => 'default.png',
            'image_name'    => 'default.png',
            'image_url'     => 'image/users/default.png',
            'image_type'    => Image::PRIVATE,
        ]);

        $user->assignRole('admin');
        // $user->syncRoles('admin');
    }
}
