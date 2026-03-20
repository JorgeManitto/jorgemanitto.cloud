{{-- resources/views/components/monky/rebels-ranking.blade.php --}}
@props([
    'rebels'   => [],    {{-- Collection of rebel data --}}
    'newCount' => 0,
])

<div class="card">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center gap-2.5">
            <span class="bullet"></span>
            <span class="font-medium">REBELS RANKING</span>
        </div>
        @if($newCount > 0)
            <span class="badge badge-warning">{{ $newCount }} NEW</span>
        @endif
    </div>
    <div class="bg-accent p-4 space-y-4">
        @foreach($rebels as $index => $rebel)
            <x-monky.rebel-rank-item
                :rank="$index + 1"
                :name="$rebel['name']"
                :handle="$rebel['handle']"
                :avatar="$rebel['avatar']"
                :points="$rebel['points']"
                :streak="$rebel['streak'] ?? null"
                :featured="$index === 0"
            />
        @endforeach
    </div>
</div>
