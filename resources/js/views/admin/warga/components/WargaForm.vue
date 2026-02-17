<script setup lang="ts">
import type { WargaForm } from '../composables/useWargaManagement';

interface Props {
  visible: boolean;
  loading: boolean;
  isEditMode: boolean;
  form: WargaForm;
  dialogTitle: string;
  submitButtonLabel: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:visible': [value: boolean];
  'submit': [];
  'cancel': [];
}>();

const genders = [
  { label: 'Laki-laki', value: 'L' },
  { label: 'Perempuan', value: 'P' },
];

const livingStatuses = [
  { label: 'Hidup', value: 'hidup' },
  { label: 'Meninggal', value: 'meninggal' },
  { label: 'Pindah', value: 'pindah' },
  { label: 'Tidak Diketahui', value: 'tidak_diketahui' },
];

const bloodTypes = ['A', 'B', 'AB', 'O'];
</script>

<template>
  <Dialog
    :header="dialogTitle"
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    :style="{ width: '65vw' }"
    :breakpoints="{ '960px': '90vw', '640px': '95vw' }"
    :modal="true"
    :dismissableMask="true"
  >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Section: Data Kependudukan -->
      <div class="col-span-1 md:col-span-2">
        <h3 class="text-base font-semibold mb-4 pb-2 border-b">Data Kependudukan</h3>
      </div>

      <!-- NIK -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">NIK <span class="text-red-500">*</span></label>
        <InputText
          v-model="form.nik"
          :disabled="isEditMode"
          placeholder="16 digit NIK"
          class="w-full"
          maxlength="16"
        />
      </div>

      <!-- No KK -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          No. KK
          <span class="text-gray-400 text-xs">(Opsional)</span>
        </label>
        <InputText
          v-model="form.no_kk"
          placeholder="16 digit No. KK (kosongkan jika belum ada)"
          class="w-full"
          maxlength="16"
        />
      </div>

      <!-- Nama -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Nama Lengkap <span class="text-red-500">*</span></label>
        <InputText v-model="form.name" placeholder="Nama lengkap" class="w-full" />
      </div>

      <!-- Jenis Kelamin -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Jenis Kelamin <span class="text-red-500">*</span></label>
        <Select
          v-model="form.gender"
          :options="genders"
          optionLabel="label"
          optionValue="value"
          placeholder="Pilih jenis kelamin"
          class="w-full"
        />
      </div>

      <!-- Tempat Lahir -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Tempat Lahir <span class="text-red-500">*</span></label>
        <InputText v-model="form.birth_place" placeholder="Kota tempat lahir" class="w-full" />
      </div>

      <!-- Tanggal Lahir -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Tanggal Lahir <span class="text-red-500">*</span></label>
        <DatePicker
          v-model="form.birth_date"
          dateFormat="yy-mm-dd"
          showIcon
          placeholder="Pilih tanggal lahir"
          class="w-full"
        />
      </div>

      <!-- Divider: Data Tambahan -->
      <div class="col-span-1 md:col-span-2">
        <Divider />
        <h3 class="text-base font-semibold mb-4 pb-2 border-b">Data Tambahan</h3>
      </div>

      <!-- Status Kehidupan -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Status Kehidupan</label>
        <Select
          v-model="form.living_status"
          :options="livingStatuses"
          optionLabel="label"
          optionValue="value"
          placeholder="Pilih status"
          class="w-full"
        />
      </div>

      <!-- Status Pernikahan -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Status Pernikahan</label>
        <InputText v-model="form.married_status" placeholder="Belum Kawin / Kawin / Cerai" class="w-full" />
      </div>

      <!-- Agama -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Agama</label>
        <InputText v-model="form.religious" placeholder="Islam / Kristen / Hindu / dll" class="w-full" />
      </div>

      <!-- Pendidikan -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Pendidikan</label>
        <InputText v-model="form.education" placeholder="SD / SMP / SMA / S1 / dll" class="w-full" />
      </div>

      <!-- Pekerjaan -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Pekerjaan</label>
        <InputText v-model="form.occupation" placeholder="Pekerjaan" class="w-full" />
      </div>

      <!-- Golongan Darah -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Golongan Darah</label>
        <Select
          v-model="form.blood_type"
          :options="bloodTypes"
          placeholder="Pilih golongan darah"
          showClear
          class="w-full"
        />
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button
          label="Batal"
          icon="pi pi-times"
          severity="secondary"
          outlined
          @click="emit('update:visible', false); emit('cancel')"
        />
        <Button
          :label="submitButtonLabel"
          :icon="isEditMode ? 'pi pi-save' : 'pi pi-check'"
          severity="success"
          :loading="loading"
          @click="emit('submit')"
        />
      </div>
    </template>
  </Dialog>
</template>