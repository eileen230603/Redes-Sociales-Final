<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRolesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spatie:assign-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar roles de Spatie a usuarios existentes basándose en su tipo_usuario';

    /**
     * Mapeo de tipos de usuario a roles de Spatie
     */
    protected $tipoUsuarioToRole = [
        'Super admin' => 'Super Admin',
        'ONG' => 'ONG',
        'Empresa' => 'Empresa',
        'Integrante externo' => 'Integrante Externo',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Asignando roles de Spatie a usuarios existentes...');
        $this->newLine();

        // Verificar que los roles existan
        $rolesExistentes = Role::pluck('name')->toArray();
        $rolesNecesarios = array_values($this->tipoUsuarioToRole);
        
        $rolesFaltantes = array_diff($rolesNecesarios, $rolesExistentes);
        if (!empty($rolesFaltantes)) {
            $this->error('❌ Los siguientes roles no existen: ' . implode(', ', $rolesFaltantes));
            $this->info('💡 Ejecuta primero: php artisan db:seed --class=RolesAndPermissionsSeeder');
            return 1;
        }

        $usuarios = User::all();
        $asignados = 0;
        $sinTipo = 0;
        $yaTienenRol = 0;

        foreach ($usuarios as $usuario) {
            $tipoUsuario = $usuario->tipo_usuario;

            if (empty($tipoUsuario)) {
                $sinTipo++;
                continue;
            }

            // Verificar si el usuario ya tiene roles asignados
            if ($usuario->roles->count() > 0) {
                $yaTienenRol++;
                continue;
            }

            // Obtener el nombre del rol correspondiente
            $nombreRol = $this->tipoUsuarioToRole[$tipoUsuario] ?? null;

            if ($nombreRol) {
                try {
                    $rol = Role::findByName($nombreRol, 'web');
                    $usuario->assignRole($rol);
                    $asignados++;
                    $this->line("  ✓ {$usuario->nombre_usuario} ({$tipoUsuario}) → {$nombreRol}");
                } catch (\Exception $e) {
                    $this->error("  ✗ Error asignando rol a {$usuario->nombre_usuario}: " . $e->getMessage());
                }
            } else {
                $this->warn("  ⚠ Tipo de usuario desconocido: {$tipoUsuario} para usuario {$usuario->nombre_usuario}");
            }
        }

        $this->newLine();
        $this->info("✅ Proceso completado:");
        $this->line("   - Roles asignados: {$asignados}");
        $this->line("   - Ya tenían rol: {$yaTienenRol}");
        $this->line("   - Sin tipo de usuario: {$sinTipo}");
        $this->line("   - Total usuarios procesados: " . $usuarios->count());

        return 0;
    }
}
