{{-- resources/views/pages/statements/_form.blade.php --}}
@php
    $isEdit     = isset($statement) && $statement->exists;
    $categories = ['Delivery / Comida','Transferencia','Suscripción','Servicios','Entretenimiento','Microsoft','Compras Online','Créditos','Otros'];

    // Preparar movimientos existentes para JS
    $existingMovements = [];
    if ($isEdit) {
        $existingMovements = $statement->movements->map(function ($m) {
            return [
                'date'         => $m->date->format('Y-m-d'),
                'description'  => $m->description,
                'operation_id' => $m->operation_id ?? '',
                'amount'       => (float) $m->amount,
                'balance'      => (float) $m->balance,
                'type'         => $m->type,
                'category'     => $m->category ?? '',
            ];
        })->values()->toArray();
    }

    // Helper formato AR
    $fmtAr = function ($value) {
        $v = (float) ($value ?? 0);
        $sign = $v < 0 ? '-' : '';
        return $sign . '$ ' . number_format(abs($v), 2, ',', '.');
    };
@endphp

{{-- Validation Errors --}}
@if($errors->any())
    <div class="p-3 rounded border mb-6" style="border-color: var(--destructive); background-color: color-mix(in srgb, var(--destructive) 8%, transparent);">
        <ul class="text-sm text-destructive space-y-1">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ $isEdit ? route('statements.update', $statement) : route('statements.store') }}"
    method="POST"
    enctype="multipart/form-data"
    id="statement-form"
>
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- ============================================================ --}}
    {{-- ACCOUNT INFO                                                  --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-2.5">
                <span class="bullet"></span>
                <span class="font-medium">ACCOUNT INFO</span>
            </div>
        </div>
        <div class="bg-accent p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-muted-foreground uppercase mb-1">Titular *</label>
                <input type="text" name="holder_name"
                    value="{{ old('holder_name', $statement->holder_name ?? '') }}"
                    class="w-full px-3 py-2 rounded text-sm bg-secondary text-secondary-foreground outline-none"
                    style="border: 1px solid var(--border);"
                    required>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground uppercase mb-1">Período *</label>
                <input type="text" name="period"
                    value="{{ old('period', $statement->period ?? '') }}"
                    placeholder="Del 1 al 28 de febrero de 2026"
                    class="w-full px-3 py-2 rounded text-sm bg-secondary text-secondary-foreground outline-none"
                    style="border: 1px solid var(--border);"
                    required>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground uppercase mb-1">CVU</label>
                <input type="text" name="cvu"
                    value="{{ old('cvu', $statement->cvu ?? '') }}"
                    class="w-full px-3 py-2 rounded text-sm bg-secondary text-secondary-foreground outline-none"
                    style="border: 1px solid var(--border);">
            </div>
            <div>
                <label class="block text-xs text-muted-foreground uppercase mb-1">CUIT / CUIL</label>
                <input type="text" name="cuit"
                    value="{{ old('cuit', $statement->cuit ?? '') }}"
                    class="w-full px-3 py-2 rounded text-sm bg-secondary text-secondary-foreground outline-none"
                    style="border: 1px solid var(--border);">
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- BALANCES (formato argentino $ 1.234.567,89)                   --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-2.5">
                <span class="bullet bullet-success"></span>
                <span class="font-medium">BALANCES</span>
            </div>
        </div>
        <div class="bg-accent p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['saldo_inicial', 'Saldo Inicial'],
                ['entradas',      'Entradas'],
                ['salidas',       'Salidas'],
                ['saldo_final',   'Saldo Final'],
            ] as [$field, $label])
                @php $rawVal = (float) old($field, $statement->$field ?? 0); @endphp
                <div>
                    <label class="block text-xs text-muted-foreground uppercase mb-1">{{ $label }} *</label>
                    <input type="text"
                        class="money-display w-full px-3 py-2 rounded text-sm bg-secondary text-secondary-foreground outline-none"
                        style="border: 1px solid var(--border);"
                        data-target="{{ $field }}"
                        value="{{ $fmtAr($rawVal) }}"
                        placeholder="$ 0,00"
                        required>
                    <input type="hidden" name="{{ $field }}" id="hidden-{{ $field }}" value="{{ $rawVal }}">
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- PDF UPLOAD                                                    --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-2.5">
                <span class="bullet"></span>
                <span class="font-medium">PDF</span>
            </div>
            @if($isEdit && $statement->pdf_path)
                <a href="{{ route('statements.pdf', $statement) }}" class="text-xs text-primary hover:underline uppercase">⬇ PDF Actual</a>
            @endif
        </div>
        <div class="bg-accent p-4">
            <label for="pdf-upload" class="flex flex-col items-center justify-center gap-2 p-6 rounded-lg border-2 border-dashed cursor-pointer transition-all" style="border-color: var(--border);" id="pdf-drop-zone">
                <span class="text-2xl">📄</span>
                <span class="font-display text-lg" id="pdf-label">
                    {{ $isEdit && $statement->pdf_path ? 'REPLACE PDF (OPTIONAL)' : 'DROP PDF HERE (OPTIONAL)' }}
                </span>
                <span class="text-xs text-muted-foreground">.pdf, max 5MB</span>
                <input type="file" name="pdf" id="pdf-upload" accept=".pdf" class="hidden">
            </label>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MOVEMENTS                                                     --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-2.5">
                <span class="bullet bullet-warning"></span>
                <span class="font-medium">MOVEMENTS</span>
            </div>
            <button type="button" id="add-movement" class="px-3 py-1.5 bg-primary text-primary-foreground rounded text-xs font-medium uppercase">
                + ADD ROW
            </button>
        </div>
        <div class="bg-accent p-4 overflow-x-auto">
            <table class="w-full text-sm" id="movements-table">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <th class="text-left p-2 text-xs text-muted-foreground uppercase" style="min-width:130px;">Fecha *</th>
                        <th class="text-left p-2 text-xs text-muted-foreground uppercase" style="min-width:180px;">Descripción *</th>
                        <th class="text-left p-2 text-xs text-muted-foreground uppercase" style="min-width:130px;">ID Operación</th>
                        <th class="text-right p-2 text-xs text-muted-foreground uppercase" style="min-width:150px;">Monto *</th>
                        <th class="text-right p-2 text-xs text-muted-foreground uppercase" style="min-width:150px;">Saldo *</th>
                        <th class="text-center p-2 text-xs text-muted-foreground uppercase" style="min-width:100px;">Tipo *</th>
                        <th class="text-center p-2 text-xs text-muted-foreground uppercase" style="min-width:150px;">Categoría</th>
                        <th class="p-2" style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody id="movements-body"></tbody>
            </table>

            <p id="no-movements-msg" class="text-center text-muted-foreground text-xs py-6">
                No movements yet. Click "+ ADD ROW" to start.
            </p>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SUBMIT                                                        --}}
    {{-- ============================================================ --}}
    <div class="flex items-center gap-3">
        <button type="submit" class="px-6 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
            {{ $isEdit ? 'UPDATE STATEMENT' : 'SAVE STATEMENT' }}
        </button>
        <a href="{{ route('statements.index') }}" class="px-6 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
            CANCEL
        </a>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ==================================================================
     *  MONEY HELPERS — formato argentino $ 1.234.567,89
     * ================================================================ */

    function formatMoney(value) {
        var n = parseFloat(value) || 0;
        var isNeg = n < 0;
        var abs = Math.abs(n).toFixed(2);
        var parts = abs.split('.');
        var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return (isNeg ? '-' : '') + '$ ' + intPart + ',' + parts[1];
    }

    function parseMoney(str) {
        if (typeof str === 'number') return str;
        if (typeof str !== 'string') return 0;
        var clean = str.replace(/\$/g, '').replace(/\s/g, '');
        // Detectar formato AR: si tiene punto Y coma, los puntos son miles
        if (clean.indexOf('.') !== -1 && clean.indexOf(',') !== -1) {
            clean = clean.replace(/\./g, '');  // quitar puntos de miles
            clean = clean.replace(',', '.');   // coma decimal → punto
        } else if (clean.indexOf(',') !== -1 && clean.indexOf('.') === -1) {
            // Solo coma → es decimal
            clean = clean.replace(',', '.');
        }
        return parseFloat(clean) || 0;
    }

    function bindMoneyInput(input, hidden) {
        input.addEventListener('focus', function () {
            var val = parseMoney(input.value);
            input.value = val !== 0 ? val.toString().replace('.', ',') : '';
            input.select();
        });

        input.addEventListener('blur', function () {
            var val = parseMoney(input.value);
            hidden.value = val;
            input.value = val !== 0 ? formatMoney(val) : '';
        });

        input.addEventListener('paste', function () {
            setTimeout(function () {
                var val = parseMoney(input.value);
                hidden.value = val;
                input.value = val !== 0 ? formatMoney(val) : '';
            }, 10);
        });
    }

    /* ---- Init balance inputs ---- */
    document.querySelectorAll('.money-display').forEach(function (input) {
        var targetName = input.getAttribute('data-target');
        var hidden = document.getElementById('hidden-' + targetName);
        bindMoneyInput(input, hidden);
    });

    /* ==================================================================
     *  MOVEMENTS TABLE
     * ================================================================ */

    var categories = @json($categories);
    var tbody      = document.getElementById('movements-body');
    var addBtn     = document.getElementById('add-movement');
    var noMsg      = document.getElementById('no-movements-msg');
    var rowIndex   = 0;

    var cls = 'w-full px-2 py-1.5 rounded text-xs bg-secondary text-secondary-foreground outline-none';
    var stl = 'border:1px solid var(--border);';

    function toggleNoMsg() {
        noMsg.style.display = tbody.children.length > 0 ? 'none' : 'block';
    }

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML.replace(/"/g, '&quot;');
    }

    function catOptions(selected) {
        var h = '<option value="">—</option>';
        for (var c = 0; c < categories.length; c++) {
            h += '<option value="' + esc(categories[c]) + '"' + (categories[c] === selected ? ' selected' : '') + '>' + esc(categories[c]) + '</option>';
        }
        return h;
    }

    function addRow(data) {
        data = data || {};
        var i = rowIndex++;

        var tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.className = 'movement-row';

        var amountVal  = parseFloat(data.amount)  || 0;
        var balanceVal = parseFloat(data.balance)  || 0;

        tr.innerHTML =
            '<td class="p-1"><input type="date" name="movements[' + i + '][date]" value="' + esc(data.date) + '" required class="' + cls + '" style="' + stl + '"></td>' +
            '<td class="p-1"><input type="text" name="movements[' + i + '][description]" value="' + esc(data.description) + '" required class="' + cls + '" style="' + stl + '"></td>' +
            '<td class="p-1"><input type="text" name="movements[' + i + '][operation_id]" value="' + esc(data.operation_id) + '" class="' + cls + '" style="' + stl + '"></td>' +
            '<td class="p-1">' +
                '<input type="text" class="mov-money ' + cls + ' text-right" style="' + stl + '" data-hidden="mov-amount-' + i + '" value="' + (amountVal !== 0 ? formatMoney(amountVal) : '') + '" placeholder="$ 0,00">' +
                '<input type="hidden" name="movements[' + i + '][amount]" id="mov-amount-' + i + '" value="' + amountVal + '">' +
            '</td>' +
            '<td class="p-1">' +
                '<input type="text" class="mov-money ' + cls + ' text-right" style="' + stl + '" data-hidden="mov-balance-' + i + '" value="' + (balanceVal !== 0 ? formatMoney(balanceVal) : '') + '" placeholder="$ 0,00">' +
                '<input type="hidden" name="movements[' + i + '][balance]" id="mov-balance-' + i + '" value="' + balanceVal + '">' +
            '</td>' +
            '<td class="p-1">' +
                '<select name="movements[' + i + '][type]" required class="' + cls + '" style="' + stl + '">' +
                    '<option value="expense"' + ((data.type || 'expense') === 'expense' ? ' selected' : '') + '>Expense</option>' +
                    '<option value="income"' + (data.type === 'income' ? ' selected' : '') + '>Income</option>' +
                '</select>' +
            '</td>' +
            '<td class="p-1">' +
                '<select name="movements[' + i + '][category]" class="' + cls + '" style="' + stl + '">' + catOptions(data.category || '') + '</select>' +
            '</td>' +
            '<td class="p-1 text-center">' +
                '<button type="button" class="remove-row px-2 py-1 rounded text-xs" style="color:var(--destructive);" title="Remove">✕</button>' +
            '</td>';

        tbody.appendChild(tr);

        // Bind money inputs en esta fila
        tr.querySelectorAll('.mov-money').forEach(function (input) {
            var hiddenId = input.getAttribute('data-hidden');
            var hidden = document.getElementById(hiddenId);
            bindMoneyInput(input, hidden);
        });

        // Bind delete
        tr.querySelector('.remove-row').addEventListener('click', function () {
            tr.remove();
            toggleNoMsg();
        });

        toggleNoMsg();
    }

    // Botón ADD ROW
    addBtn.addEventListener('click', function () {
        addRow();
        var last = tbody.lastElementChild;
        if (last) {
            last.scrollIntoView({ behavior: 'smooth', block: 'center' });
            var firstInput = last.querySelector('input[type="date"]');
            if (firstInput) firstInput.focus();
        }
    });

    // Cargar existentes
    var existing = @json($existingMovements);
    for (var e = 0; e < existing.length; e++) {
        addRow(existing[e]);
    }
    toggleNoMsg();

    /* ==================================================================
     *  PDF DROP ZONE
     * ================================================================ */

    var pdfInput = document.getElementById('pdf-upload');
    var pdfLabel = document.getElementById('pdf-label');
    var pdfZone  = document.getElementById('pdf-drop-zone');

    pdfInput.addEventListener('change', function () {
        if (pdfInput.files[0]) {
            pdfLabel.textContent = pdfInput.files[0].name;
            pdfZone.style.borderColor = 'var(--success)';
        }
    });
    pdfZone.addEventListener('dragover', function (e) { e.preventDefault(); pdfZone.style.borderColor = 'var(--primary)'; });
    pdfZone.addEventListener('dragleave', function () { pdfZone.style.borderColor = 'var(--border)'; });
    pdfZone.addEventListener('drop', function (e) {
        e.preventDefault();
        var file = e.dataTransfer.files[0];
        if (file && file.type === 'application/pdf') {
            var dt = new DataTransfer();
            dt.items.add(file);
            pdfInput.files = dt.files;
            pdfLabel.textContent = file.name;
            pdfZone.style.borderColor = 'var(--success)';
        }
    });

});
</script>
@endpush