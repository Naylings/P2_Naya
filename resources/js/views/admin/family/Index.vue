<script setup lang="ts">
import { onBeforeMount } from 'vue';
import { useFamilyManagement } from './composables/useFamilyManagement';
import FamilyTable from './components/FamilyTable.vue';
import FamilyForm from './components/FamilyForm.vue';
import FamilyMemberDialog from './components/FamilyMemberDialog.vue';

const {
  families, rtList, rwList,
  loading, loadingMembers,
  form, isEditMode,
  formVisible, memberVisible,
  expandedRows, familyDetail,
  selectedFamily, availableHeads, wargaWithoutFamily,
  selectedNewMemberId, selectedNewHeadId,
  filters,
  dialogTitle, submitButtonLabel,
  loadFamilies, loadRukun,
  onRowExpand, openCreateDialog, openEditDialog,
  submitForm, confirmDelete,
  openMemberDialog, addMember, confirmRemoveMember, setHead,
  resetForm, clearFilter,
} = useFamilyManagement();

onBeforeMount(() => {
  loadFamilies();
  loadRukun();
});
</script>

<template>
  <div class="card">
    <div class="font-semibold text-xl mb-4">Data Keluarga</div>

    <ConfirmPopup />
    <Toast />

    <!-- Form Dialog -->
    <FamilyForm
      v-model:visible="formVisible"
      :loading="loading"
      :is-edit-mode="isEditMode"
      :form="form"
      :rt-list="rtList"
      :rw-list="rwList"
      :dialog-title="dialogTitle"
      :submit-button-label="submitButtonLabel"
      @submit="submitForm"
      @cancel="resetForm"
    />

    <!-- Member Management Dialog -->
    <FamilyMemberDialog
      v-model:visible="memberVisible"
      :loading="loadingMembers"
      :family="selectedFamily"
      :available-heads="availableHeads"
      :warga-without-family="wargaWithoutFamily"
      v-model:selectedNewMemberId="selectedNewMemberId"
      v-model:selectedNewHeadId="selectedNewHeadId"
      @addMember="addMember"
      @removeMember="confirmRemoveMember"
      @setHead="setHead"
    />

    <!-- Table -->
    <FamilyTable
      :families="families"
      :loading="loading"
      v-model:filters="filters"
      v-model:expandedRows="expandedRows"
      :family-detail="familyDetail"
      @create="openCreateDialog"
      @edit="openEditDialog"
      @delete="confirmDelete"
      @manage-members="openMemberDialog"
      @row-expand="onRowExpand"
      @clear-filter="clearFilter"
    />
  </div>
</template>