// ============================================================
// useDocForm.ts
// Composable shared untuk semua form surat.
// Print menggunakan print-js yang fetch PDF dari BE.
// ============================================================
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import printJS from 'print-js';
import LetterService from '@/service/DocumentService';

export function useDocForm(bladeTemplate: string) {
  const toast        = useToast();
  const loadingSave  = ref(false);
  const loadingPrint = ref(false);

  // ====== SAVE ======
  async function save(detail: Record<string, any>): Promise<number | null> {
    loadingSave.value = true;
    try {
      const res = await LetterService.store({ doc_type: bladeTemplate, detail: detail as any });
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Surat berhasil disimpan', life: 3000 });
      return res.id;
    } catch (err: any) {
      toast.add({ severity: 'error', summary: 'Gagal', detail: getErrMsg(err), life: 4000 });
      return null;
    } finally {
      loadingSave.value = false;
    }
  }

  // ====== PRINT ======
  // Simpan ke BE → dapat file_url PDF → print-js buka dialog print dari PDF.
  // Tidak pakai window.print() — tidak ada masalah CSS/visibility/loading.
  async function print(detail: Record<string, any>): Promise<void> {
    loadingPrint.value = true;
    try {
      const res = await LetterService.store({ doc_type: bladeTemplate, detail: detail as any });

      const fileUrl: string = res.file_url;
      if (!fileUrl) throw new Error('URL PDF tidak ditemukan dari server');

      printJS({
        printable: fileUrl,
        type: 'pdf',
        onLoadingEnd: () => {
          loadingPrint.value = false;
        },
        onError: (err) => {
          loadingPrint.value = false;
          toast.add({ severity: 'error', summary: 'Gagal membuka PDF', detail: String(err), life: 4000 });
        },
      });

    } catch (err: any) {
      loadingPrint.value = false;
      if (isDuplicateError(err)) {
        toast.add({ severity: 'warn', summary: 'Nomor sudah ada', detail: 'Gunakan nomor surat yang berbeda atau generate ulang.', life: 4000 });
      } else {
        toast.add({ severity: 'error', summary: 'Gagal menyimpan', detail: getErrMsg(err), life: 4000 });
      }
    }
  }

  // ====== VALIDATE ======
  function validate(detail: Record<string, any>, requiredFields: string[]): boolean {
    const missing = requiredFields.filter(k => !detail[k]?.toString().trim());
    if (missing.length) {
      toast.add({ severity: 'warn', summary: 'Form tidak lengkap', detail: 'Mohon isi semua kolom yang wajib', life: 3000 });
      return false;
    }
    return true;
  }

  return { loadingSave, loadingPrint, save, print, validate };
}

function getErrMsg(err: any): string { return err?.response?.data?.message || 'Terjadi kesalahan'; }
function isDuplicateError(err: any): boolean {
  return !!getErrMsg(err).toLowerCase().match(/unique|duplicate|sudah/);
}