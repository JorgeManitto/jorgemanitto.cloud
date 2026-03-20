{{-- resources/views/components/monky/chart-section.blade.php --}}
@props([
    'activeRange' => 'week',  {{-- week | month | year --}}
])

<div>
    <div class="flex items-center justify-between mb-4">
        {{-- Range toggles --}}
        <div class="flex gap-2">
            @foreach(['week', 'month', 'year'] as $range)
                <button
                    @class([
                        'px-4 py-2 rounded text-sm',
                        'bg-primary text-primary-foreground'     => $activeRange === $range,
                        'bg-secondary text-secondary-foreground' => $activeRange !== $range,
                    ])
                    data-range="{{ $range }}"
                >
                    {{ strtoupper($range) }}
                </button>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-6">
            @php
                $legends = [
                    ['color' => 'var(--chart-1)', 'label' => 'SPENDINGS'],
                    ['color' => 'var(--chart-2)', 'label' => 'SALES'],
                    ['color' => 'var(--chart-3)', 'label' => 'COFFEE'],
                ];
            @endphp
            @foreach($legends as $legend)
                <div class="flex items-center gap-2">
                    <span class="bullet" style="background-color: {{ $legend['color'] }}; transform: rotate(45deg);"></span>
                    <span class="text-sm text-muted-foreground">{{ $legend['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Chart area — reemplazar con Chart.js / ApexCharts / etc. --}}
    <div class="chart-container" id="main-chart">
        <p class="text-center">Chart visualization would be rendered here with a charting library</p>
    </div>
</div>
