<x-main-layout :title-page=" __('Edit Kategori')">
    <form class="card col-lg-6" action="{{ route('kategori.update', $kategori->id) }}" method="post" enctype="multipart/form-data">
        <div class="card-body">
            @method('PUT')
            @include('kategori.partials._form', ['update' => true])
        </div>
    </form>
</x-main-layout>