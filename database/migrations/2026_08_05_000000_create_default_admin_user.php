<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        User::updateOrCreate([
            'email' => 'admin@turingdesenvolvimento.com',
        ], [
            'name' => 'Administrador',
            'password' => 'admin@123',
            'active' => true,
        ]);
    }

    public function down(): void
    {
        User::where('email', 'admin@turingdesenvolvimento.com')->delete();
    }
};
