{{-- resources/views/components/monky/security-item.blade.php --}}
@props([
    'status' => 'success',  {{-- success | warning --}}
    'label'  => '',
    'value'  => '',
    'note'   => '',
])

<div @class([
    'security-item',
    'security-success' => $status === 'success',
    'security-warning' => $status === 'warning',
])>
    <div class="flex items-center gap-2 py-1 px-2 border-b border-current">
        <span @class([
            'bullet',
            'bullet-success' => $status === 'success',
            'bullet-warning' => $status === 'warning',
        ]) style="width:6px;height:6px;"></span>
        <span class="text-sm font-medium">{{ $label }}</span>
    </div>
    <div class="py-1 px-2">
        <div class="text-2xl font-bold">{{ $value }}</div>
        <div class="text-xs opacity-50">[{{ $note }}]</div>
    </div>
</div>
