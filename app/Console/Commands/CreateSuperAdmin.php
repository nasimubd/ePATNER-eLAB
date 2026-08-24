<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-super-admin
                            {name : The administrator name}
                            {email : The administrator email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or reset a super-administrator account using an interactive password prompt';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = trim($this->argument('name'));
        $email = strtolower(trim($this->argument('email')));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Provide a name and a valid email address.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user && ! $this->confirm("An account for {$email} already exists. Reset its password and assign super-admin access?")) {
            $this->warn('No changes were made.');

            return self::SUCCESS;
        }

        $password = $this->secret('Password');
        $confirmation = $this->secret('Confirm password');

        if ($password === false || $password === '' || $password !== $confirmation) {
            $this->error('Passwords must be non-empty and match.');

            return self::FAILURE;
        }

        $user ??= new User();
        $user->fill([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'business_id' => null,
            'email_verified_at' => now(),
        ]);
        $user->save();
        $user->syncRoles('super-admin');

        $this->info("Super-admin access configured for {$email}.");

        return self::SUCCESS;
    }
}
