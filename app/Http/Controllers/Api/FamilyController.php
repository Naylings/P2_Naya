<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Family;
use App\Models\Warga;
use App\Models\Rukun;
use Illuminate\Support\Facades\DB;

class FamilyController extends Controller
{
    public function index()
    {
        $families = Family::with(['familyHead', 'rt', 'rw'])
            ->withCount('members')
            ->latest()
            ->get()
            ->map(fn($family) => [
                'id'               => $family->id,
                'no_kk'            => $family->no_kk,
                'rt_id'            => $family->rt_id,
                'rw_id'            => $family->rw_id,
                'rt_no'            => $family->rt?->no ?? '-',
                'rw_no'            => $family->rw?->no ?? '-',
                'address'          => $family->address,
                'family_head_id'   => $family->family_head_id,
                'family_head_name' => $family->familyHead?->name ?? '-',
                'members_count'    => $family->members_count,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $families,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kk'          => 'required|string|size:16|unique:family,no_kk',
            'rt_id'          => 'required|exists:rukun,id',
            'rw_id'          => 'required|exists:rukun,id',
            'address'        => 'required|string|max:500',
            'family_head_id' => 'nullable|exists:warga,id',
            'member_ids'     => 'nullable|array',
            'member_ids.*'   => 'exists:warga,id',
        ]);

        DB::beginTransaction();
        try {
            $family = Family::create([
                'no_kk'   => $validated['no_kk'],
                'rt_id'   => $validated['rt_id'],
                'rw_id'   => $validated['rw_id'],
                'address' => $validated['address'],
            ]);

            if (!empty($validated['member_ids'])) {
                $family->assignMembers($validated['member_ids']);
            }

            if (!empty($validated['family_head_id'])) {
                $warga = Warga::find($validated['family_head_id']);
                if ($warga->no_kk !== $family->no_kk) {
                    $family->assignMembers([$validated['family_head_id']]);
                }
                $family->setHead($validated['family_head_id']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil ditambahkan',
                'data'    => $family->load(['familyHead', 'members']),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $family = Family::with(['familyHead', 'members', 'rt', 'rw'])->findOrFail($id);
            $availableHeads = $family->getAvailableHeads();

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'               => $family->id,
                    'no_kk'            => $family->no_kk,
                    'rt_id'            => $family->rt_id,
                    'rw_id'            => $family->rw_id,
                    'rt_no'            => $family->rt?->no ?? '-',
                    'rw_no'            => $family->rw?->no ?? '-',
                    'address'          => $family->address,
                    'family_head_id'   => $family->family_head_id,
                    'family_head_name' => $family->familyHead?->name ?? '-',
                    'members_count'    => $family->members->count(),
                    'members'          => $family->members->map(fn($w) => [
                        'id'      => $w->id,
                        'name'    => $w->name,
                        'nik'     => $w->nik,
                        'is_head' => $w->isHead(),
                    ]),
                    'available_heads'  => $availableHeads->map(fn($w) => [
                        'id'   => $w->id,
                        'name' => $w->name,
                        'nik'  => $w->nik,
                    ]),
                    'created_at'       => $family->created_at?->format('d M Y, H:i'),
                    'updated_at'       => $family->updated_at?->format('d M Y, H:i'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no_kk'          => 'required|string|size:16|unique:family,no_kk,' . $id,
            'rt_id'          => 'required|exists:rukun,id',
            'rw_id'          => 'required|exists:rukun,id',
            'address'        => 'required|string|max:500',
            'family_head_id' => 'nullable|exists:warga,id',
        ]);

        DB::beginTransaction();
        try {
            $family = Family::findOrFail($id);
            $oldNoKk = $family->no_kk;

            $family->update([
                'no_kk'   => $validated['no_kk'],
                'rt_id'   => $validated['rt_id'],
                'rw_id'   => $validated['rw_id'],
                'address' => $validated['address'],
            ]);

            // Jika no_kk berubah, update semua anggota
            if ($oldNoKk !== $validated['no_kk']) {
                Warga::where('no_kk', $oldNoKk)
                    ->update(['no_kk' => $validated['no_kk']]);
            }

            if (!empty($validated['family_head_id'])) {
                $family->setHead($validated['family_head_id']);
            } else {
                $family->update(['family_head_id' => null]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil diupdate',
                'data'    => $family->fresh(['familyHead', 'members']),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $family = Family::findOrFail($id);
            $family->removeAllMembers();
            $family->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function setHead(Request $request, $id)
    {
        $validated = $request->validate([
            'warga_id' => 'required|exists:warga,id',
        ]);

        try {
            $family = Family::findOrFail($id);
            $family->setHead($validated['warga_id']);

            return response()->json([
                'success' => true,
                'message' => 'Kepala keluarga berhasil diubah',
                'data'    => $family->fresh(['familyHead', 'members']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function addMember(Request $request, $id)
    {
        $validated = $request->validate([
            'warga_id' => 'required|exists:warga,id',
        ]);

        DB::beginTransaction();
        try {
            $family = Family::findOrFail($id);
            $family->assignMembers([$validated['warga_id']]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Anggota berhasil ditambahkan',
                'data'    => $family->fresh(['familyHead', 'members']),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeMember(Request $request, $id)
    {
        $validated = $request->validate([
            'warga_id' => 'required|exists:warga,id',
        ]);

        DB::beginTransaction();
        try {
            $family = Family::findOrFail($id);
            $family->removeMember($validated['warga_id']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Anggota berhasil dihapus dari keluarga',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}