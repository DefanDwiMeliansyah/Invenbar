<x-main-layout :title-page=" __('Edit Lokasi')">
    <form class="card col-lg-6" action="{{ route('lokasi.update', $lokasi->id) }}" method="post" enctype="multipart/form-data">
        <div class="card-body">
            @method('PUT')
            @include('lokasi.partials._form', ['update' => true])
        </div>
    </form>
</x-main-layout>