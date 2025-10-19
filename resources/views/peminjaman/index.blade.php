<x-main-layout title-page="{{ __('Peminjaman Inventaris') }}">
    <div class="card">
        <div class="card-body">
            @include('peminjaman.partials.toolbar')
            <x-notif-alert class="mt-4" />
            @include('peminjaman.partials.list-peminjaman')
        </div>
        <div class="card-body">
            {{ $peminjamans->links() }}
        </div>
    </div>

    <x-modal-delete />
</x-main-layout>