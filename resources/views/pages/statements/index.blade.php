{{-- resources/views/pages/statements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Statements')

@section('content')

    <x-monky.page-header icon="📄" title="Statements" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Success flash --}}
        @if(session('success'))
            <div class="p-3 rounded border" style="border-color: var(--success); background-color: color-mix(in srgb, var(--success) 8%, transparent);">
                <p class="text-sm text-success">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('statements.create') }}" class="px-5 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                + NEW STATEMENT
            </a>
            <a href="{{ route('statements.import') }}" class="px-5 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                ↑ IMPORT PDF
            </a>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-2.5">
                    <span class="bullet"></span>
                    <span class="font-medium">ALL STATEMENTS</span>
                </div>
                <span class="badge badge-secondary">{{ $statements->total() }} TOTAL</span>
            </div>

            <div class="bg-accent overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase">Período</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase">Titular</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Entradas</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Salidas</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase">Saldo Final</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden sm:table-cell">Mov.</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden sm:table-cell">PDF</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statements as $st)
                            <tr class="hover:bg-accent transition-colors" style="border-bottom: 1px solid var(--border);">
                                <td class="p-3 whitespace-nowrap">
                                    <a href="{{ route('statements.show', $st) }}" class="font-medium hover:underline">
                                        {{ $st->period }}
                                    </a>
                                </td>
                                <td class="p-3 text-muted-foreground">{{ $st->holder_name }}</td>
                                <td class="p-3 text-right text-success hidden md:table-cell">
                                    $ {{ number_format($st->entradas, 2, ',', '.') }}
                                </td>
                                <td class="p-3 text-right text-destructive hidden md:table-cell">
                                    $ {{ number_format(abs($st->salidas), 2, ',', '.') }}
                                </td>
                                <td class="p-3 text-right font-bold">
                                    $ {{ number_format($st->saldo_final, 2, ',', '.') }}
                                </td>
                                <td class="p-3 text-center hidden sm:table-cell">
                                    <span class="badge badge-secondary">{{ $st->movements_count }}</span>
                                </td>
                                <td class="p-3 text-center hidden sm:table-cell">
                                    @if($st->pdf_path)
                                        <a href="{{ route('statements.pdf', $st) }}" class="text-primary hover:underline text-xs uppercase">⬇ PDF</a>
                                    @else
                                        <span class="text-muted-foreground text-xs">—</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('statements.show', $st) }}" class="px-2 py-1 bg-secondary text-secondary-foreground rounded text-xs">VER</a>
                                        <a href="{{ route('statements.edit', $st) }}" class="px-2 py-1 bg-secondary text-secondary-foreground rounded text-xs">EDIT</a>
                                        <form action="{{ route('statements.destroy', $st) }}" method="POST" onsubmit="return confirm('¿Eliminar este resumen y todos sus movimientos?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-2 py-1 rounded text-xs" style="background-color: color-mix(in srgb, var(--destructive) 15%, transparent); color: var(--destructive);">DEL</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-muted-foreground">
                                    <p class="font-display text-xl mb-2">NO STATEMENTS YET</p>
                                    <p class="text-xs">Create one manually or import a PDF.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($statements->hasPages())
            <div class="flex justify-center">
                {{ $statements->links() }}
            </div>
        @endif

    </div>

@endsection
