<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $bankAccounts = BankAccount::where('user_id', $user->id)->get();
            $incomeCategories = Category::where('user_id', $user->id)->where('type', 'income')->get();
            $expenseCategories = Category::where('user_id', $user->id)->where('type', 'expense')->get();

            if ($bankAccounts->isEmpty() || ($incomeCategories->isEmpty() && $expenseCategories->isEmpty())) {
                continue;
            }

            $startDate = Carbon::now()->subMonths(3);
            $endDate = Carbon::now();

            $incomeCount = rand(20, 30);
            for ($i = 0; $i < $incomeCount; $i++) {
                $date = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );

                Transaction::create([
                    'user_id' => $user->id,
                    'bank_account_id' => $bankAccounts->random()->id,
                    'category_id' => $incomeCategories->isNotEmpty() ? $incomeCategories->random()->id : null,
                    'type' => 'income',
                    'amount' => rand(500000, 10000000),
                    'description' => $this->getIncomeDescription(),
                    'date' => $date->format('Y-m-d'),
                ]);
            }

            $expenseCount = rand(30, 50);
            for ($i = 0; $i < $expenseCount; $i++) {
                $date = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );

                Transaction::create([
                    'user_id' => $user->id,
                    'bank_account_id' => $bankAccounts->random()->id,
                    'category_id' => $expenseCategories->isNotEmpty() ? $expenseCategories->random()->id : null,
                    'type' => 'expense',
                    'amount' => rand(10000, 5000000),
                    'description' => $this->getExpenseDescription(),
                    'date' => $date->format('Y-m-d'),
                ]);
            }
        }
    }

    private function getIncomeDescription(): string
    {
        $descriptions = [
            'Gaji bulanan',
            'Bonus kinerja',
            'Pendapatan freelance',
            'Dividen investasi',
            'Pembayaran proyek',
            'Komisi penjualan',
            'Refund pembelian',
            'Hadiah',
        ];
        return $descriptions[array_rand($descriptions)];
    }

    private function getExpenseDescription(): string
    {
        $descriptions = [
            'Belanja bulanan',
            'Makan siang',
            'Bensin kendaraan',
            'Tagihan listrik',
            'Tagihan internet',
            'Tagihan air',
            'Belanja kebutuhan rumah',
            'Makan malam',
            'Kopi pagi',
            'Parkir',
            'Tol',
            'Uber/Grab',
            'Belanja online',
            'Bayar tagihan kartu kredit',
            'Biaya kesehatan',
        ];
        return $descriptions[array_rand($descriptions)];
    }
}
