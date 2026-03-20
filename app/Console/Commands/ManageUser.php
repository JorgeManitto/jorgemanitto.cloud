<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ManageUser extends Command
{
    protected $signature = 'user:manage
                            {--create : Crear un nuevo usuario}
                            {--reset : Resetear contraseña de un usuario existente}
                            {--list : Listar todos los usuarios}';

    protected $description = 'Crear usuarios o resetear contraseñas (no hay registro público)';

    public function handle(): int
    {
        if ($this->option('list')) {
            return $this->listUsers();
        }

        if ($this->option('reset')) {
            return $this->resetPassword();
        }

        // Default: create
        return $this->createUser();
    }

    private function createUser(): int
    {
        $name     = $this->ask('Nombre');
        $email    = $this->ask('Email');
        $password = $this->secret('Contraseña');

        if (User::where('email', $email)->exists()) {
            $this->error("Ya existe un usuario con el email {$email}");
            return self::FAILURE;
        }

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("✓ Usuario «{$name}» creado correctamente.");
        return self::SUCCESS;
    }

    private function resetPassword(): int
    {
        $email = $this->ask('Email del usuario');
        $user  = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró un usuario con el email {$email}");
            return self::FAILURE;
        }

        $password = $this->secret('Nueva contraseña');

        $user->update(['password' => Hash::make($password)]);

        $this->info("✓ Contraseña actualizada para «{$user->name}».");
        return self::SUCCESS;
    }

    private function listUsers(): int
    {
        $users = User::select('id', 'name', 'email', 'created_at')->get();

        if ($users->isEmpty()) {
            $this->warn('No hay usuarios registrados.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Nombre', 'Email', 'Creado'],
            $users->map(fn ($u) => [
                $u->id,
                $u->name,
                $u->email,
                $u->created_at->format('d/m/Y H:i'),
            ])
        );

        return self::SUCCESS;
    }
}
