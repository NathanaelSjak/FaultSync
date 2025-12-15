<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $bankNames = [
            'Bank Mandiri',
            'Bank BCA',
            'Bank BRI',
            'Bank BNI',
            'Bank CIMB Niaga',
            'Bank Danamon',
            'Bank Permata',
            'Bank OCBC NISP',
        ];

        foreach ($users as $user) {
            // Create 2-4 bank accounts per user
            $accountCount = rand(2, 4);
            
            for ($i = 0; $i < $accountCount; $i++) {
                $bankName = $bankNames[array_rand($bankNames)];
                $accountNumber = str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
                $types = ['savings', 'checking', 'credit'];
                $type = $types[array_rand($types)];

                BankAccount::create([
                    'user_id' => $user->id,
                    'bank_name' => $bankName,
                    'account_number' => $accountNumber,
                    'type' => $type,
                    'description' => 'Akun ' . $type . ' - ' . $bankName,
                ]);
            }
        }
    }
}
