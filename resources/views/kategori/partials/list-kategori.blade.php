<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Nama Kategori</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>
    
    @forelse ($kategoris as $index => $kategori)
        <tr>
            <td>{{ $kategoris->firstItem() + $index }}</td>
            <td>{{ $kategori->nama_kategori }}</td>
            
            <td>
                <x-tombol-aksi :href="route('kategori.edit', $kategori->id)" type="edit" />
                <x-tombol-aksi :href="route('kategori.destroy', $kategori->id)" type="delete" />
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center">
                <div class="alert alert-danger">
                    Data kategori belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>