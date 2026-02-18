<script setup lang="ts">
import { reactive, inject, watch, onMounted, ref, computed } from 'vue';
import { useDocForm } from '@/views/admin/doc/useDocForm';
import { useDocNumber } from '@/views/admin/doc/useDocNumber';
import { LETTER_DATA_KEY } from '@/views/admin/doc/doc-types';
import type { DocFormProps } from '@/views/admin/doc/doc-types';
import WargaService from '@/service/WargaService';
import type { Warga } from '@/service/WargaService';
import FamilyService from '@/service/FamilyService';

export interface DomisiliDetail {
  no_surat:      string;
  nama_pemohon:  string;
  nik:           string;
  tempat_lahir:  string;
  tgl_lahir:     string;
  jenis_kelamin: string;
  agama:         string;
  warganegara:   string;
  married_status:string;
  occupation:    string;
  rt:            string;
  rw:            string;
  kelurahan:     string;
  kecamatan:     string;
  keperluan:     string;
}

const props = defineProps<DocFormProps>();
const letterData = inject(LETTER_DATA_KEY)!;

const model = reactive<DomisiliDetail>({
  no_surat:      '',
  nama_pemohon:  '',
  nik:           '',
  tempat_lahir:  '',
  tgl_lahir:     '',
  jenis_kelamin: '',
  agama:         '',
  warganegara:   'Indonesia',
  married_status:'',
  occupation:    '',
  rt:            '',
  rw:            '',
  kelurahan:     props.config?.name     ?? '',
  kecamatan:     props.config?.district ?? '',
  keperluan:     '',
});

// Sync kelurahan/kecamatan jika config baru datang setelah mount
watch(() => props.config, (cfg) => {
  if (cfg && !model.kelurahan) model.kelurahan = cfg.name     ?? '';
  if (cfg && !model.kecamatan) model.kecamatan = cfg.district ?? '';
});

watch(model, (val) => { letterData.value = { ...val }; }, { deep: true });

// ====== Auto nomor surat ======
const { noSurat, loading: loadingNo, generateNumber } = useDocNumber(
  props.docDef.bladeTemplate,
  props.docDef.codePrefix,
);

onMounted(async () => {
  await generateNumber();
  model.no_surat = noSurat.value;
  letterData.value = { ...model };
});

// ====== Smart Warga Search ======
const wargaList       = ref<Warga[]>([]);
const wargaSearch     = ref('');
const selectedWarga   = ref<Warga | null>(null);
const showSuggestions = ref(false);
const loadingSelect   = ref(false);

onMounted(async () => {
  try {
    const res = await WargaService.getAll();
    wargaList.value = res.data ?? [];
  } catch { /* silent */ }
});

const suggestions = computed(() => {
  const q = wargaSearch.value.trim().toLowerCase();
  if (!q || q.length < 2) return [];
  return wargaList.value
    .filter(w => w.name.toLowerCase().includes(q) || w.nik.toLowerCase().includes(q))
    .slice(0, 8);
});

async function selectWarga(warga: Warga) {
  selectedWarga.value   = warga;
  wargaSearch.value     = warga.name;
  showSuggestions.value = false;
  loadingSelect.value   = true;

  const tgl = warga.birth_date
    ? new Date(warga.birth_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
    : '';

  Object.assign(model, {
    nama_pemohon:  warga.name,
    nik:           warga.nik,
    tempat_lahir:  warga.birth_place ?? '',
    tgl_lahir:     tgl,
    jenis_kelamin: warga.gender === 'L' ? 'Laki-laki' : warga.gender === 'P' ? 'Perempuan' : '',
    agama:         warga.religious ?? '',
    married_status:warga.married_status ?? '',
    occupation:    warga.occupation ?? '',
  });
  letterData.value = { ...model };

  if (warga.family?.id) {
    try {
      const res = await FamilyService.getById(warga.family.id);
      const fam  = res.data;
      model.rt = fam.rt_no ?? '';
      model.rw = fam.rw_no ?? '';
      letterData.value = { ...model };
    } catch { /* isi manual */ }
  }

  loadingSelect.value = false;
}

function clearWarga() {
  selectedWarga.value   = null;
  wargaSearch.value     = '';
  showSuggestions.value = false;
}

function onSearchBlur() {
  setTimeout(() => { showSuggestions.value = false; }, 150);
}

// ====== Save & Print ======
const { loadingSave, loadingPrint, save, print, validate } = useDocForm(props.docDef.bladeTemplate);
const requiredFields: (keyof DomisiliDetail)[] = ['no_surat', 'nama_pemohon', 'nik', 'keperluan'];

function emptyModel(): DomisiliDetail {
  return {
    no_surat: noSurat.value, nama_pemohon: '', nik: '', tempat_lahir: '', tgl_lahir: '',
    jenis_kelamin: '', agama: '', warganegara: 'Indonesia', married_status: '', occupation: '',
    rt: '', rw: '',
    kelurahan: props.config?.name     ?? '',
    kecamatan: props.config?.district ?? '',
    keperluan: '',
  };
}

async function handleSave() {
  if (!validate(model, requiredFields)) return;
  const id = await save({ ...model });
  if (id) {
    clearWarga();
    await generateNumber();
    Object.assign(model, emptyModel());
    letterData.value = { ...model };
  }
}

async function handlePrint() {
  if (!validate(model, requiredFields)) return;
  await print({ ...model });
}

function handleClear() {
  clearWarga();
  Object.assign(model, emptyModel());
  letterData.value = { ...model };
}
</script>

<template>
  <Card>
    <template #title>
      <div class="font-semibold text-xl">Surat Keterangan Domisili</div>
    </template>

    <template #content>
      <div class="grid grid-cols-6 gap-4">

        <!-- Nomor Surat -->
        <div class="col-span-6 flex flex-col gap-2">
          <label class="font-semibold text-sm">
            Nomor Surat
            <span class="text-gray-400 font-normal text-xs ml-1">(otomatis)</span>
          </label>
          <div class="flex gap-2">
            <InputText v-model="model.no_surat" class="w-full" :disabled="loadingNo"
              :placeholder="loadingNo ? 'Memuat...' : '001/SKD/IX/2025'" />
            <Button icon="pi pi-refresh" severity="secondary" outlined :loading="loadingNo"
              v-tooltip.top="'Generate ulang nomor'"
              @click="generateNumber().then(() => { model.no_surat = noSurat; letterData.value = { ...model }; })" />
          </div>
        </div>

        <!-- Smart Search Warga -->
        <div class="col-span-6 flex flex-col gap-2">
          <label class="font-semibold text-sm">
            Cari Warga
            <span class="text-gray-400 font-normal text-xs ml-1">(nama atau NIK, isi otomatis)</span>
          </label>
          <div class="relative">
            <div class="flex gap-2">
              <div class="relative w-full">
                <InputText v-model="wargaSearch" placeholder="Ketik nama atau NIK..." class="w-full"
                  :class="selectedWarga ? 'pr-8' : ''" @input="showSuggestions = true" @blur="onSearchBlur" />
                <span v-if="selectedWarga"
                  class="absolute right-2 top-1/2 -translate-y-1/2 text-green-500 text-xs pointer-events-none">
                  <i class="pi pi-check-circle" />
                </span>
              </div>
              <Button v-if="selectedWarga" icon="pi pi-times" severity="secondary" outlined
                v-tooltip.top="'Hapus pilihan'" @click="handleClear" />
            </div>
            <div v-if="showSuggestions && suggestions.length"
              class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto">
              <div v-for="w in suggestions" :key="w.id"
                class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0"
                @mousedown.prevent="selectWarga(w)">
                <div class="flex-1 min-w-0">
                  <div class="font-medium text-sm truncate">{{ w.name }}</div>
                  <div class="text-xs text-gray-400">{{ w.nik }}</div>
                </div>
                <div class="text-right text-xs text-gray-400 shrink-0">
                  <div>{{ w.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                  <div>{{ w.birth_place }}</div>
                </div>
              </div>
            </div>
            <div v-else-if="showSuggestions && wargaSearch.length >= 2 && !suggestions.length && !selectedWarga"
              class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg px-4 py-3 text-sm text-gray-400">
              Warga tidak ditemukan — isi manual di bawah
            </div>
          </div>
        </div>

        <div class="col-span-6"><Divider class="my-0" /></div>

        <!-- Nama & Jenis Kelamin -->
        <div class="col-span-6 md:col-span-4 flex flex-col gap-2">
          <label class="font-semibold text-sm">Nama Lengkap <span class="text-red-500">*</span></label>
          <InputText v-model="model.nama_pemohon" placeholder="Budi Santoso" class="w-full" />
        </div>
        <div class="col-span-6 md:col-span-2 flex flex-col gap-2">
          <label class="font-semibold text-sm">Jenis Kelamin</label>
          <Select v-model="model.jenis_kelamin" :options="['Laki-laki', 'Perempuan']"
            placeholder="Pilih" class="w-full" />
        </div>

        <!-- NIK & Warganegara -->
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">NIK <span class="text-red-500">*</span></label>
          <InputText v-model="model.nik" placeholder="3201xxxxxxxxxxxxxxx" maxlength="16" class="w-full" />
        </div>
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">Warganegara</label>
          <InputText v-model="model.warganegara" placeholder="Indonesia" class="w-full" />
        </div>

        <!-- TTL -->
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">Tempat Lahir</label>
          <InputText v-model="model.tempat_lahir" placeholder="Bandung" class="w-full" />
        </div>
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">Tanggal Lahir</label>
          <InputText v-model="model.tgl_lahir" placeholder="15 Januari 1990" class="w-full" />
        </div>

        <!-- Agama & Status -->
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">Agama</label>
          <InputText v-model="model.agama" placeholder="Islam" class="w-full" />
        </div>
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">Status Pernikahan</label>
          <InputText v-model="model.married_status" placeholder="Kawin / Belum Kawin" class="w-full" />
        </div>

        <!-- Pekerjaan -->
        <div class="col-span-6 flex flex-col gap-2">
          <label class="font-semibold text-sm">Pekerjaan</label>
          <InputText v-model="model.occupation" placeholder="Buruh / Petani / dll" class="w-full" />
        </div>

        <div class="col-span-6"><Divider class="my-0" /></div>

        <!-- RT & RW — auto-fill dari family, bisa edit manual -->
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">
            RT
            <span v-if="loadingSelect" class="text-gray-400 font-normal text-xs ml-1">
              <i class="pi pi-spin pi-spinner" /> memuat...
            </span>
          </label>
          <InputText v-model="model.rt" placeholder="001" class="w-full" :disabled="loadingSelect" />
        </div>
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">
            RW
            <span v-if="loadingSelect" class="text-gray-400 font-normal text-xs ml-1">
              <i class="pi pi-spin pi-spinner" /> memuat...
            </span>
          </label>
          <InputText v-model="model.rw" placeholder="002" class="w-full" :disabled="loadingSelect" />
        </div>

        <!-- Kelurahan & Kecamatan — default dari config, bisa override -->
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">
            Kelurahan
            <span class="text-gray-400 font-normal text-xs ml-1">(dari config)</span>
          </label>
          <InputText v-model="model.kelurahan" class="w-full" />
        </div>
        <div class="col-span-6 md:col-span-3 flex flex-col gap-2">
          <label class="font-semibold text-sm">
            Kecamatan
            <span class="text-gray-400 font-normal text-xs ml-1">(dari config)</span>
          </label>
          <InputText v-model="model.kecamatan" class="w-full" />
        </div>

        <!-- Keperluan -->
        <div class="col-span-6 flex flex-col gap-2">
          <label class="font-semibold text-sm">Keperluan <span class="text-red-500">*</span></label>
          <InputText v-model="model.keperluan" placeholder="Melamar Pekerjaan / Mengurus BPJS / dll" class="w-full" />
        </div>

        <!-- Buttons -->
        <div class="col-span-6 flex gap-3 pt-2 justify-end">
          <Button icon="pi pi-trash"  label="Clear"  severity="danger"  outlined       @click="handleClear" />
          <Button icon="pi pi-save"   label="Simpan" severity="success" :loading="loadingSave"  @click="handleSave" />
          <Button icon="pi pi-print"  label="Print"  severity="info"    :loading="loadingPrint" @click="handlePrint" />
        </div>

      </div>
    </template>
  </Card>
</template>