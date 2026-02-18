// ============================================================
// useDocConfig.ts
// Load config kelurahan (kop surat) sekali, share ke semua komponen.
// ============================================================

import { ref, onBeforeMount } from 'vue';
import ConfigService from '@/service/ConfigService';
import type { LurahConfig } from '@/service/ConfigService';

export function useDocConfig() {
  const config  = ref<LurahConfig | null>(null);
  const loading = ref(false);

  async function loadConfig() {
    loading.value = true;
    try {
      const res = await ConfigService.get();
      config.value = res.data ?? null;
    } catch {
      // preview tetap tampil meski config gagal dimuat
    } finally {
      loading.value = false;
    }
  }

  onBeforeMount(() => loadConfig());

  return { config, loading };
}