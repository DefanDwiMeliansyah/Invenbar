<x-main-layout :title-page=" __('Tambah Barang')">
    <form class="card" action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            @include('barang.partials._form')
        </div>
    </form>
</x-main-layout>
