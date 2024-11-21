<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\SubService;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a User
        $user = User::create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('11223344'),
            'type' => 'barber',
            'image' => 'default.jpg',
            'phone_no' => '1234567890',
            'city' => 'New York',
            'address' => '123 Main St',
            'zipcode' => '10001',
            'api_token' => Str::random(60),
        ]);

        // Create a Service linked to the user
        $service = Service::create([
            'user_id' => $user->id,
            'location_type' => 'shop',
            'shop_name' => 'John\'s Barbershop',
            'country' => 'USA',
            'city' => 'New York',
            'building' => 'Building A',
            'zipcode' => '10001',
            'time_open_close' => '09:00 - 17:00',
            'book_before' => '24 hours',
            'phone_no' => '1234567890',
            'bio' => 'Experienced barber with over 10 years of experience.',
        ]);

        // Create multiple SubServices linked to the service and user
        $subServicesData = [
            [
                'name' => 'Haircut',
                'price' => 25,
                'time' => '30 min',
                'description' => 'A stylish haircut for men.',
            ],
            [
                'name' => 'Beard Trim',
                'price' => 15,
                'time' => '15 min',
                'description' => 'A precise beard trim to keep you looking sharp.',
            ],
            [
                'name' => 'Shave',
                'price' => 20,
                'time' => '20 min',
                'description' => 'A clean and smooth shave.',
            ],
        ];

        foreach ($subServicesData as $subServiceData) {
            SubService::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'name' => $subServiceData['name'],
                'price' => $subServiceData['price'],
                'time' => $subServiceData['time'],
                'description' => $subServiceData['description'],
            ]);
        }

        // Output a success message to indicate seeding completion
        $this->command->info('Initial data seeded successfully!');
    }
}
