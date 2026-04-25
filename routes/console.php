<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:password {email? : Admin user email (default: first user with role admin)} {--password= : Non-interactive: set this value (at least 8 characters)}', function () {
    $email = $this->argument('email') !== null ? (string) $this->argument('email') : null;
    $user = $email !== null && $email !== ''
        ? User::query()->where('role', 'admin')->where('email', $email)->first()
        : User::query()->where('role', 'admin')->orderBy('id')->first();

    if (! $user) {
        $this->error($email
            ? "No admin user with email [{$email}]."
            : 'No user with role "admin" was found. Pass an email: php artisan admin:password you@example.com');

        return 1;
    }

    $this->line("Target: <fg=cyan>{$user->email}</> ({$user->name})");

    if ($this->option('password') !== null && $this->option('password') !== '') {
        $password = (string) $this->option('password');
    } else {
        $password = (string) $this->secret('New password');
        if ($password === '') {
            $this->error('Password cannot be empty.');

            return 1;
        }
        $confirm = (string) $this->secret('Confirm new password');
        if (! hash_equals($password, $confirm)) {
            $this->error('Passwords do not match.');

            return 1;
        }
    }

    if (strlen($password) < 8) {
        $this->error('The password must be at least 8 characters.');

        return 1;
    }

    $user->password = $password;
    $user->save();

    $this->info("Password updated for [{$user->email}].");

    return 0;
})->purpose('Set the password for an admin user (interactive, or use --password= for scripts)');
