<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReturnPeminjamanRequest extends FormRequest
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
            'detail_ids' => 'required|array|min:1',
            'detail_ids.*' => 'required|exists:peminjaman_details,id',
            'jumlah_kembali' => 'nullable|array',
            'jumlah_kembali.*' => 'nullable|integer|min:1',
            'kondisi_akhir' => 'required|array',
            'kondisi_akhir.*' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'detail_ids.required' => 'Pilih minimal 1 barang untuk dikembalikan.',
            'detail_ids.min' => 'Pilih minimal 1 barang untuk dikembalikan.',
            'detail_ids.*.exists' => 'Data barang tidak valid.',
            'jumlah_kembali.*.required' => 'Jumlah pengembalian wajib diisi.',
            'jumlah_kembali.*.min' => 'Jumlah pengembalian minimal 1.',
            'kondisi_akhir.*.required' => 'Kondisi akhir barang wajib diisi.',
            'kondisi_akhir.*.in' => 'Kondisi akhir tidak valid.',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // Ensure jumlah_kembali exists for all detail_ids
        $detailIds = $this->input('detail_ids', []);
        $jumlahKembali = $this->input('jumlah_kembali', []);
        
        foreach ($detailIds as $detailId) {
            if (!isset($jumlahKembali[$detailId])) {
                $jumlahKembali[$detailId] = 1; // Default untuk barang per unit
            }
        }
        
        $this->merge([
            'jumlah_kembali' => $jumlahKembali
        ]);
    }
}