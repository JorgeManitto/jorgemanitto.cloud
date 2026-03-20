<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatementRequest;
use App\Models\Movement;
use App\Models\Statement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;

class StatementController extends Controller
{
    /* ==================================================================
     *  CRUD
     * ================================================================ */

    /**
     * Listado de resúmenes.
     */
    public function index()
    {
        $statements = Statement::withCount('movements')
            ->latest()
            ->paginate(10);

        return view('pages.statements.index', compact('statements'));
    }

    /**
     * Formulario de creación manual.
     */
    public function create()
    {
        return view('pages.statements.create');
    }

    /**
     * Guardar resumen + movimientos.
     */
    public function store(StatementRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {

            // PDF opcional
            $pdfPath = null;
            if ($request->hasFile('pdf')) {
                $pdfPath = $request->file('pdf')->store('statements', 'public');
            }

            $statement = Statement::create([
                'holder_name'   => $data['holder_name'],
                'cvu'           => $data['cvu'] ?? null,
                'cuit'          => $data['cuit'] ?? null,
                'period'        => $data['period'],
                'saldo_inicial' => $data['saldo_inicial'],
                'entradas'      => $data['entradas'],
                'salidas'       => $data['salidas'],
                'saldo_final'   => $data['saldo_final'],
                'pdf_path'      => $pdfPath,
            ]);

            // Movimientos
            if (!empty($data['movements'])) {
                foreach ($data['movements'] as $mov) {
                    $statement->movements()->create($mov);
                }
            }

            return redirect()
                ->route('statements.show', $statement)
                ->with('success', 'Resumen creado correctamente.');
        });
    }

    /**
     * Detalle de un resumen con sus movimientos.
     */
    public function show(Statement $statement)
    {
        $statement->load('movements');

        return view('pages.statements.show', compact('statement'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Statement $statement)
    {
        $statement->load('movements');

        return view('pages.statements.edit', compact('statement'));
    }

    /**
     * Actualizar resumen + movimientos.
     */
    public function update(StatementRequest $request, Statement $statement)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request, $statement) {

            // PDF — reemplazar si viene uno nuevo
            if ($request->hasFile('pdf')) {
                if ($statement->pdf_path) {
                    Storage::disk('public')->delete($statement->pdf_path);
                }
                $data['pdf_path'] = $request->file('pdf')->store('statements', 'public');
            }

            $statement->update(collect($data)->except('movements', 'pdf')->toArray());

            // Sincronizar movimientos: eliminar todos y recrear
            $statement->movements()->delete();

            if (!empty($data['movements'])) {
                foreach ($data['movements'] as $mov) {
                    $statement->movements()->create($mov);
                }
            }

            return redirect()
                ->route('statements.show', $statement)
                ->with('success', 'Resumen actualizado correctamente.');
        });
    }

    /**
     * Eliminar resumen + PDF + movimientos.
     */
    public function destroy(Statement $statement)
    {
        if ($statement->pdf_path) {
            Storage::disk('public')->delete($statement->pdf_path);
        }

        $statement->delete(); // cascadeOnDelete elimina movements

        return redirect()
            ->route('statements.index')
            ->with('success', 'Resumen eliminado correctamente.');
    }

    /* ==================================================================
     *  IMPORT FROM PDF
     * ================================================================ */

    /**
     * Vista de importación.
     */
    public function importForm()
    {
        return view('pages.statements.import');
    }

    /**
     * Procesar PDF y redirigir a create con datos pre-cargados.
     */
    public function import(Request $request)
    {
        $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        // Guardar PDF
        $pdfPath = $request->file('pdf')->store('statements', 'public');
        $fullPath = Storage::disk('public')->path($pdfPath);

        try {
            $parsed = $this->parsePdf($fullPath);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($pdfPath);
            return back()->withErrors(['pdf' => 'No se pudo procesar el PDF: ' . $e->getMessage()]);
        }

        // Guardar todo en DB directamente
        $statement = DB::transaction(function () use ($parsed, $pdfPath) {

            $statement = Statement::create([
                'holder_name'   => $parsed['header']['name'],
                'cvu'           => $parsed['header']['cvu'],
                'cuit'          => $parsed['header']['cuit'],
                'period'        => $parsed['header']['period'],
                'saldo_inicial' => $parsed['summary']['saldo_inicial'],
                'entradas'      => $parsed['summary']['entradas'],
                'salidas'       => $parsed['summary']['salidas'],
                'saldo_final'   => $parsed['summary']['saldo_final'],
                'pdf_path'      => $pdfPath,
            ]);

            foreach ($parsed['movements'] as $mov) {
                $statement->movements()->create($mov);
            }

            return $statement;
        });

        return redirect()
            ->route('statements.show', $statement)
            ->with('success', 'PDF importado correctamente — ' . count($parsed['movements']) . ' movimientos extraídos.');
    }

    /**
     * Descargar el PDF de un statement.
     */
    public function downloadPdf(Statement $statement)
    {
        if (!$statement->pdf_path || !Storage::disk('public')->exists($statement->pdf_path)) {
            abort(404, 'PDF no encontrado.');
        }

        return Storage::disk('public')->download($statement->pdf_path, 'resumen_' . Str::slug($statement->period) . '.pdf');
    }

    /* ==================================================================
     *  PDF PARSING
     * ================================================================ */

    private function parsePdf(string $path): array
    {
        $text = Pdf::getText($path);

        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);

        return [
            'header'    => $this->extractHeader($text),
            'summary'   => $this->extractSummary($text),
            'movements' => $this->extractMovements($text),
        ];
    }

    private function extractHeader(string $text): array
    {
        $header = ['name' => '', 'cvu' => '', 'cuit' => '', 'period' => ''];

        if (preg_match('/RESUMEN DE CUENTA\s*\n(.+)/i', $text, $m)) {
            $header['name'] = trim($m[1]);
        }
        if (preg_match('/CVU:\s*([\d]+)/i', $text, $m)) {
            $header['cvu'] = $m[1];
        }
        if (preg_match('/CUIT\s*\/?\s*CUIL:\s*([\d\-]+)/i', $text, $m)) {
            $header['cuit'] = $m[1];
        }
        if (preg_match('/Periodo:\s*(.+)/i', $text, $m)) {
            $header['period'] = trim($m[1]);
        }

        return $header;
    }

    private function extractSummary(string $text): array
    {
        $summary = ['saldo_inicial' => 0, 'entradas' => 0, 'salidas' => 0, 'saldo_final' => 0];

        $patterns = [
            'saldo_inicial' => '/Saldo\s*inicial:\s*\$\s*([\d.,\-]+)/i',
            'entradas'      => '/Entradas:\s*\$\s*([\d.,\-]+)/i',
            'salidas'       => '/Salidas:\s*\$\s*([\d.,\-]+)/i',
            'saldo_final'   => '/Saldo\s*final:\s*\$\s*([\d.,\-]+)/i',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $summary[$key] = $this->parseAmount($m[1]);
            }
        }

        return $summary;
    }

    private function extractMovements(string $text): array
    {
        $movements = [];
        $pattern = '/(\d{2}-\d{2}-\d{4})\s+(.+?)\s+(\d{12,15})\s+\$\s*([\d.,\-]+)\s+\$\s*([\d.,\-]+)/';

        $lines = explode("\n", $text);
        $collapsed = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (preg_match('/^\d{2}-\d{2}-\d{4}/', $line)) {
                $collapsed .= "\n" . $line;
            } else {
                $collapsed .= ' ' . $line;
            }
        }

        preg_match_all($pattern, $collapsed, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $amount      = $this->parseAmount($m[4]);
            $description = trim(preg_replace('/\s+/', ' ', $m[2]));

            // Convertir DD-MM-YYYY a YYYY-MM-DD para la BD
            $dateParts = explode('-', $m[1]);
            $dateForDb = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];

            $movements[] = [
                'date'         => $dateForDb,
                'description'  => $description,
                'operation_id' => $m[3],
                'amount'       => $amount,
                'balance'      => $this->parseAmount($m[5]),
                'type'         => $amount >= 0 ? 'income' : 'expense',
                'category'     => $this->categorize($description),
            ];
        }

        return $movements;
    }

    /* ----- Categorización ------------------------------------------------ */

    private function categorize(string $description): string
    {
        $desc = Str::lower($description);

        $map = [
            'Delivery / Comida' => ['pedidosya', 'rappi', 'didi food'],
            'Transferencia'     => ['transferencia'],
            'Suscripción'       => ['subscription', 'suscripción', 'suscripcion'],
            'Servicios'         => ['edenor', 'edesur', 'metrogas', 'claro', 'movistar', 'personal', 'arca', 'pago de servicio'],
            'Entretenimiento'   => ['twitch', 'steam', 'crunchyro', 'netflix', 'spotify', 'disney'],
            'Microsoft'         => ['microsoft'],
            'Compras Online'    => ['mercado libre', 'compra mercado'],
            'Créditos'          => ['créditos de mercado pago', 'creditos de mercado pago', 'pago de cuota', 'pago anticipado'],
        ];

        foreach ($map as $category => $keywords) {
            foreach ($keywords as $kw) {
                if (Str::contains($desc, $kw)) {
                    return $category;
                }
            }
        }

        return 'Otros';
    }

    private function parseAmount(string $raw): float
    {
        $raw = str_replace(' ', '', $raw);
        $raw = str_replace('.', '', $raw);
        $raw = str_replace(',', '.', $raw);

        return (float) $raw;
    }
}