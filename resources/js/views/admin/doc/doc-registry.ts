// ============================================================
// doc-registry.ts
// Daftarkan jenis surat baru di sini — cukup 1 file.
// ============================================================

import type { Component } from 'vue';

export interface DocDefinition {
  /** Sama dengan :type di URL, misal 'sktm', 'sket' */
  type: string;

  /** Label tampilan di halaman */
  label: string;

  /** Singkatan untuk nomor surat, misal 'SKTM', 'SKET' */
  codePrefix: string;

  /** Nama Blade template di backend (resources/views/pdf/) */
  bladeTemplate: string;

  /** Komponen form (kiri) */
  formComponent: () => Promise<Component>;

  /** Komponen preview (kanan) */
  previewComponent: () => Promise<Component>;
}

export const DOC_REGISTRY: DocDefinition[] = [
  {
    type:             'sktm',
    label:            'Surat Keterangan Tidak Mampu',
    codePrefix:       'SKTM',
    bladeTemplate:    'surat-keterangan-tidak-mampu',
    formComponent:    () => import('@/views/admin/doc/sktm/FormSktm.vue'),
    previewComponent: () => import('@/views/admin/doc/sktm/PreviewSktm.vue'),
  },
  {
    type:             'skpk',
    label:            'Surat Keterangan Pindah Keluar',
    codePrefix:       'SKPK',
    bladeTemplate:    'surat-keterangan-pindah-keluar',
    formComponent:    () => import('@/views/admin/doc/skpk/FormSkpk.vue'),
    previewComponent: () => import('@/views/admin/doc/skpk/PreviewSkpk.vue'),
  },
  {
    type:             'skaw',
    label:            'Surat Keterangan Ahli Waris',
    codePrefix:       'SKAW',
    bladeTemplate:    'surat-keterangan-ahli-waris',
    formComponent:    () => import('@/views/admin/doc/skaw/FormSkaw.vue'),
    previewComponent: () => import('@/views/admin/doc/skaw/PreviewSkaw.vue'),
  },
  {
    type:             'skd',
    label:            'Surat Keterangan Domisili',
    codePrefix:       'SKD',
    bladeTemplate:    'surat-keterangan-domisili',
    formComponent:    () => import('@/views/admin/doc/skd/FormSkd.vue'),
    previewComponent: () => import('@/views/admin/doc/skd/PreviewSkd.vue'),
  },
];

export function getDocDefinition(type: string): DocDefinition | undefined {
  return DOC_REGISTRY.find(d => d.type === type);
}