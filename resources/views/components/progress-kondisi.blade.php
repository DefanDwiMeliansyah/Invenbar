@props(['judul', 'jumlah', 'kondisi', 'color'])

<h6 class="small font-weight-bold">
    {{ $judul }} <span class="float-end">{{ $kondisi }}</span>
</h6>

<div class="progress mb-4">
    <div class="progress-bar bg-{{ $color }}" role="progressbar"style="width: {{ $jumlah > 0 ? ($kondisi / $jumlah) * 100 : 0 }}%">
    </div>
</div>