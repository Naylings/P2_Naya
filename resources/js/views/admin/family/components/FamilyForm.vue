<script setup lang="ts">
import type { FamilyForm } from '../composables/useFamilyManagement';
import type { Rukun } from '@/service/RukunService';

interface Props {
  visible: boolean;
  loading: boolean;
  isEditMode: boolean;
  form: FamilyForm;
  rtList: Rukun[];
  rwList: Rukun[];
  dialogTitle: string;
  submitButtonLabel: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:visible': [value: boolean];
  'submit': [];
  'cancel': [];
}>();
</script>

<template>
  <Dialog
    :header="dialogTitle"
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    :style="{ width: '50vw' }"
    :breakpoints="{ '960px': '80vw', '640px': '95vw' }"
    :modal="true"
    :dismissableMask="true"
  >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- No KK -->
      <div class="col-span-1 md:col-span-2 flex flex-col gap-2">
        <label class="font-semibold text-sm">
          No. KK <span class="text-red-500">*</span>
        </label>
        <InputText
          v-model="form.no_kk"
          :disabled="isEditMode"
          placeholder="16 digit Nomor Kartu Keluarga"
          class="w-full"
          maxlength="16"
        />
        <small v-if="isEditMode" class="text-gray-400">
          No. KK tidak dapat diubah setelah dibuat
        </small>
      </div>

      <!-- RT -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">RT <span class="text-red-500">*</span></label>
        <Select
          v-model="form.rt_id"
          :options="rtList"
          optionLabel="no"
          optionValue="id"
          placeholder="Pilih RT"
          class="w-full"
        />
      </div>

      <!-- RW -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">RW <span class="text-red-500">*</span></label>
        <Select
          v-model="form.rw_id"
          :options="rwList"
          optionLabel="no"
          optionValue="id"
          placeholder="Pilih RW"
          class="w-full"
        />
      </div>

      <!-- Alamat -->
      <div class="col-span-1 md:col-span-2 flex flex-col gap-2">
        <label class="font-semibold text-sm">Alamat <span class="text-red-500">*</span></label>
        <Textarea
          v-model="form.address"
          rows="3"
          placeholder="Alamat lengkap sesuai KK"
          class="w-full"
        />
      </div>

      <!-- Info kepala keluarga -->
      <div class="col-span-1 md:col-span-2">
        <Message severity="info" :closable="false">
          <span class="text-sm">
            Kepala keluarga dapat diatur setelah keluarga dibuat, melalui menu
            <strong>Kelola Anggota</strong>.
          </span>
        </Message>
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