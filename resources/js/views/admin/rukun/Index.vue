<script setup lang="ts">
import { onBeforeMount } from 'vue';
import { useRukunManagement } from './composables/useRukunManagement';
import { useConfirm } from 'primevue';
import type { Rukun } from '@/service/RukunService';

const {
  rukuns,
  loading,
  form,
  formVisible,
  filters,
  typeOptions,
  loadRukuns,
  openCreateDialog,
  submitForm,
  deleteRukun,
  resetForm,
  clearFilter,
} = useRukunManagement();

const confirmPopup = useConfirm();
const filterTypeOptions = ['RT', 'RW'];

onBeforeMount(() => {
  loadRukuns();
});

function confirmDelete(event: Event, rukun: Rukun) {
  confirmPopup.require({
    target: event.currentTarget as HTMLElement,
    message: `Are you sure you want to delete ${rukun.type} No. ${rukun.no}?`,
    icon: 'pi pi-exclamation-triangle',
    rejectProps: {
      label: 'Cancel',
      severity: 'secondary',
      outlined: true,
    },
    acceptProps: {
      label: 'Delete',
      severity: 'danger',
    },
    accept: () => {
      deleteRukun(rukun.id);
    },
  });
}

function getTypeBadge(type: string) {
  return type === 'RT' ? 'info' : 'success';
}

function handleCancel() {
  formVisible.value = false;
  resetForm();
}
</script>

<template>
  <div class="card">
    <div class="font-semibold text-xl mb-4">Rukun Management (RT/RW)</div>
    <ConfirmPopup />

    <!-- Create Form Dialog -->
    <Dialog
      header="Create New Rukun"
      v-model:visible="formVisible"
      :style="{ width: '450px' }"
      :modal="true"
      :dismissableMask="true"
    >
      <div class="flex flex-col gap-4">
        
        <!-- Type -->
        <div class="flex flex-col gap-2">
          <label class="font-semibold text-sm">
            Type <span class="text-red-500">*</span>
          </label>
          <Select
            v-model="form.type"
            :options="typeOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="Select Type"
            class="w-full"
          />
        </div>

        <!-- No -->
        <div class="flex flex-col gap-2">
          <label class="font-semibold text-sm">
            No <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="form.no"
            type="number"
            placeholder="Enter number (e.g., 1, 12, 123)"
            class="w-full"
          />
          <small class="text-gray-500">
            Number will be automatically formatted to 3 digits (e.g., 001, 012, 123)
          </small>
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
            label="Save"
            icon="pi pi-check"
            severity="success"
            :loading="loading"
            @click="submitForm"
          />
        </div>
      </template>
    </Dialog>

    <!-- Data Table -->
    <DataTable
      :value="rukuns"
      :paginator="true"
      :rows="10"
      dataKey="id"
      :rowHover="true"
      v-model:filters="filters"
      filterDisplay="menu"
      :loading="loading"
      :globalFilterFields="['type', 'no']"
      showGridlines
    >
      <template #header>
        <div class="flex justify-between items-center w-full">
          <div class="flex gap-2">
            <Button
              type="button"
              icon="pi pi-filter-slash"
              label="Clear"
              outlined
              @click="clearFilter"
            />
          </div>

          <div class="flex gap-2 items-center">
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText
                v-model="filters.global.value"
                placeholder="Keyword Search"
              />
            </IconField>

            <Button
              icon="pi pi-plus"
              label="New Rukun"
              severity="success"
              @click="openCreateDialog"
            />
          </div>
        </div>
      </template>

      <template #empty> No rukun found. </template>
      <template #loading> Loading rukun data. Please wait. </template>

      <Column
        field="type"
        header="Type"
        :showFilterMatchModes="false"
        style="min-width: 8rem"
      >
        <template #body="{ data }">
          <Tag :value="data.type" :severity="getTypeBadge(data.type)" />
        </template>
        <template #filter="{ filterModel }">
          <Select
            v-model="filterModel.value"
            :options="filterTypeOptions"
            placeholder="Select Type"
            showClear
          />
        </template>
      </Column>

      <Column
        field="no"
        header="No"
        :showFilterMatchModes="false"
        style="min-width: 10rem"
      >
        <template #body="{ data }">{{ data.no }}</template>
        <template #filter="{ filterModel }">
          <InputText
            v-model="filterModel.value"
            type="text"
            placeholder="Search by number"
          />
        </template>
      </Column>

      <Column header="Action" style="max-width: 8rem">
        <template #body="{ data }">
          <div class="flex gap-2 justify-center">
            <Button
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              @click="confirmDelete($event, data)"
            />
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>