@props(['name', 'label' => null, 'value' => null, 'rows' => 3, 'disabled' => false])

@if ($label)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
    </label>
@endif

<textarea 
    class="form-control @error($name) is-invalid @enderror" 
    id="{{ $name }}" 
    name="{{ $name }}" 
    rows="{{ $rows }}" 
    @disabled($disabled)
>{{ old($name, $value ?? '') }}</textarea>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
