import { ref } from "vue";
import LetterService from "@/service/DocumentService";

export function useDocNumber(bladeTemplate: string, codePrefix: string) {
  const noSurat = ref("");
  const loading = ref(false);

  async function generateNumber(): Promise<void> {
    loading.value = true;

    const romans = [
      "I", "II", "III", "IV", "V", "VI",
      "VII", "VIII", "IX", "X", "XI", "XII",
    ];

    try {
      const count = await LetterService.count(bladeTemplate);
      const seq = String(count + 1).padStart(3, "0");
      const year = new Date().getFullYear();
      const month = romans[new Date().getMonth()];

      noSurat.value = `${seq}/${codePrefix}/${month}/${year}`;
    } catch {
      // fallback: minimal tetap ada nomor "001"
      const year = new Date().getFullYear();
      const month = romans[new Date().getMonth()];
      noSurat.value = `001/${codePrefix}/${month}/${year}`;
    } finally {
      loading.value = false;
    }
  }

  return { noSurat, loading, generateNumber };
}
