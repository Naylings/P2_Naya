<script setup lang="ts">
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import WargaService from '@/service/WargaService';

interface Props {
  visible: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:visible': [value: boolean];
  'imported': [];
}>();

const toast = useToast();
const loading = ref(false);
const loadingTemplate = ref(false);
const selectedFile = ref<File | null>(null);
const importResult = ref<{
  success_count: number;
  error_count: number;
  errors: { row: number; message: string }[];
} | null>(null);

function onFileSelect(event: any) {
  selectedFile.value = event.files[0] ?? null;
  importResult.value = null;
}

function onFileClear() {
  selectedFile.value = null;
  importResult.value = null;
}

async function handleImport() {
  if (!selectedFile.value) {
    toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Pilih file terlebih dahulu', life: 3000 });
    return;
  }

  loading.value = true;
  try {
    const response = await WargaService.import(selectedFile.value);
    importResult.value = {
      success_count: response.success_count,
      error_count: response.error_count,
      errors: response.errors ?? [],
    };

    if (response.error_count === 0) {
      toast.add({ severity: 'success', summary: 'Import Berhasil', detail: `${response.success_count} data berhasil diimport`, life: 4000 });
      emit('imported');
    } else {
      toast.add({
        severity: 'warn',
        summary: 'Import Selesai dengan Error',
        detail: `${response.success_count} berhasil, ${response.error_count} gagal`,
        life: 4000,
      });
    }
  } catch (error: any) {
    toast.add({
      severity: 'error',
      summary: 'Import Gagal',
      detail: error?.response?.data?.message || 'Gagal memproses file',
      life: 3000,
    });
  } finally {
    loading.value = false;
  }
}

async function handleDownloadTemplate() {
  loadingTemplate.value = true;
  try {
    const blob = await WargaService.downloadTemplate();
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'template_import_warga.xlsx';
    a.click();
    URL.revokeObjectURL(url);
    toast.add({ severity: 'info', summary: 'Download', detail: 'Template berhasil diunduh', life: 3000 });
  } catch {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal mengunduh template', life: 3000 });
  } finally {
    loadingTemplate.value = false;
  }
}

function handleClose() {
  selectedFile.value = null;
  importResult.value = null;
  emit('update:visible', false);
}
</script>

<template>
  <Dialog
    header="Import Data Warga"
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    :style="{ width: '50vw' }"
    :breakpoints="{ '960px': '80vw', '640px': '95vw' }"
    :modal="true"
    :dismissableMask="true"
  >
    <div class="flex flex-col gap-6">

      <!-- Info & Download Template -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <i class="pi pi-info-circle text-blue-500 mt-0.5" style="font-size: 1.1rem" />
          <div class="flex-1">
            <p class="text-sm font-semibold text-blue-800 mb-1">Panduan Import</p>
            <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
              <li>Gunakan template yang sudah disediakan</li>
              <li>Format file: <strong>.xlsx, .xls, atau .csv</strong></li>
              <li>Ukuran maksimal: <strong>5 MB</strong></li>
              <li>NIK harus 16 digit dan belum terdaftar</li>
              <li>Kolom <strong>no_kk</strong> harus sudah ada di sistem (opsional)</li>
            </ul>
          </div>
        </div>
        <div class="mt-3">
          <Button
            icon="pi pi-download"
            label="Download Template"
            severity="info"
            outlined
            size="small"
            :loading="loadingTemplate"
            @click="handleDownloadTemplate"
          />
        </div>
      </div>

      <!-- File Upload -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Pilih File <span class="text-red-500">*</span></label>
        <FileUpload
          mode="basic"
          accept=".xlsx,.xls,.csv"
          :maxFileSize="5242880"
          chooseLabel="Pilih File Excel/CSV"
          :auto="false"
          @select="onFileSelect"
          @clear="onFileClear"
        />
        <p v-if="selectedFile" class="text-sm text-green-600">
          <i class="pi pi-file-excel mr-1" />
          {{ selectedFile.name }}
          ({{ (selectedFile.size / 1024).toFixed(1) }} KB)
        </p>
      </div>

      <!-- Import Result -->
      <div v-if="importResult" class="flex flex-col gap-3">
        <div class="flex gap-4">
          <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex-1 text-center">
            <p class="text-2xl font-bold text-green-600">{{ importResult.success_count }}</p>
            <p class="text-sm text-green-700">Berhasil</p>
          </div>
          <div class="bg-red-50 border border-red-200 rounded-lg p-3 flex-1 text-center">
            <p class="text-2xl font-bold text-red-600">{{ importResult.error_count }}</p>
            <p class="text-sm text-red-700">Gagal</p>
          </div>
        </div>

        <!-- Error detail -->
        <div v-if="importResult.errors.length > 0">
          <p class="text-sm font-semibold text-red-600 mb-2">Detail Error:</p>
          <div class="max-h-40 overflow-y-auto border border-red-200 rounded-lg">
            <div
              v-for="(err, idx) in importResult.errors"
              :key="idx"
              class="flex gap-2 px-3 py-2 text-sm border-b border-red-100 last:border-b-0"
            >
              <span class="text-red-500 font-medium whitespace-nowrap">Baris {{ err.row }}:</span>
              <span class="text-gray-700">{{ err.message }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button
          label="Tutup"
          severity="secondary"
          outlined
          @click="handleClose"
        />
        <Button
          icon="pi pi-upload"
          label="Import Sekarang"
          severity="success"
          :loading="loading"
          :disabled="!selectedFile"
          @click="handleImport"
        />
      </div>
    </template>
  </Dialog>
</template>