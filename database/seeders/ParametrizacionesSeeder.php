<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoEvento;
use App\Models\CategoriaMegaEvento;
use App\Models\Ciudad;
use App\Models\Lugar;
use App\Models\EstadoParticipacion;
use App\Models\TipoNotificacion;
use App\Models\EstadoEvento;
use App\Models\TipoUsuario;

class ParametrizacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================
        // TIPOS DE EVENTO
        // ============================================
        $tiposEvento = [
            ['codigo' => 'conferencia', 'nombre' => 'Conferencia', 'descripcion' => 'Evento de conferencia o charla', 'icono' => 'fas fa-microphone', 'color' => 'primary', 'orden' => 1],
            ['codigo' => 'taller', 'nombre' => 'Taller', 'descripcion' => 'Taller práctico', 'icono' => 'fas fa-tools', 'color' => 'info', 'orden' => 2],
            ['codigo' => 'seminario', 'nombre' => 'Seminario', 'descripcion' => 'Seminario académico', 'icono' => 'fas fa-graduation-cap', 'color' => 'success', 'orden' => 3],
            ['codigo' => 'voluntariado', 'nombre' => 'Voluntariado', 'descripcion' => 'Actividad de voluntariado', 'icono' => 'fas fa-hands-helping', 'color' => 'warning', 'orden' => 4],
            ['codigo' => 'cultural', 'nombre' => 'Cultural', 'descripcion' => 'Evento cultural', 'icono' => 'fas fa-theater-masks', 'color' => 'purple', 'orden' => 5],
            ['codigo' => 'deportivo', 'nombre' => 'Deportivo', 'descripcion' => 'Evento deportivo', 'icono' => 'fas fa-running', 'color' => 'danger', 'orden' => 6],
            ['codigo' => 'otro', 'nombre' => 'Otro', 'descripcion' => 'Otro tipo de evento', 'icono' => 'fas fa-calendar', 'color' => 'secondary', 'orden' => 7],
        ];

        foreach ($tiposEvento as $tipo) {
            TipoEvento::create($tipo);
        }

        // ============================================
        // CATEGORÍAS DE MEGA EVENTOS
        // ============================================
        $categoriasMegaEvento = [
            ['codigo' => 'social', 'nombre' => 'Social', 'descripcion' => 'Evento social', 'icono' => 'fas fa-users', 'color' => 'primary', 'orden' => 1],
            ['codigo' => 'cultural', 'nombre' => 'Cultural', 'descripcion' => 'Evento cultural', 'icono' => 'fas fa-theater-masks', 'color' => 'purple', 'orden' => 2],
            ['codigo' => 'deportivo', 'nombre' => 'Deportivo', 'descripcion' => 'Evento deportivo', 'icono' => 'fas fa-running', 'color' => 'danger', 'orden' => 3],
            ['codigo' => 'educativo', 'nombre' => 'Educativo', 'descripcion' => 'Evento educativo', 'icono' => 'fas fa-graduation-cap', 'color' => 'info', 'orden' => 4],
            ['codigo' => 'benefico', 'nombre' => 'Benéfico', 'descripcion' => 'Evento benéfico', 'icono' => 'fas fa-heart', 'color' => 'danger', 'orden' => 5],
            ['codigo' => 'ambiental', 'nombre' => 'Ambiental', 'descripcion' => 'Evento ambiental', 'icono' => 'fas fa-leaf', 'color' => 'success', 'orden' => 6],
            ['codigo' => 'otro', 'nombre' => 'Otro', 'descripcion' => 'Otra categoría', 'icono' => 'fas fa-calendar', 'color' => 'secondary', 'orden' => 7],
        ];

        foreach ($categoriasMegaEvento as $categoria) {
            CategoriaMegaEvento::create($categoria);
        }

        // ============================================
        // CIUDADES (Principales ciudades de Bolivia)
        // ============================================
        $ciudades = [
            ['nombre' => 'Santa Cruz de la Sierra', 'departamento' => 'Santa Cruz', 'pais' => 'Bolivia', 'lat' => -17.8146, 'lng' => -63.1561],
            ['nombre' => 'La Paz', 'departamento' => 'La Paz', 'pais' => 'Bolivia', 'lat' => -16.5000, 'lng' => -68.1500],
            ['nombre' => 'Cochabamba', 'departamento' => 'Cochabamba', 'pais' => 'Bolivia', 'lat' => -17.3935, 'lng' => -66.1570],
            ['nombre' => 'Sucre', 'departamento' => 'Chuquisaca', 'pais' => 'Bolivia', 'lat' => -19.0196, 'lng' => -65.2620],
            ['nombre' => 'Oruro', 'departamento' => 'Oruro', 'pais' => 'Bolivia', 'lat' => -17.9750, 'lng' => -67.1100],
            ['nombre' => 'Potosí', 'departamento' => 'Potosí', 'pais' => 'Bolivia', 'lat' => -19.5833, 'lng' => -65.7500],
            ['nombre' => 'Tarija', 'departamento' => 'Tarija', 'pais' => 'Bolivia', 'lat' => -21.5311, 'lng' => -64.7311],
            ['nombre' => 'Trinidad', 'departamento' => 'Beni', 'pais' => 'Bolivia', 'lat' => -14.8333, 'lng' => -64.9000],
            ['nombre' => 'Cobija', 'departamento' => 'Pando', 'pais' => 'Bolivia', 'lat' => -11.0333, 'lng' => -68.7333],
        ];

        foreach ($ciudades as $ciudad) {
            Ciudad::create($ciudad);
        }

        // ============================================
        // ESTADOS DE PARTICIPACIÓN
        // ============================================
        $estadosParticipacion = [
            ['codigo' => 'pendiente', 'nombre' => 'Pendiente', 'descripcion' => 'Solicitud pendiente de aprobación', 'color' => 'warning', 'icono' => 'fas fa-clock', 'orden' => 1],
            ['codigo' => 'aprobada', 'nombre' => 'Aprobada', 'descripcion' => 'Participación aprobada', 'color' => 'success', 'icono' => 'fas fa-check-circle', 'orden' => 2],
            ['codigo' => 'rechazada', 'nombre' => 'Rechazada', 'descripcion' => 'Participación rechazada', 'color' => 'danger', 'icono' => 'fas fa-times-circle', 'orden' => 3],
        ];

        foreach ($estadosParticipacion as $estado) {
            EstadoParticipacion::create($estado);
        }

        // ============================================
        // TIPOS DE NOTIFICACIÓN
        // ============================================
        $tiposNotificacion = [
            [
                'codigo' => 'reaccion_evento',
                'nombre' => 'Reacción a Evento',
                'descripcion' => 'Notificación cuando un usuario reacciona a un evento',
                'plantilla_mensaje' => '{usuario} reaccionó a tu evento "{evento}"',
                'icono' => 'fas fa-heart',
                'color' => 'danger'
            ],
            [
                'codigo' => 'nueva_participacion',
                'nombre' => 'Nueva Participación',
                'descripcion' => 'Notificación cuando un usuario se inscribe a un evento',
                'plantilla_mensaje' => '{usuario} se inscribió a tu evento "{evento}"',
                'icono' => 'fas fa-user-plus',
                'color' => 'info'
            ],
        ];

        foreach ($tiposNotificacion as $tipo) {
            TipoNotificacion::create($tipo);
        }

        // ============================================
        // ESTADOS DE EVENTO
        // ============================================
        $estadosEvento = [
            // Estados para eventos regulares
            ['codigo' => 'borrador', 'nombre' => 'Borrador', 'descripcion' => 'Evento en borrador', 'tipo' => 'evento', 'color' => 'secondary', 'icono' => 'fas fa-edit', 'orden' => 1],
            ['codigo' => 'publicado', 'nombre' => 'Publicado', 'descripcion' => 'Evento publicado', 'tipo' => 'evento', 'color' => 'success', 'icono' => 'fas fa-check', 'orden' => 2],
            ['codigo' => 'cancelado', 'nombre' => 'Cancelado', 'descripcion' => 'Evento cancelado', 'tipo' => 'evento', 'color' => 'danger', 'icono' => 'fas fa-times', 'orden' => 3],
            
            // Estados para mega eventos
            ['codigo' => 'planificacion', 'nombre' => 'En Planificación', 'descripcion' => 'Mega evento en planificación', 'tipo' => 'mega_evento', 'color' => 'info', 'icono' => 'fas fa-calendar-alt', 'orden' => 1],
            ['codigo' => 'activo', 'nombre' => 'Activo', 'descripcion' => 'Mega evento activo', 'tipo' => 'mega_evento', 'color' => 'success', 'icono' => 'fas fa-play', 'orden' => 2],
            ['codigo' => 'en_curso', 'nombre' => 'En Curso', 'descripcion' => 'Mega evento en curso', 'tipo' => 'mega_evento', 'color' => 'warning', 'icono' => 'fas fa-spinner', 'orden' => 3],
            ['codigo' => 'finalizado', 'nombre' => 'Finalizado', 'descripcion' => 'Mega evento finalizado', 'tipo' => 'mega_evento', 'color' => 'secondary', 'icono' => 'fas fa-check-circle', 'orden' => 4],
            ['codigo' => 'cancelado_mega', 'nombre' => 'Cancelado', 'descripcion' => 'Mega evento cancelado', 'tipo' => 'mega_evento', 'color' => 'danger', 'icono' => 'fas fa-times-circle', 'orden' => 5],
        ];

        foreach ($estadosEvento as $estado) {
            EstadoEvento::create($estado);
        }

        // ============================================
        // TIPOS DE USUARIO
        // ============================================
        $tiposUsuario = [
            [
                'codigo' => 'super_admin',
                'nombre' => 'Super Admin',
                'descripcion' => 'Administrador del sistema',
                'permisos_default' => ['*']
            ],
            [
                'codigo' => 'ong',
                'nombre' => 'ONG',
                'descripcion' => 'Organización No Gubernamental',
                'permisos_default' => ['eventos.*', 'mega_eventos.*', 'participaciones.*', 'notificaciones.*']
            ],
            [
                'codigo' => 'empresa',
                'nombre' => 'Empresa',
                'descripcion' => 'Empresa patrocinadora',
                'permisos_default' => ['eventos.ver', 'eventos.patrocinar']
            ],
            [
                'codigo' => 'externo',
                'nombre' => 'Integrante Externo',
                'descripcion' => 'Usuario externo o voluntario',
                'permisos_default' => ['eventos.ver', 'eventos.inscribirse', 'eventos.reaccionar']
            ],
        ];

        foreach ($tiposUsuario as $tipo) {
            TipoUsuario::create($tipo);
        }

        $this->command->info('✅ Parametrizaciones creadas exitosamente!');
        $this->command->info('   - ' . count($tiposEvento) . ' tipos de evento');
        $this->command->info('   - ' . count($categoriasMegaEvento) . ' categorías de mega eventos');
        $this->command->info('   - ' . count($ciudades) . ' ciudades');
        $this->command->info('   - ' . count($estadosParticipacion) . ' estados de participación');
        $this->command->info('   - ' . count($tiposNotificacion) . ' tipos de notificación');
        $this->command->info('   - ' . count($estadosEvento) . ' estados de evento');
        $this->command->info('   - ' . count($tiposUsuario) . ' tipos de usuario');
    }
}
