<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePeminjamanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama_peminjam' => 'required|string|max:150',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'tanggal_pinjam' => 'required|date',
            'tanggal_batas_pengembalian' => 'required|date|after_or_equal:tanggal_pinjam',
            'lokasi_id' => 'required|exists:lokasis,id',
            'barang_ids' => 'required|array|min:1',
            'barang_ids.*' => 'required|exists:barangs,id',
            'jumlah_pinjam' => 'required|array',
            'jumlah_pinjam.*' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_batas_pengembalian.required' => 'Tanggal batas pengembalian wajib diisi.',
            'tanggal_batas_pengembalian.after_or_equal' => 'Tanggal batas pengembalian harus setelah atau sama dengan tanggal pinjam.',
            'lokasi_id.required' => 'Lokasi wajib dipilih.',
            'lokasi_id.exists' => 'Lokasi tidak valid.',
            'barang_ids.required' => 'Pilih minimal 1 barang untuk dipinjam.',
            'barang_ids.min' => 'Pilih minimal 1 barang untuk dipinjam.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-fill lokasi untuk petugas
        $user = Auth::user();
        if ($user && $user->isPetugas() && $user->lokasi_id) {
            $this->merge([
                'lokasi_id' => $user->lokasi_id,
            ]);
        }
    }
}