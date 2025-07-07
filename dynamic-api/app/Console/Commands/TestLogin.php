<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-login {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test login credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("No user found with email: {$email}");
            return;
        }
        
        $this->info("User found: {$user->name} ({$user->email})");
        
        // Test password verification
        if (Hash::check($password, $user->password)) {
            $this->info("Password matches!");
        } else {
            $this->error("Password does not match!");
        }
        
        // Test Laravel Auth attempt
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->info("Laravel Auth::attempt() succeeded!");
        } else {
            $this->error("Laravel Auth::attempt() failed!");
        }
        
        // Test Filament panel access
        try {
            $panel = app('filament')->getDefaultPanel();
            if ($user->canAccessPanel($panel)) {
                $this->info("User can access Filament panel!");
            } else {
                $this->error("User cannot access Filament panel!");
            }
        } catch (\Exception $e) {
            $this->error("Error testing panel access: " . $e->getMessage());
        }
    }
}
