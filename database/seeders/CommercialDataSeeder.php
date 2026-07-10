<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Goal;
use App\Models\MonthlyStat;
use App\Models\Opportunity;
use Illuminate\Database\Seeder;

class CommercialDataSeeder extends Seeder
{
    private const MONTHS = [
        'Enero' => 1, 'Febrero' => 2, 'Marzo' => 3, 'Abril' => 4,
        'Mayo' => 5, 'Junio' => 6, 'Julio' => 7, 'Agosto' => 8,
        'Septiembre' => 9, 'Octubre' => 10, 'Noviembre' => 11, 'Diciembre' => 12,
        'Otros 2026' => 0,
    ];

    private const CATEGORY_MAP = [
        'facturado' => 'facturado',
        'perdido' => 'perdido',
        'pipelineNuevo' => 'pipeline',
    ];

    /**
     * Opportunities won but not yet invoiced (matched by client + service).
     */
    private const PENDING_INVOICE = [
        ['cliente' => 'BHD', 'servicio' => 'Elearning técnico'],
        ['cliente' => 'Superintendencia de Bancos', 'servicio' => 'Teambuilding'],
        ['cliente' => 'An Uniformes', 'servicio' => 'Programa modular ventas'],
        ['cliente' => 'Trane Trade', 'servicio' => 'Teambuilding'],
    ];

    public function run(): void
    {
        Goal::updateOrCreate(['year' => 2026], ['amount' => 154317]);

        foreach ($this->monthlyStats() as $stat) {
            MonthlyStat::updateOrCreate(
                ['year' => 2026, 'month' => self::MONTHS[$stat['mes']]],
                ['total_appointments' => $stat['citasTotales'], 'new_appointments' => $stat['citasNuevas']]
            );
        }

        $clients = [];

        foreach ($this->detalle() as $row) {
            $clientName = $row['cliente'];

            if (! isset($clients[$clientName])) {
                $clients[$clientName] = Client::firstOrCreate(['name' => $clientName]);
            }

            $pendingInvoice = collect(self::PENDING_INVOICE)->contains(
                fn ($p) => $p['cliente'] === $row['cliente'] && $p['servicio'] === $row['servicio']
            );

            Opportunity::create([
                'client_id' => $clients[$clientName]->id,
                'service' => $row['servicio'] === '—' ? null : $row['servicio'],
                'amount' => $row['monto'],
                'category' => self::CATEGORY_MAP[$row['categoria']],
                'status' => $row['estado'],
                'month' => self::MONTHS[$row['mes']],
                'year' => 2026,
                'pending_invoice' => $pendingInvoice,
            ]);
        }
    }

    private function monthlyStats(): array
    {
        return [
            ['mes' => 'Abril', 'citasTotales' => 22, 'citasNuevas' => 9],
            ['mes' => 'Mayo', 'citasTotales' => 17, 'citasNuevas' => 9],
            ['mes' => 'Junio', 'citasTotales' => 16, 'citasNuevas' => 9],
        ];
    }

    private function detalle(): array
    {
        return [
            // --- Facturado ---
            ['mes' => 'Febrero', 'categoria' => 'facturado', 'cliente' => 'Acap', 'servicio' => 'Elearning técnico', 'monto' => 5766, 'estado' => 'Pagado'],
            ['mes' => 'Febrero', 'categoria' => 'facturado', 'cliente' => 'Mallen', 'servicio' => 'Programa de gerentes de distrito', 'monto' => 9989.5, 'estado' => 'Pagado'],
            ['mes' => 'Marzo', 'categoria' => 'facturado', 'cliente' => 'Superintendencia de Bancos', 'servicio' => 'Kickoff a Gestión Humana', 'monto' => 5000, 'estado' => 'Pagado'],
            ['mes' => 'Mayo', 'categoria' => 'facturado', 'cliente' => 'ET Heinsen', 'servicio' => 'Taller fundamentos de liderazgo', 'monto' => 2000, 'estado' => 'Pagado'],
            ['mes' => 'Mayo', 'categoria' => 'facturado', 'cliente' => 'Popular', 'servicio' => 'Talleres modulares - Liderazgo lateral', 'monto' => 4650, 'estado' => 'Pagado'],
            ['mes' => 'Mayo', 'categoria' => 'facturado', 'cliente' => 'Caribe', 'servicio' => 'Taller perform', 'monto' => 1650, 'estado' => 'Pagado'],
            ['mes' => 'Mayo', 'categoria' => 'facturado', 'cliente' => 'ET Heinsen', 'servicio' => 'Pullso', 'monto' => 5838, 'estado' => 'Pagado 50%'],
            ['mes' => 'Mayo', 'categoria' => 'facturado', 'cliente' => 'Banreservas', 'servicio' => 'Elearning', 'monto' => 7434, 'estado' => 'Pagado'],
            ['mes' => 'Mayo', 'categoria' => 'facturado', 'cliente' => 'Abitare', 'servicio' => 'Teambuilding', 'monto' => 1500, 'estado' => 'Pagado'],
            ['mes' => 'Junio', 'categoria' => 'facturado', 'cliente' => 'Mallen', 'servicio' => 'Programa para gerentes de distrito', 'monto' => 9989, 'estado' => 'Facturado'],
            ['mes' => 'Julio', 'categoria' => 'facturado', 'cliente' => 'Superintendencia de Bancos', 'servicio' => 'Acompañamiento', 'monto' => 2500, 'estado' => 'Facturado'],
            ['mes' => 'Julio', 'categoria' => 'facturado', 'cliente' => 'Costa Farms', 'servicio' => 'Programa acompañamiento líderes', 'monto' => 15400, 'estado' => 'Facturado'],

            // --- Perdido ---
            ['mes' => 'Mayo', 'categoria' => 'perdido', 'cliente' => 'La Famosa', 'servicio' => 'Programa modular', 'monto' => 17500, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'perdido', 'cliente' => 'Emergent Cold', 'servicio' => 'Skala', 'monto' => 5100, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'perdido', 'cliente' => 'Superintendencia', 'servicio' => 'Programa acompañamiento', 'monto' => 21500, 'estado' => 'Aplazado / perdido'],
            ['mes' => 'Mayo', 'categoria' => 'perdido', 'cliente' => 'Ademi', 'servicio' => 'Gestión del cambio', 'monto' => 42500, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'perdido', 'cliente' => 'Zona Friki', 'servicio' => 'Acompañamiento a líderes', 'monto' => 1850, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'perdido', 'cliente' => 'Helados Bon', 'servicio' => 'Programa modular ventas', 'monto' => 7500, 'estado' => 'Perdido'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Indotel', 'servicio' => 'Teambuilding (presupuesto)', 'monto' => 1800, 'estado' => 'Perdido'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Plusval', 'servicio' => 'Teambuilding 300 personas', 'monto' => 12850, 'estado' => 'Perdido - pedían logística incluida'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Ademi', 'servicio' => 'Elearning', 'monto' => 6300, 'estado' => 'Pausado para próximo año'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Ademi', 'servicio' => 'Teambuilding', 'monto' => 4500, 'estado' => 'Se fueron con otro proveedor'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Sanesto', 'servicio' => 'Taller de liderazgo', 'monto' => 1650, 'estado' => 'Sin respuesta'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Adell', 'servicio' => 'Taller de liderazgo', 'monto' => 1650, 'estado' => 'Sin respuesta'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Laboratorio Óptica', 'servicio' => 'Teambuilding', 'monto' => 1800, 'estado' => 'Perdido'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Bravo', 'servicio' => 'Programa modular líderes de tienda', 'monto' => 12800, 'estado' => 'Perdido por otras prioridades'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'IHGRD', 'servicio' => 'Acompañamiento a líderes', 'monto' => 2500, 'estado' => 'Perdido'],
            ['mes' => 'Junio', 'categoria' => 'perdido', 'cliente' => 'Desing ISA', 'servicio' => 'Teambuilding 500 personas', 'monto' => 19863, 'estado' => 'Perdido por precio'],

            // --- Perdidas adicionales del listado anual ---
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'NYLC', 'servicio' => '—', 'monto' => 2500, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Banco Popular Dominicano', 'servicio' => '—', 'monto' => 5700, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Remax', 'servicio' => '—', 'monto' => 6600, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Banco Ademi', 'servicio' => '—', 'monto' => 5700, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Chevyplan', 'servicio' => '—', 'monto' => 1700, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Brinks', 'servicio' => '—', 'monto' => 2490, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Algiers', 'servicio' => '—', 'monto' => 3300, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Cooperativa Maimón', 'servicio' => 'Programa de ventas', 'monto' => 11750, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Banco Popular Dominicano', 'servicio' => 'Genius', 'monto' => 5000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Ennova', 'servicio' => '—', 'monto' => 18000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'TPG', 'servicio' => '—', 'monto' => 3550, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Melo, Martínez & Contín Abogados', 'servicio' => '—', 'monto' => 12000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'DP World', 'servicio' => '—', 'monto' => 9000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Banco Popular Dominicano', 'servicio' => '—', 'monto' => 13000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Casa Brugal', 'servicio' => '—', 'monto' => 16000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Rocedental', 'servicio' => '—', 'monto' => 3550, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Caribetrans', 'servicio' => '—', 'monto' => 4500, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Scotia GBS', 'servicio' => '—', 'monto' => 2000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Scotia GBS', 'servicio' => '—', 'monto' => 850, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Banco Caribe', 'servicio' => '—', 'monto' => 5130, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Mailboxes', 'servicio' => '—', 'monto' => 1250, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Carol Morgan School', 'servicio' => '—', 'monto' => 1650, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Ministerio de Turismo', 'servicio' => '—', 'monto' => 20000, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Cervecería Nacional Dominicana', 'servicio' => '—', 'monto' => 19950, 'estado' => 'Perdido'],
            ['mes' => 'Otros 2026', 'categoria' => 'perdido', 'cliente' => 'Rannik', 'servicio' => '—', 'monto' => 330, 'estado' => 'Perdido'],

            // --- Pipeline nuevo ---
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'Banreservas', 'servicio' => 'Elearning técnico', 'monto' => 7434, 'estado' => 'Ganado'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'BON', 'servicio' => 'Taller de ventas', 'monto' => 7500, 'estado' => 'Perdido'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'BHD', 'servicio' => 'Elearning técnico', 'monto' => 6700, 'estado' => 'Ganado sin facturar'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'Superintendencia', 'servicio' => 'Proceso gestión humana', 'monto' => 21500, 'estado' => 'Se quedó para el próximo año'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'Laboratorios ALFA', 'servicio' => 'Programa gerentes', 'monto' => 8600, 'estado' => 'En proceso'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'Sanesto', 'servicio' => 'Taller fundamentos liderazgo', 'monto' => 1650, 'estado' => 'Perdido'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'Farmaconal', 'servicio' => 'Cultura del servicio', 'monto' => 28045, 'estado' => 'En proceso'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'Laboratorio Óptica', 'servicio' => 'Teambuilding', 'monto' => 1800, 'estado' => 'Perdido'],
            ['mes' => 'Abril', 'categoria' => 'pipelineNuevo', 'cliente' => 'Bravo', 'servicio' => 'Programa modular líderes de tienda', 'monto' => 12800, 'estado' => 'Perdido por otras prioridades'],

            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Superintendencia de Bancos', 'servicio' => 'Programa modular servicio al cliente', 'monto' => 19200, 'estado' => 'En proceso'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Senasa', 'servicio' => 'Pullso', 'monto' => 13366, 'estado' => 'En proceso'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Senasa', 'servicio' => 'Teambuilding', 'monto' => 4850, 'estado' => 'En proceso'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Superintendencia de Bancos', 'servicio' => 'Teambuilding', 'monto' => 4500, 'estado' => 'Ganado sin facturar'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Hacienda Campo Verde', 'servicio' => 'Acompañamiento a líderes', 'monto' => 1850, 'estado' => 'En proceso'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Adell (firma de abogados)', 'servicio' => 'Teambuilding', 'monto' => 1650, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'DiDA', 'servicio' => 'Pullso', 'monto' => 4800, 'estado' => 'En proceso'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Ademi', 'servicio' => 'Teambuilding', 'monto' => 4500, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'An Uniformes', 'servicio' => 'Programa modular ventas', 'monto' => 7980, 'estado' => 'Aprobado sin facturar'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Laboratorios ALFA', 'servicio' => 'Programa talleres visitador médico - presupuesto', 'monto' => 12500, 'estado' => 'En proceso'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Mallen', 'servicio' => 'Programa actualización MORE', 'monto' => 12500, 'estado' => 'En proceso'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'IHGRD', 'servicio' => 'Acompañamiento a líderes', 'monto' => 2500, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Zona Friki', 'servicio' => 'Acompañamiento a líderes', 'monto' => 1850, 'estado' => 'Perdido'],
            ['mes' => 'Mayo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Abitare', 'servicio' => 'Acompañamiento a líderes', 'monto' => 1650, 'estado' => 'Ganado'],

            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Trane Trade', 'servicio' => 'Teambuilding', 'monto' => 1650, 'estado' => 'Ganado sin facturar'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Coop Osaka', 'servicio' => 'Teambuilding', 'monto' => 1650, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Sheraton', 'servicio' => 'Taller de fundamentos de liderazgo', 'monto' => 1650, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Aduanas', 'servicio' => 'Teambuilding', 'monto' => 1650, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Banco Popular', 'servicio' => 'Programa modular', 'monto' => 26000, 'estado' => 'En proceso - próximo año'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Indotel', 'servicio' => 'Teambuilding', 'monto' => 1800, 'estado' => 'Perdido'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Heinsen', 'servicio' => 'Programa modular', 'monto' => 13454, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'DP World', 'servicio' => 'Taller de IE y toma de decisiones', 'monto' => 4500, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Superintendencia de Bancos', 'servicio' => 'Acompañamiento', 'monto' => 2500, 'estado' => 'Ganado'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Cirsa', 'servicio' => 'Teambuilding', 'monto' => 2960, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Bellamar', 'servicio' => 'Programa liderazgo', 'monto' => 18811, 'estado' => 'En proceso - oferta económica enviada'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'ST Medic', 'servicio' => 'Train the trainer', 'monto' => 4500, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Gym Ingeniería', 'servicio' => 'Consultoría', 'monto' => 8800, 'estado' => 'En proceso'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Desing ISA', 'servicio' => 'Teambuilding 500 personas', 'monto' => 19863, 'estado' => 'Perdido por precio'],
            ['mes' => 'Junio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Plusval', 'servicio' => 'Teambuilding 300 personas', 'monto' => 12850, 'estado' => 'Perdido - logística'],

            // --- Pipeline nuevo julio / marzo ---
            ['mes' => 'Julio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Litter Cesars', 'servicio' => 'Taller de servicio para líderes', 'monto' => 4950, 'estado' => 'En proceso'],
            ['mes' => 'Julio', 'categoria' => 'pipelineNuevo', 'cliente' => 'Mallen', 'servicio' => 'Construcción del perfil ideal', 'monto' => 8500, 'estado' => 'En proceso'],
            ['mes' => 'Marzo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Grupo Mallen', 'servicio' => 'Programa VM', 'monto' => 31500, 'estado' => 'En proceso'],
            ['mes' => 'Marzo', 'categoria' => 'pipelineNuevo', 'cliente' => 'DIF', 'servicio' => 'Cultura del servicio', 'monto' => 15000, 'estado' => 'En proceso'],
            ['mes' => 'Marzo', 'categoria' => 'pipelineNuevo', 'cliente' => 'Banco Caribe', 'servicio' => 'People Analytics', 'monto' => 5000, 'estado' => 'En proceso'],
        ];
    }
}
