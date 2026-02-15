<script setup lang="ts">
import { onBeforeMount } from 'vue';
import { useUserManagement } from './composables/useUserManagement';
import UserForm from './components/UserForm.vue';
import UserDetail from './components/UserDetail.vue';
import UserTable from './components/UserTable.vue';

const {
  users,
  jabatans,
  loading,
  loadingDetail,
  form,
  isEditMode,
  formVisible,
  detailVisible,
  selectedUser,
  filters,
  dialogTitle,
  submitButtonLabel,
  loadUsers,
  loadJabatan,
  loadUserDetail,
  openCreateDialog,
  openEditDialog,
  submitForm,
  toggleStatus,
  resetForm,
  clearFilter,
} = useUserManagement();

onBeforeMount(() => {
  loadUsers();
  loadJabatan();
});

async function handleViewUser(user: any) {
  detailVisible.value = true;
  await loadUserDetail(user.id);
}

async function handleEditFromDetail(userId: number) {
  detailVisible.value = false;
  await openEditDialog(userId);
}
</script>

<template>
  <div class="card">
    <div class="font-semibold text-xl mb-4">User Management</div>
    <ConfirmPopup />

    <!-- User Form Dialog -->
    <UserForm
      v-model:visible="formVisible"
      :loading="loading"
      :is-edit-mode="isEditMode"
      :form="form"
      :jabatans="jabatans"
      :dialog-title="dialogTitle"
      :submit-button-label="submitButtonLabel"
      @submit="submitForm"
      @cancel="resetForm"
    />

    <!-- User Detail Drawer -->
    <UserDetail
      v-model:visible="detailVisible"
      :loading="loadingDetail"
      :user="selectedUser"
      @edit="handleEditFromDetail"
    />

    <!-- User Table -->
    <UserTable
      :users="users"
      :loading="loading"
      v-model:filters="filters"
      @create="openCreateDialog"
      @view="handleViewUser"
      @toggle-status="toggleStatus"
      @clear-filter="clearFilter"
    />
  </div>
</template>