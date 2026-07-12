<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:migrate-passwords')]
#[Description('Hash all existing plaintext passwords in the users table')]
class MigratePasswords extends Command
{
    public function handle(): void
    {
        $users = \App\Models\User::whereNull('password')->orWhere('password', '')->get();

        $this->info("Found {$users->count()} users with unhashed passwords.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            if ($user->passwrd) {
                $user->password = \Illuminate\Support\Facades\Hash::make($user->passwrd);
                $user->save();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Password migration complete.');
    }
}
