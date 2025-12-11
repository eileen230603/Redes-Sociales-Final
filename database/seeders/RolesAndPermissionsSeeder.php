<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // CREAR PERMISOS
        // ============================================
        
        // Permisos de Eventos
        $permisosEventos = [
            'eventos.ver',
            'eventos.crear',
            'eventos.editar',
            'eventos.eliminar',
            'eventos.gestionar',
            'eventos.patrocinar',
            'eventos.inscribirse',
            'eventos.reaccionar',
            'eventos.compartir',
            'eventos.control-asistencia',
            'eventos.ver-participantes',
            'eventos.exportar-reportes',
        ];

        // Permisos de Mega Eventos
        $permisosMegaEventos = [
            'mega-eventos.ver',
            'mega-eventos.crear',
            'mega-eventos.editar',
            'mega-eventos.eliminar',
            'mega-eventos.gestionar',
            'mega-eventos.participar',
            'mega-eventos.reaccionar',
            'mega-eventos.compartir',
            'mega-eventos.control-asistencia',
            'mega-eventos.ver-participantes',
            'mega-eventos.exportar-reportes',
        ];

        // Permisos de Participaciones
        $permisosParticipaciones = [
            'participaciones.ver',
            'participaciones.gestionar',
            'participaciones.aprobar',
            'participaciones.rechazar',
            'participaciones.ver-mis-participaciones',
        ];

        // Permisos de Reportes y Dashboards
        $permisosReportes = [
            'reportes.ver',
            'reportes.ver-basicos',
            'reportes.ver-avanzados',
            'reportes.exportar',
            'dashboard.ver-ong',
            'dashboard.ver-empresa',
            'dashboard.ver-externo',
        ];

        // Permisos de Notificaciones
        $permisosNotificaciones = [
            'notificaciones.ver',
            'notificaciones.gestionar',
        ];

        // Permisos de Configuración y Parametrizaciones
        $permisosConfiguracion = [
            'configuracion.ver',
            'configuracion.gestionar',
            'parametrizaciones.ver',
            'parametrizaciones.gestionar',
        ];

        // Permisos de Usuarios y Voluntarios
        $permisosUsuarios = [
            'usuarios.ver',
            'usuarios.gestionar',
            'voluntarios.ver',
            'voluntarios.gestionar',
        ];

        // Permisos de Perfil
        $permisosPerfil = [
            'perfil.ver',
            'perfil.editar',
        ];

        // Combinar todos los permisos
        $todosLosPermisos = array_merge(
            $permisosEventos,
            $permisosMegaEventos,
            $permisosParticipaciones,
            $permisosReportes,
            $permisosNotificaciones,
            $permisosConfiguracion,
            $permisosUsuarios,
            $permisosPerfil
        );

        // Crear permisos
        foreach ($todosLosPermisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $this->command->info('✅ Permisos creados: ' . count($todosLosPermisos));

        // ============================================
        // CREAR ROLES Y ASIGNAR PERMISOS
        // ============================================

        // 1. SUPER ADMIN - Todos los permisos
        $rolSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $rolSuperAdmin->syncPermissions(Permission::all());
        $this->command->info('✅ Rol "Super Admin" creado con todos los permisos');

        // 2. ONG - Permisos completos de gestión
        $rolOng = Role::firstOrCreate(['name' => 'ONG', 'guard_name' => 'web']);
        $rolOng->syncPermissions([
            // Eventos
            'eventos.ver',
            'eventos.crear',
            'eventos.editar',
            'eventos.eliminar',
            'eventos.gestionar',
            'eventos.control-asistencia',
            'eventos.ver-participantes',
            'eventos.exportar-reportes',
            // Mega Eventos
            'mega-eventos.ver',
            'mega-eventos.crear',
            'mega-eventos.editar',
            'mega-eventos.eliminar',
            'mega-eventos.gestionar',
            'mega-eventos.control-asistencia',
            'mega-eventos.ver-participantes',
            'mega-eventos.exportar-reportes',
            // Participaciones
            'participaciones.ver',
            'participaciones.gestionar',
            'participaciones.aprobar',
            'participaciones.rechazar',
            // Reportes
            'reportes.ver',
            'reportes.ver-basicos',
            'reportes.ver-avanzados',
            'reportes.exportar',
            'dashboard.ver-ong',
            // Notificaciones
            'notificaciones.ver',
            'notificaciones.gestionar',
            // Voluntarios
            'voluntarios.ver',
            'voluntarios.gestionar',
            // Perfil
            'perfil.ver',
            'perfil.editar',
        ]);
        $this->command->info('✅ Rol "ONG" creado con permisos de gestión');

        // 3. EMPRESA - Permisos limitados
        $rolEmpresa = Role::firstOrCreate(['name' => 'Empresa', 'guard_name' => 'web']);
        $rolEmpresa->syncPermissions([
            // Eventos
            'eventos.ver',
            'eventos.patrocinar',
            'eventos.reaccionar',
            'eventos.compartir',
            // Mega Eventos
            'mega-eventos.ver',
            'mega-eventos.reaccionar',
            'mega-eventos.compartir',
            // Participaciones
            'participaciones.ver-mis-participaciones',
            // Reportes
            'reportes.ver-basicos',
            'dashboard.ver-empresa',
            // Notificaciones
            'notificaciones.ver',
            // Perfil
            'perfil.ver',
            'perfil.editar',
        ]);
        $this->command->info('✅ Rol "Empresa" creado con permisos limitados');

        // 4. INTEGRANTE EXTERNO - Permisos básicos
        $rolExterno = Role::firstOrCreate(['name' => 'Integrante Externo', 'guard_name' => 'web']);
        $rolExterno->syncPermissions([
            // Eventos
            'eventos.ver',
            'eventos.inscribirse',
            'eventos.reaccionar',
            'eventos.compartir',
            // Mega Eventos
            'mega-eventos.ver',
            'mega-eventos.participar',
            'mega-eventos.reaccionar',
            'mega-eventos.compartir',
            // Participaciones
            'participaciones.ver-mis-participaciones',
            // Reportes
            'reportes.ver-basicos',
            'dashboard.ver-externo',
            // Notificaciones
            'notificaciones.ver',
            // Perfil
            'perfil.ver',
            'perfil.editar',
        ]);
        $this->command->info('✅ Rol "Integrante Externo" creado con permisos básicos');

        $this->command->info('');
        $this->command->info('✅ Roles y permisos creados exitosamente!');
        $this->command->info('   - ' . count($todosLosPermisos) . ' permisos');
        $this->command->info('   - 4 roles (Super Admin, ONG, Empresa, Integrante Externo)');
    }
}

