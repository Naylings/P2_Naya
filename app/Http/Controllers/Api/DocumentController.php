<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentLog;
use App\Models\LurahConfig;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    /**
     * Simpan surat: generate PDF + simpan snapshot JSON ke document_log.
     */
    public function store(Request $request)
    {
        // Validasi hanya untuk field yang WAJIB ada di semua jenis surat.
        // PENTING: jangan pakai $input['detail'] — validate() hanya return
        // field yang ada di rules, sehingga field lain (nik, nama, dll) hilang.
        $request->validate([
            'doc_type'        => 'required|string',
            'detail'          => 'required|array',
            'detail.no_surat' => 'required|string',
        ]);

        $docType = $request->input('doc_type');
        $detail  = $request->input('detail'); // ← ambil SEMUA field dari detail

        // Snapshot config kelurahan saat surat dibuat — termasuk logo sebagai base64
        // agar PDF bisa di-regenerate meski config berubah atau file logo dipindah.
        $config         = LurahConfig::first();
        $configSnapshot = $config
            ? collect($config->toArray())
                ->except(['id', 'created_at', 'updated_at'])
                ->toArray()
            : [];

        // Konversi logo ke base64 agar DomPDF bisa render gambar
        // (DomPDF tidak bisa load URL eksternal / http)
        if (!empty($configSnapshot['logo'])) {
            $configSnapshot['logo_base64'] = $this->logoToBase64($configSnapshot['logo']);
        }

        // Simpan snapshot config ke dalam detail JSON
        $detail['_config'] = $configSnapshot;

        // Generate PDF
        $pdf = Pdf::loadView("pdf.{$docType}", [
            'letter' => (object) $detail,
            'config' => (object) $configSnapshot,
        ])->setPaper('a4');

        $safeNo   = str_replace(['/', '\\', ' ', ':'], '-', $detail['no_surat']);
        $fileName = "{$docType}_{$safeNo}.pdf";
        $filePath = "surat/{$fileName}";

        Storage::disk('public')->put($filePath, $pdf->output());

        $log = DocumentLog::create([
            'doc_type'   => $docType,
            'detail'     => $detail,
            'local_file' => $filePath,
        ]);

        return response()->json([
            'message'  => 'Surat berhasil disimpan',
            'id'       => $log->id,
            'file_url' => asset("storage/{$filePath}"),
        ], 201);
    }

    /**
     * Hitung jumlah surat berdasarkan doc_type.
     * GET /api/doc/count?doc_type=surat-keterangan-tidak-mampu
     */
    public function count(Request $request)
    {
        $request->validate(['doc_type' => 'required|string']);

        $count = DocumentLog::where('doc_type', $request->doc_type)->count();

        return response()->json(['count' => $count]);
    }

    /**
     * List semua log surat, opsional filter by doc_type.
     */
    public function index(Request $request)
    {
        $query = DocumentLog::latest('created_at');

        if ($request->filled('doc_type')) {
            $query->where('doc_type', $request->doc_type);
        }

        $logs = $query->select(
            'id', 'doc_type', 'local_file', 'created_at',
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(detail, '$.no_surat'))     as no_surat"),
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(detail, '$.nama_pemohon')) as nama_pemohon"),
        )->get();

        return response()->json($logs);
    }

    /**
     * Regenerate PDF dari snapshot JSON tersimpan.
     */
    public function regenerate(DocumentLog $log)
    {
        $detail  = $log->detail;
        $config  = (object) ($detail['_config'] ?? []);
        $docType = $log->doc_type;

        $pdf = Pdf::loadView("pdf.{$docType}", [
            'letter' => (object) $detail,
            'config' => $config,
        ])->setPaper('a4');

        Storage::disk('public')->put($log->local_file, $pdf->output());

        return response()->json([
            'message'  => 'PDF berhasil dibuat ulang',
            'file_url' => asset('storage/' . $log->local_file),
        ]);
    }

    /**
     * Stream PDF dari snapshot JSON (tidak butuh file lokal).
     */
    public function stream(DocumentLog $log)
    {
        $detail  = $log->detail;
        $config  = (object) ($detail['_config'] ?? []);
        $docType = $log->doc_type;

        $pdf = Pdf::loadView("pdf.{$docType}", [
            'letter' => (object) $detail,
            'config' => $config,
        ])->setPaper('a4');

        $safeNo   = str_replace(['/', '\\', ' ', ':'], '-', $detail['no_surat'] ?? $log->id);
        $fileName = "{$docType}_{$safeNo}.pdf";

        return $pdf->stream($fileName);
    }

    /**
     * Hapus log + file lokal.
     */
    public function destroy(DocumentLog $log)
    {
        Storage::disk('public')->delete($log->local_file);
        $log->delete();

        return response()->json(['message' => 'Surat berhasil dihapus']);
    }

    /**
     * Konversi URL/path logo ke base64 agar bisa dirender oleh DomPDF.
     * DomPDF tidak support URL http/https eksternal.
     */
    private function logoToBase64(string $logoUrl): ?string
    {
        try {
            // Coba ambil dari storage lokal dulu (lebih cepat, tidak butuh HTTP)
            $relativePath = str_replace(asset('storage') . '/', '', $logoUrl);
            $localPath    = Storage::disk('public')->path($relativePath);

            if (file_exists($localPath)) {
                $mime    = mime_content_type($localPath);
                $encoded = base64_encode(file_get_contents($localPath));
                return "data:{$mime};base64,{$encoded}";
            }

            // Fallback: fetch via HTTP jika tidak ditemukan lokal
            $content = @file_get_contents($logoUrl);
            if ($content === false) return null;

            $finfo   = new \finfo(FILEINFO_MIME_TYPE);
            $mime    = $finfo->buffer($content);
            $encoded = base64_encode($content);
            return "data:{$mime};base64,{$encoded}";

        } catch (\Throwable) {
            return null;
        }
    }
}