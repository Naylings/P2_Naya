<script setup lang="ts">
import { onBeforeMount } from 'vue';
import { useWargaManagement } from './composables/useWargaManagement';
import WargaTable from './components/WargaTable.vue';
import WargaForm from './components/WargaForm.vue';
import WargaDetail from './components/WargaDetail.vue';
import WargaImport from './components/WargaImport.vue';

const {
  wargas, loading, loadingDetail,
  form, isEditMode,
  formVisible, detailVisible, importVisible,
  selectedWarga, filters,
  dialogTitle, submitButtonLabel,
  loadWargas, loadWargaDetail,
  openCreateDialog, openEditDialog,
  submitForm, confirmDelete,
  resetForm, clearFilter,
} = useWargaManagement();

onBeforeMount(() => {
  loadWargas();
});

async function handleViewWarga(warga: any) {
  detailVisible.value = true;
  await loadWargaDetail(warga.id);
}

async function handleEditFromDetail(wargaId: number) {
  detailVisible.value = false;
  await openEditDialog(wargaId);
}
</script>

<template>
  <div class="card">
    <div class="font-semibold text-xl mb-4">Data Warga</div>

    <ConfirmPopup />
    <Toast />

    <!-- Import Dialog -->
    <WargaImport
      v-model:visible="importVisible"
      @imported="loadWargas"
    />

    <!-- Form Dialog -->
    <WargaForm
      v-model:visible="formVisible"
      :loading="loading"
      :is-edit-mode="isEditMode"
      :form="form"
      :dialog-title="dialogTitle"
      :submit-button-label="submitButtonLabel"
      @submit="submitForm"
      @cancel="resetForm"
    />

    <!-- Detail Drawer -->
    <WargaDetail
      v-model:visible="detailVisible"
      :loading="loadingDetail"
      :warga="selectedWarga"
      @edit="handleEditFromDetail"
    />

    <!-- Table -->
    <WargaTable
      :wargas="wargas"
      :loading="loading"
      v-model:filters="filters"
      @create="openCreateDialog"
      @view="handleViewWarga"
      @edit="openEditDialog"
      @delete="confirmDelete"
      @import="importVisible = true"
      @clear-filter="clearFilter"
    />
  </div>
</template>