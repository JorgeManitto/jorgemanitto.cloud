{{-- resources/views/pages/statements/import.blade.php --}}
@extends('layouts.app')

@section('title', 'Import PDF')

@section('content')

    <x-monky.page-header icon="↑" title="Import PDF" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Back --}}
        <div>
            <a href="{{ route('statements.index') }}" class="px-4 py-2 bg-secondary text-secondary-foreground rounded text-sm uppercase">← BACK</a>
        </div>

        @if($errors->has('pdf'))
            <div class="p-3 rounded border" style="border-color: var(--destructive); background-color: color-mix(in srgb, var(--destructive) 8%, transparent);">
                <p class="text-sm text-destructive">{{ $errors->first('pdf') }}</p>
            </div>
        @endif

        <div class="card">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-2.5">
                    <span class="bullet"></span>
                    <span class="font-medium">IMPORT FROM MERCADO PAGO PDF</span>
                </div>
            </div>
            <div class="bg-accent p-4">
                <form action="{{ route('statements.import.process') }}" method="POST" enctype="multipart/form-data" id="import-form">
                    @csrf

                    <label for="import-pdf" id="import-zone"
                        class="flex flex-col items-center justify-center gap-3 p-10 rounded-lg border-2 border-dashed cursor-pointer transition-all"
                        style="border-color: var(--border);">
                        <div class="size-16 rounded-lg bg-primary flex items-center justify-center text-3xl text-primary-foreground">↑</div>
                        <p class="font-display text-2xl" id="import-label">DROP MERCADO PAGO PDF HERE</p>
                        <p class="text-xs text-muted-foreground">The system will extract all data automatically (.pdf, max 5MB)</p>
                        <input type="file" name="pdf" id="import-pdf" accept=".pdf" class="hidden" required>
                    </label>

                    <div class="flex items-center gap-3 mt-4">
                        <button type="submit" id="import-btn" disabled
                            class="px-6 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase disabled:opacity-30 transition-opacity">
                            IMPORT & SAVE
                        </button>
                        <p class="text-xs text-muted-foreground">This will parse the PDF, create the statement and all movements automatically.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
(function () {
    const zone  = document.getElementById('import-zone');
    const input = document.getElementById('import-pdf');
    const label = document.getElementById('import-label');
    const btn   = document.getElementById('import-btn');
    const form  = document.getElementById('import-form');

    function handle(file) {
        if (!file || file.type !== 'application/pdf') return;
        const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
        label.textContent = file.name;
        zone.style.borderColor = 'var(--success)';
        btn.disabled = false;
    }

    input.addEventListener('change', () => handle(input.files[0]));
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = 'var(--primary)'; });
    zone.addEventListener('dragleave', () => { zone.style.borderColor = input.files.length ? 'var(--success)' : 'var(--border)'; });
    zone.addEventListener('drop', e => { e.preventDefault(); handle(e.dataTransfer.files[0]); });
    form.addEventListener('submit', () => { btn.disabled = true; btn.textContent = 'PROCESSING...'; });
})();
</script>
@endpush
