<x-main-layout title-page="{{ __('Perbaikan Pemeliharaan') }}">
    <div class="card">
        <div class="card-body">
            @include('perbaikan-pemeliharaan.partials.toolbar')
            <x-notif-alert class="mt-4" />
            @include('perbaikan-pemeliharaan.partials.list-perbaikan')
        </div>
        <div class="card-body">
            {{ $perbaikans->links() }}
        </div>
    </div>

    <x-modal-delete />
</x-main-layout>