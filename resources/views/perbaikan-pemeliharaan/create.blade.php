<x-main-layout title-page="{{ __('Ajukan Perbaikan / Pemeliharaan') }}">
    <div class="card">
        <div class="card-body">
            <x-notif-alert />
            
            <form action="{{ route('perbaikan-pemeliharaan.store') }}" method="POST">
                @csrf
                @include('perbaikan-pemeliharaan.partials.form')
            </form>
        </div>
    </div>
</x-main-layout>