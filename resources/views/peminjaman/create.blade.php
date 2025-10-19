<x-main-layout title-page="{{ __('Buat Peminjaman Baru') }}">
    <div class="card">
        <div class="card-body">
            <x-notif-alert />
            
            <form action="{{ route('peminjaman.store') }}" method="POST" id="formPeminjaman">
                @csrf
                @include('peminjaman.partials.form')
            </form>
        </div>
    </div>

    @include('peminjaman.partials.modal-pilih-barang')
</x-main-layout>