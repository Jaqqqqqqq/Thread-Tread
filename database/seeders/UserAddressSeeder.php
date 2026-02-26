<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAddress;
use App\Models\User;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        $juan  = User::where('email', 'juan@example.com')->first();
        $maria = User::where('email', 'maria@example.com')->first();
        $jose  = User::where('email', 'jose@example.com')->first();

        $addresses = [
            // Juan — 2 addresses (1 default)
            [
                'user_id'     => $juan->user_id,
                'label'       => 'Home',
                'street'      => '123 Rizal Street',
                'city'        => 'Manila',
                'province'    => 'Metro Manila',
                'postal_code' => '1000',
                'country'     => 'Philippines',
                'is_default'  => true,
            ],
            [
                'user_id'     => $juan->user_id,
                'label'       => 'Office',
                'street'      => '456 Ayala Avenue',
                'city'        => 'Makati',
                'province'    => 'Metro Manila',
                'postal_code' => '1226',
                'country'     => 'Philippines',
                'is_default'  => false,
            ],
            // Maria — 1 address (default)
            [
                'user_id'     => $maria->user_id,
                'label'       => 'Home',
                'street'      => '456 Mabini Avenue',
                'city'        => 'Quezon City',
                'province'    => 'Metro Manila',
                'postal_code' => '1100',
                'country'     => 'Philippines',
                'is_default'  => true,
            ],
            // Jose — 1 address (default)
            [
                'user_id'     => $jose->user_id,
                'label'       => 'Home',
                'street'      => '789 Bonifacio Boulevard',
                'city'        => 'Cebu City',
                'province'    => 'Cebu',
                'postal_code' => '6000',
                'country'     => 'Philippines',
                'is_default'  => true,
            ],
        ];

        foreach ($addresses as $address) {
            UserAddress::create($address);
        }
    }
}