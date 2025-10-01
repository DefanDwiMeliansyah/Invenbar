<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LokasiController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view lokasi', only: ['index', 'show']),
            new Middleware('permission:manage lokasi', except: ['index', 'show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search ?? null;

        // Ambil semua lokasi + jumlah barangnya + fitur pencarian + pagination
        $lokasis = Lokasi::withCount('barang') // hitung barang otomatis
            ->when($search, function ($query, $search) {
                $query->where('nama_lokasi', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // supaya parameter search tetap

        return view('lokasi.index', compact('lokasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lokasi = new Lokasi();

        return view('lokasi.create', compact('lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100|unique:lokasis,nama_lokasi',
        ]);

        $lokasi = Lokasi::create($validated);

        // Jika mau hitung jumlah barang di lokasi baru (biasanya 0)
        $jumlahBarang = Barang::where('lokasi_id', $lokasi->id)->count();

        return redirect()->route('lokasi.index')
            ->with('success', 'Lokasi baru berhasil ditambahkan.')
            ->with('jumlahBarang', $jumlahBarang);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lokasi $lokasi)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lokasi $lokasi)
    {
        return view('lokasi.edit', compact('lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:100|unique:lokasis,nama_lokasi,' . $lokasi->id,
        ]);

        $lokasi->update($validated);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lokasi $lokasi)
    {
        if ($lokasi->barang()->exists()) {
            return redirect()->route('lokasi.index')
                ->with('error', 'Lokasi tidak dapat dihapus karena masih memiliki barang terkait.');
        }

        $lokasi->delete();

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}