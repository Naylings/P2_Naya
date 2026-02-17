<script setup lang="ts">
import { ref, watch } from 'vue';
import type { Family } from '../composables/useFamilyManagement';

interface Props {
  families: Family[];
  loading: boolean;
  filters: any;
  expandedRows: Record<number, boolean>;
  familyDetail: Record<number, Family>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:filters': [value: any];
  'update:expandedRows': [value: any];
  'create': [];
  'edit': [familyId: number];
  'delete': [familyId: number, event: Event];
  'manageMembers': [family: Family];
  'rowExpand': [event: any];
  'clearFilter': [];
}>();

// Local copy agar tidak mutasi prop langsung
const localFilters = ref({ ...props.filters });
const localExpandedRows = ref({ ...props.expandedRows });

watch(() => props.filters, (val) => {
  localFilters.value = { ...val };
}, { deep: true });

watch(() => props.expandedRows, (val) => {
  localExpandedRows.value = { ...val };
}, { deep: true });

function onFilterUpdate(val: any) {
  localFilters.value = val;
  emit('update:filters', val);
}

function onExpandedRowsUpdate(val: any) {
  localExpandedRows.value = val;
  emit('update:expandedRows', val);
}
</script>

<template>
  <DataTable
    :value="families"
    :paginator="true"
    :rows="10"
    dataKey="id"
    :rowHover="true"
    v-model:filters="localFilters"
    @update:filters="onFilterUpdate"
    v-model:expandedRows="localExpandedRows"
    @update:expandedRows="onExpandedRowsUpdate"
    filterDisplay="menu"
    :loading="loading"
    :globalFilterFields="['no_kk', 'rt_no', 'rw_no', 'family_head_name']"
    showGridlines
    @rowExpand="emit('rowExpand', $event)"
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
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText
              v-model="localFilters.global.value"
              placeholder="Cari keluarga..."
            />
          </IconField>
          <Button
            icon="pi pi-plus"
            label="Tambah Keluarga"
            severity="success"
            @click="emit('create')"
          />
        </div>
      </div>
    </template>

    <template #empty>Tidak ada data keluarga.</template>
    <template #loading>Memuat data keluarga...</template>

    <!-- Expand column -->
    <Column expander style="width: 3rem" />

    <Column field="no_kk" header="No. KK" :showFilterMatchModes="false" style="min-width: 14rem">
      <template #body="{ data }">
        <span class="font-mono font-medium">{{ data.no_kk }}</span>
      </template>
      <template #filter="{ filterModel }">
        <InputText v-model="filterModel.value" placeholder="Cari No. KK" />
      </template>
    </Column>

    <Column field="rt_no" header="RT" :showFilterMatchModes="false" style="min-width: 7rem">
      <template #body="{ data }">{{ data.rt_no }}</template>
      <template #filter="{ filterModel }">
        <InputText v-model="filterModel.value" placeholder="Cari RT" />
      </template>
    </Column>

    <Column field="rw_no" header="RW" :showFilterMatchModes="false" style="min-width: 7rem">
      <template #body="{ data }">{{ data.rw_no }}</template>
      <template #filter="{ filterModel }">
        <InputText v-model="filterModel.value" placeholder="Cari RW" />
      </template>
    </Column>

    <Column field="family_head_name" header="Kepala Keluarga" :showFilterMatchModes="false" style="min-width: 14rem">
      <template #body="{ data }">
        <span v-if="data.family_head_name && data.family_head_name !== '-'">
          <i class="pi pi-user text-blue-500 mr-1" />
          {{ data.family_head_name }}
        </span>
        <Tag v-else value="Belum ditentukan" severity="warn" />
      </template>
      <template #filter="{ filterModel }">
        <InputText v-model="filterModel.value" placeholder="Cari kepala keluarga" />
      </template>
    </Column>

    <Column field="members_count" header="Anggota" style="min-width: 7rem; text-align: center">
      <template #body="{ data }">
        <Badge :value="data.members_count ?? 0" severity="info" />
      </template>
    </Column>

    <Column header="Aksi" style="min-width: 10rem">
      <template #body="{ data }">
        <div class="flex gap-1">
          <Button
            icon="pi pi-users"
            severity="info"
            text rounded
            v-tooltip.top="'Kelola Anggota'"
            @click="emit('manageMembers', data)"
          />
          <Button
            icon="pi pi-pencil"
            severity="warn"
            text rounded
            v-tooltip.top="'Edit'"
            @click="emit('edit', data.id)"
          />
          <Button
            icon="pi pi-trash"
            severity="danger"
            text rounded
            v-tooltip.top="'Hapus'"
            @click="emit('delete', data.id, $event)"
          />
        </div>
      </template>
    </Column>

    <!-- Expand row content: daftar anggota -->
    <template #expansion="{ data }">
      <div class="p-4">
        <div v-if="!familyDetail[data.id]" class="flex items-center gap-2 text-gray-500">
          <i class="pi pi-spin pi-spinner" />
          <span class="text-sm">Memuat anggota...</span>
        </div>
        <div v-else>
          <div class="flex justify-between items-center mb-3">
            <p class="font-semibold text-sm">
              Anggota Keluarga — KK {{ data.no_kk }}
            </p>
            <p class="text-xs text-gray-500">
              {{ familyDetail[data.id].members?.length ?? 0 }} anggota
            </p>
          </div>

          <div v-if="!familyDetail[data.id].members?.length" class="text-gray-500 text-sm">
            Belum ada anggota terdaftar.
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
            <div
              v-for="member in familyDetail[data.id].members"
              :key="member.id"
              class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg border"
            >
              <div
                class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                :class="member.is_head ? 'bg-blue-500' : 'bg-gray-400'"
              >
                {{ member.name.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ member.name }}</p>
                <p class="text-xs text-gray-500">{{ member.nik }}</p>
              </div>
              <Tag v-if="member.is_head" value="KK" severity="info" class="text-xs" />
            </div>
          </div>

          <div class="mt-3 pt-3 border-t">
            <p class="text-xs text-gray-500">
              <i class="pi pi-map-marker mr-1" />
              {{ familyDetail[data.id].address }}
            </p>
          </div>
        </div>
      </div>
    </template>
  </DataTable>
</template>