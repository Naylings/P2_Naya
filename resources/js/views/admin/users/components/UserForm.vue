<script setup lang="ts">
import type { UserForm } from '../composables/useUserManagement';

interface Props {
  visible: boolean;
  loading: boolean;
  isEditMode: boolean;
  form: UserForm;
  jabatans: any[];
  dialogTitle: string;
  submitButtonLabel: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  'update:visible': [value: boolean];
  'submit': [];
  'cancel': [];
}>();

const statuses = ['active', 'inactive'];

function handleCancel() {
  emit('update:visible', false);
  emit('cancel');
}

function handleSubmit() {
  emit('submit');
}
</script>

<template>
  <Dialog
    :header="dialogTitle"
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    :breakpoints="{ '960px': '90vw', '640px': '95vw' }"
    :style="{ width: '60vw' }"
    :modal="true"
    :dismissableMask="true"
  >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Account Information -->
      <div class="col-span-1 md:col-span-2">
        <h3 class="text-lg font-semibold mb-4 pb-2 border-b">
          Account Information
        </h3>
      </div>

      <!-- Email -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          Email <span class="text-red-500">*</span>
        </label>
        <InputText
          v-model="form.email"
          :disabled="isEditMode"
          placeholder="user@example.com"
          class="w-full"
        />
      </div>

      <!-- Jabatan -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          Jabatan <span class="text-red-500">*</span>
        </label>
        <Select
          v-model="form.jabatan_id"
          :options="jabatans"
          optionLabel="name"
          optionValue="id"
          placeholder="Select Jabatan"
          class="w-full"
        />
      </div>

      <!-- Password -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          Password
          <span v-if="!isEditMode" class="text-red-500">*</span>
          <span v-else class="text-gray-500 text-xs">
            (Leave blank to keep current)
          </span>
        </label>
        <Password
          v-model="form.password"
          :toggleMask="true"
          fluid
          placeholder="Enter password"
          :feedback="false"
        />
      </div>

      <!-- Confirm Password -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          Confirm Password
          <span v-if="!isEditMode" class="text-red-500">*</span>
        </label>
        <Password
          v-model="form.password_confirmation"
          :toggleMask="true"
          fluid
          placeholder="Confirm password"
          :feedback="false"
        />
      </div>

      <!-- DIVIDER -->
      <div class="col-span-1 md:col-span-2">
        <Divider />
        <h3 class="text-lg font-semibold mb-4 pb-2 border-b">
          Personal Information
        </h3>
      </div>

      <!-- NIP -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          NIP <span class="text-red-500">*</span>
        </label>
        <InputText v-model="form.nip" placeholder="Enter NIP" class="w-full" />
      </div>

      <!-- NIK -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          NIK <span class="text-red-500">*</span>
        </label>
        <InputText v-model="form.nik" placeholder="Enter NIK" class="w-full" />
      </div>

      <!-- Nama -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">
          Nama Lengkap <span class="text-red-500">*</span>
        </label>
        <InputText
          v-model="form.name"
          placeholder="Enter full name"
          class="w-full"
        />
      </div>

      <!-- No HP -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">No. HP</label>
        <InputText
          v-model="form.no_hp"
          placeholder="Enter phone number"
          class="w-full"
        />
      </div>

      <!-- Tanggal Lahir -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Tanggal Lahir</label>
        <DatePicker
          v-model="form.birth_date"
          dateFormat="yy-mm-dd"
          showIcon
          placeholder="Select birth date"
          class="w-full"
        />
      </div>

      <!-- Status -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Status</label>
        <Select
          v-model="form.status"
          :options="statuses"
          placeholder="Select status"
          class="w-full"
        />
      </div>

      <!-- Alamat - Full Width -->
      <div class="col-span-1 md:col-span-2 flex flex-col gap-2">
        <label class="font-semibold text-sm">Alamat</label>
        <Textarea
          v-model="form.address"
          rows="3"
          placeholder="Enter full address"
          class="w-full"
        />
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button
          label="Cancel"
          icon="pi pi-times"
          severity="secondary"
          outlined
          @click="handleCancel"
        />
        <Button
          :label="submitButtonLabel"
          :icon="isEditMode ? 'pi pi-save' : 'pi pi-check'"
          severity="success"
          :loading="loading"
          @click="handleSubmit"
        />
      </div>
    </template>
  </Dialog>
</template>