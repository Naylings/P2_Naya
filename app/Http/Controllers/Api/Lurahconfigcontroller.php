<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LurahConfig;
use Illuminate\Support\Facades\Storage;

class LurahConfigController extends Controller
{
    /**
     * GET /config
     * Ambil config yang ada
     */
    public function show()
    {
        $config = LurahConfig::getConfig();

        if (!$config) {
            return response()->json([
                'success' => true,
                'data'    => null,
                'message' => 'Config belum diisi',
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'       => $config->id,
                'name'     => $config->name,
                'province' => $config->province,
                'city'     => $config->city,
                'district' => $config->district,
                'pos_code' => $config->pos_code,
                'logo'     => $config->logo
                    ? Storage::url($config->logo)
                    : null,
                'updated_at' => $config->updated_at?->format('d M Y, H:i'),
            ],
        ]);
    }

    /**
     * POST /config
     * Simpan atau update config (upsert)
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city'     => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pos_code' => 'required|string|max:10',
            'logo'     => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ], [
            'name.required'     => 'Nama kelurahan wajib diisi',
            'province.required' => 'Provinsi wajib diisi',
            'city.required'     => 'Kota/Kabupaten wajib diisi',
            'district.required' => 'Kecamatan wajib diisi',
            'pos_code.required' => 'Kode pos wajib diisi',
            'logo.image'        => 'Logo harus berupa gambar',
            'logo.max'          => 'Ukuran logo maksimal 2MB',
        ]);

        try {
            $data = $request->only(['name', 'province', 'city', 'district', 'pos_code']);

            // Handle logo upload
            $logoFile = $request->file('logo');
            if ($logoFile !== null && $logoFile->isValid() && !empty($logoFile->getClientOriginalName())) {

                $existingConfig = LurahConfig::getConfig();

                // Hapus logo lama jika ada
                if ($existingConfig?->logo) {
                    Storage::disk('public')->delete($existingConfig->logo);
                }

                $data['logo'] = $request->file('logo')->store('config/logo', 'public');
            }

            $config = LurahConfig::saveConfig($data);

            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi berhasil disimpan',
                'data'    => [
                    'id'       => $config->id,
                    'name'     => $config->name,
                    'province' => $config->province,
                    'city'     => $config->city,
                    'district' => $config->district,
                    'pos_code' => $config->pos_code,
                    'logo'     => $config->logo
                        ? Storage::url($config->logo)
                        : null,
                    'updated_at' => $config->updated_at?->format('d M Y, H:i'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan konfigurasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /config/logo
     * Hapus logo saja
     */
    public function deleteLogo()
    {
        try {
            $config = LurahConfig::getConfig();

            if (!$config || !$config->logo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logo tidak ditemukan',
                ], 404);
            }

            Storage::disk('public')->delete($config->logo);
            $config->update(['logo' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Logo berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus logo: ' . $e->getMessage(),
            ], 500);
        }
    }
}