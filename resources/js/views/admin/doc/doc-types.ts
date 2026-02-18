// ============================================================
// doc-types.ts
// Interface & type shared untuk semua jenis surat.
// ============================================================

import type { LurahConfig } from '@/service/ConfigService';
import type { DocDefinition } from './doc-registry';

// ====== Props standar untuk komponen Form ======
// Model TIDAK di-inject dari luar — tiap form manage sendiri
export interface DocFormProps {
  config: LurahConfig | null;
  docDef: DocDefinition;
}

// ====== Props standar untuk komponen Preview ======
// Preview dapat data via inject (provide dari form)
export interface DocPreviewProps {
  config: LurahConfig | null;
}

// ====== Injection key untuk share data form → preview ======
import type { InjectionKey, Ref } from 'vue';

export const LETTER_DATA_KEY = Symbol('letterData') as InjectionKey<Ref<Record<string, any>>>;