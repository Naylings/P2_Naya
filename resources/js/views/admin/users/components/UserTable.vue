<script setup lang="ts">
import type { User } from '../composables/useUserManagement';
import { useConfirm } from 'primevue';

interface Props {
  users: User[];
  loading: boolean;
  filters: any;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  'update:filters': [value: any];
  'create': [];
  'view': [user: User];
  'toggleStatus': [userId: number];
  'clearFilter': [];
}>();

const confirmPopup = useConfirm();
const statuses = ['active', 'inactive'];

function getSeverity(status: string) {
  switch (status) {
    case 'active':
      return 'success';
    case 'inactive':
      return 'danger';
    default:
      return undefined;
  }
}

function confirmToggleStatus(event: Event, user: User) {
  const isActive = user.status === 'active';

  confirmPopup.require({
    target: event.currentTarget as HTMLElement,
    message: `Are you sure you want to ${isActive ? 'deactivate' : 'activate'} this user?`,
    icon: 'pi pi-exclamation-triangle',
    rejectProps: {
      label: 'Cancel',
      severity: 'secondary',
      outlined: true,
    },
    acceptProps: {
      label: 'Yes',
      severity: isActive ? 'danger' : 'success',
    },
    accept: () => {
      emit('toggleStatus', user.id);
    },
  });
}
</script>

<template>
  <DataTable
    :value="users"
    :paginator="true"
    :rows="10"
    dataKey="id"
    :rowHover="true"
    :modelValue="filters"
    @update:modelValue="emit('update:filters', $event)"
    filterDisplay="menu"
    :loading="loading"
    :globalFilterFields="['email', 'nip', 'name', 'jabatan', 'no_hp', 'status']"
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
            @click="emit('clearFilter')"
          />
        </div>

        <div class="flex gap-2 items-center">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText
              :modelValue="filters.global.value"
              @update:modelValue="filters.global.value = $event"
              placeholder="Keyword Search"
            />
          </IconField>

          <Button
            icon="pi pi-plus"
            label="New User"
            severity="success"
            @click="emit('create')"
          />
        </div>
      </div>
    </template>

    <template #empty> No users found. </template>
    <template #loading> Loading users data. Please wait. </template>

    <Column
      field="email"
      header="Email"
      :showFilterMatchModes="false"
      style="min-width: 12rem"
    >
      <template #body="{ data }">{{ data.email }}</template>
      <template #filter="{ filterModel }">
        <InputText
          v-model="filterModel.value"
          type="text"
          placeholder="Search by email"
        />
      </template>
    </Column>

    <Column
      field="nip"
      header="NIP"
      :showFilterMatchModes="false"
      style="min-width: 10rem"
    >
      <template #body="{ data }">{{ data.nip }}</template>
      <template #filter="{ filterModel }">
        <InputText
          v-model="filterModel.value"
          type="text"
          placeholder="Search by NIP"
        />
      </template>
    </Column>

    <Column
      field="name"
      header="Nama"
      style="min-width: 12rem"
      :showFilterMatchModes="false"
    >
      <template #body="{ data }">{{ data.name }}</template>
      <template #filter="{ filterModel }">
        <InputText
          v-model="filterModel.value"
          type="text"
          placeholder="Search by name"
        />
      </template>
    </Column>

    <Column
      field="jabatan"
      header="Jabatan"
      style="min-width: 12rem"
      :showFilterMatchModes="false"
    >
      <template #body="{ data }">{{ data.jabatan }}</template>
      <template #filter="{ filterModel }">
        <InputText
          v-model="filterModel.value"
          type="text"
          placeholder="Search by jabatan"
        />
      </template>
    </Column>

    <Column
      field="no_hp"
      header="No. HP"
      style="min-width: 10rem"
      :showFilterMatchModes="false"
    >
      <template #body="{ data }">{{ data.no_hp }}</template>
      <template #filter="{ filterModel }">
        <InputText
          v-model="filterModel.value"
          type="text"
          placeholder="Search by phone"
        />
      </template>
    </Column>

    <Column
      field="status"
      header="Status"
      :filterMenuStyle="{ width: '14rem' }"
      style="min-width: 10rem"
      :showFilterMatchModes="false"
    >
      <template #body="{ data }">
        <Tag :value="data.status" :severity="getSeverity(data.status)" />
      </template>
      <template #filter="{ filterModel }">
        <Select
          v-model="filterModel.value"
          :options="statuses"
          placeholder="Select Status"
          showClear
        />
      </template>
    </Column>

    <Column header="Action" style="max-width: 10rem">
      <template #body="{ data }">
        <div class="flex gap-2">
          <Button
            icon="pi pi-eye"
            severity="info"
            text
            rounded
            @click="emit('view', data)"
          />
          <Button
            :icon="
              data.status == 'active'
                ? 'pi pi-times-circle'
                : 'pi pi-check-circle'
            "
            :severity="data.status == 'active' ? 'danger' : 'success'"
            text
            rounded
            @click="confirmToggleStatus($event, data)"
          />
        </div>
      </template>
    </Column>
  </DataTable>
</template>