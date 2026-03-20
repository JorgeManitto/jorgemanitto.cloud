{{-- resources/views/pages/statements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Statement — ' . $statement->period)

@section('content')

    <x-monky.page-header icon="📄" title="Statement Detail" :lastUpdated="$statement->updated_at->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Success flash --}}
        @if(session('success'))
            <div class="p-3 rounded border" style="border-color: var(--success); background-color: color-mix(in srgb, var(--success) 8%, transparent);">
                <p class="text-sm text-success">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('statements.index') }}" class="px-4 py-2 bg-secondary text-secondary-foreground rounded text-sm uppercase">← BACK</a>
            <a href="{{ route('statements.edit', $statement) }}" class="px-4 py-2 bg-primary text-primary-foreground rounded text-sm uppercase">EDIT</a>
            @if($statement->pdf_path)
                <a href="{{ route('statements.pdf', $statement) }}" class="px-4 py-2 bg-secondary text-secondary-foreground rounded text-sm uppercase">⬇ PDF</a>
            @endif
            <form action="{{ route('statements.destroy', $statement) }}" method="POST" class="ml-auto" onsubmit="return confirm('¿Eliminar este resumen?')">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 rounded text-sm uppercase" style="background-color: color-mix(in srgb, var(--destructive) 15%, transparent); color: var(--destructive);">
                    DELETE
                </button>
            </form>
        </div>

        {{-- Account Info --}}
        <div class="card">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-2.5">
                    <span class="bullet"></span>
                    <span class="font-medium">ACCOUNT INFO</span>
                </div>
                <span class="badge badge-secondary">MERCADO PAGO</span>
            </div>
            <div class="bg-accent p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-muted-foreground block text-xs mb-1">TITULAR</span>
                        <span class="font-display text-lg">{{ strtoupper($statement->holder_name) }}</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground block text-xs mb-1">CUIT / CUIL</span>
                        <span class="font-medium">{{ $statement->cuit ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground block text-xs mb-1">CVU</span>
                        <span class="font-medium text-xs">{{ $statement->cvu ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground block text-xs mb-1">PERÍODO</span>
                        <span class="font-medium">{{ $statement->period }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $fmt = fn($v) => '$ ' . number_format(abs($v), 2, ',', '.');
            $diff = $statement->saldo_final - $statement->saldo_inicial;
            $pct  = $statement->saldo_inicial != 0
                ? round(($diff / abs($statement->saldo_inicial)) * 100, 1)
                : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-monky.stat-card
                label="SALDO INICIAL"
                icon="💰"
                :value="$fmt($statement->saldo_inicial)"
                description="INICIO DEL PERÍODO"
            />
            <x-monky.stat-card
                label="ENTRADAS"
                icon="📥"
                :value="$fmt($statement->entradas)"
                description="INGRESOS TOTALES"
                trend="up"
            />
            <x-monky.stat-card
                label="SALIDAS"
                icon="📤"
                :value="$fmt($statement->salidas)"
                description="EGRESOS TOTALES"
                trend="down"
            />
            <x-monky.stat-card
                label="SALDO FINAL"
                icon="🏦"
                :value="$fmt($statement->saldo_final)"
                :description="($diff >= 0 ? '+' : '') . number_format($pct, 1, ',', '.') . '% VS INICIO'"
                :trend="$diff >= 0 ? 'up' : 'down'"
            />
        </div>

        {{-- Categories Breakdown --}}
        @php
            $cats = $statement->movements
                ->where('type', 'expense')
                ->groupBy('category')
                ->map(fn($items) => ['count' => $items->count(), 'total' => $items->sum('amount')])
                ->sortBy('total');

            $totalExp = $cats->sum('total');

            $colors = [
                'Delivery / Comida' => 'var(--chart-3)',
                'Transferencia'     => 'var(--chart-1)',
                'Suscripción'       => 'var(--chart-2)',
                'Servicios'         => 'var(--warning)',
                'Entretenimiento'   => '#e879f9',
                'Microsoft'         => '#60a5fa',
                'Compras Online'    => 'var(--destructive)',
                'Créditos'          => '#f472b6',
                'Otros'             => 'var(--muted-foreground)',
            ];
        @endphp

        @if($cats->isNotEmpty())
        <div class="card">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-2.5">
                    <span class="bullet bullet-warning"></span>
                    <span class="font-medium">EXPENSES BY CATEGORY</span>
                </div>
                <span class="badge badge-secondary">{{ $cats->count() }} CATEGORIES</span>
            </div>
            <div class="bg-accent p-4 space-y-3">
                @foreach($cats as $cat => $info)
                    @php
                        $pctCat = $totalExp != 0 ? round(($info['total'] / $totalExp) * 100, 1) : 0;
                        $clr    = $colors[$cat] ?? 'var(--muted-foreground)';
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full" style="background-color: {{ $clr }};"></span>
                                <span class="text-sm font-medium">{{ strtoupper($cat) }}</span>
                                <span class="text-xs text-muted-foreground">({{ $info['count'] }})</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-muted-foreground">{{ $pctCat }}%</span>
                                <span class="text-sm font-bold" style="color: {{ $clr }};">{{ $fmt($info['total']) }}</span>
                            </div>
                        </div>
                        <div class="w-full h-1.5 rounded-full" style="background-color: var(--secondary);">
                            <div class="h-full rounded-full" style="width: {{ $pctCat }}%; background-color: {{ $clr }};"></div>
                        </div>
                    </div>
                @endforeach
                <div class="flex items-center justify-between pt-3 mt-3" style="border-top: 1px solid var(--border);">
                    <span class="font-display text-xl">TOTAL EGRESOS</span>
                    <span class="font-display text-xl text-destructive">{{ $fmt($totalExp) }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Movements Table --}}
        <div class="card">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-2.5">
                    <span class="bullet"></span>
                    <span class="font-medium">MOVEMENTS</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-secondary">{{ $statement->movements->count() }} TOTAL</span>
                </div>
            </div>

            {{-- Filters --}}
            <div class="px-4 pb-2 flex flex-wrap items-center gap-2">
                <button class="filter-btn px-3 py-1.5 rounded text-xs font-medium uppercase bg-primary text-primary-foreground" data-filter="all">ALL</button>
                <button class="filter-btn px-3 py-1.5 rounded text-xs font-medium uppercase bg-secondary text-secondary-foreground" data-filter="income">INCOME</button>
                <button class="filter-btn px-3 py-1.5 rounded text-xs font-medium uppercase bg-secondary text-secondary-foreground" data-filter="expense">EXPENSES</button>
                <div class="ml-auto">
                    <input type="text" id="mov-search" placeholder="Search..."
                        class="px-3 py-1.5 rounded text-xs bg-secondary text-secondary-foreground outline-none"
                        style="border:1px solid var(--border); min-width:180px;">
                </div>
            </div>

            <div class="bg-accent overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase">Fecha</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase">Descripción</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Categoría</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase hidden lg:table-cell">ID Operación</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase">Monto</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="mov-body">
                        @foreach($statement->movements as $mov)
                            <tr class="mov-row hover:bg-accent transition-colors"
                                data-type="{{ $mov->type }}"
                                data-search="{{ Str::lower($mov->description . ' ' . $mov->category) }}"
                                style="border-bottom: 1px solid var(--border);">
                                <td class="p-3 whitespace-nowrap text-muted-foreground">{{ $mov->date->format('d-m-Y') }}</td>
                                <td class="p-3">
                                    <div class="max-w-xs truncate" title="{{ $mov->description }}">{{ $mov->description }}</div>
                                </td>
                                <td class="p-3 hidden md:table-cell">
                                    <span class="badge badge-secondary text-xs">{{ $mov->category ?: '—' }}</span>
                                </td>
                                <td class="p-3 text-muted-foreground hidden lg:table-cell font-mono text-xs">{{ $mov->operation_id }}</td>
                                <td class="p-3 text-right font-bold whitespace-nowrap {{ $mov->type === 'income' ? 'text-success' : 'text-destructive' }}">
                                    {{ $mov->type === 'income' ? '+' : '-' }}{{ $fmt($mov->amount) }}
                                </td>
                                <td class="p-3 text-right text-muted-foreground hidden md:table-cell whitespace-nowrap">{{ $fmt($mov->balance) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($statement->movements->isEmpty())
                    <p class="text-center text-muted-foreground text-xs py-8">No movements recorded.</p>
                @endif
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
(function () {
    const rows       = document.querySelectorAll('.mov-row');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('mov-search');
    let activeFilter = 'all', searchTerm = '';

    function apply() {
        rows.forEach(r => {
            const mt = activeFilter === 'all' || r.dataset.type === activeFilter;
            const ms = !searchTerm || r.dataset.search.includes(searchTerm);
            r.style.display = mt && ms ? '' : 'none';
        });
    }

    filterBtns.forEach(b => b.addEventListener('click', () => {
        filterBtns.forEach(x => { x.classList.remove('bg-primary','text-primary-foreground'); x.classList.add('bg-secondary','text-secondary-foreground'); });
        b.classList.remove('bg-secondary','text-secondary-foreground');
        b.classList.add('bg-primary','text-primary-foreground');
        activeFilter = b.dataset.filter;
        apply();
    }));

    searchInput.addEventListener('input', e => { searchTerm = e.target.value.toLowerCase(); apply(); });
})();
</script>
@endpush
