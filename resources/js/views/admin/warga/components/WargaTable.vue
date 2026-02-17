<script setup lang="ts">
import { ref, watch } from 'vue';
import type { Warga } from '../composables/useWargaManagement';

interface Props {
  wargas: Warga[];
  loading: boolean;
  filters: any;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:filters': [value: any];
  'create': [];
  'view': [warga: Warga];
  'edit': [wargaId: number];
  'delete': [wargaId: number, event: Event];
  'import': [];
  'clearFilter': [];
}>();

// Local copy agar tidak mutasi prop langsung
const localFilters = ref({ ...props.filters });

// Sync dari parent (misal saat clearFilter)
watch(() => props.filters, (val) => {
  localFilters.value = { ...val };
}, { deep: true });

// Sync ke parent saat DataTable update filter internal
function onFilterUpdate(val: any) {
  localFilters.value = val;
  emit('update:filters', val);
}

const genders = ['L', 'P'];
const livingStatuses = ['hidup', 'meninggal', 'pindah', 'tidak_diketahui'];

function getGenderLabel(gender: string) {
  return gender === 'L' ? 'Laki-laki' : 'Perempuan';
}

function getStatusSeverity(status: string) {
  switch (status) {
    case 'hidup': return 'success';
    case 'meninggal': return 'danger';
    case 'pindah': return 'warn';
    case 'tidak_diketahui': return 'secondary';
    default: return undefined;
  }
}
</script>

<template>
  <DataTable
    :value="wargas"
    :paginator="true"
    :rows="10"
    dataKey="id"
    :rowHover="true"
    v-model:filters="localFilters"
    @update:filters="onFilterUpdate"
    filterDisplay="menu"
    :loading="loading"
    :globalFilterFields="['nik', 'name', 'no_kk', 'gender', 'living_status']"
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
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText
              v-model="localFilters.global.value"
              placeholder="Cari warga..."
            />
          </IconField>
          <Button
            icon="pi pi-upload"
            label="Import"
            severity="info"
            @click="emit('import')"
          />
          <Button
            icon="pi pi-plus"
            label="Tambah Warga"
            severity="success"
            @click="emit('create')"
          />
        </div>
      </div>
    </template>

    <template #empty>Tidak ada data warga.</template>
    <template #loading>Memuat data warga...</template>

    <Column field="nik" header="NIK" :showFilterMatchModes="false" style="min-width: 14rem">
      <template #body="{ data }">{{ data.nik }}</template>
      <template #filter="{ filterModel }">
        <InputText v-model="filterModel.value" placeholder="Cari NIK" />
      </template>
    </Column>

    <Column field="name" header="Nama" :showFilterMatchModes="false" style="min-width: 14rem">
      <template #body="{ data }">{{ data.name }}</template>
      <template #filter="{ filterModel }">
        <InputText v-model="filterModel.value" placeholder="Cari nama" />
      </template>
    </Column>

    <Column field="no_kk" header="No. KK" :showFilterMatchModes="false" style="min-width: 14rem">
      <template #body="{ data }">
        <span v-if="data.no_kk">{{ data.no_kk }}</span>
        <Tag v-else value="Belum ada KK" severity="secondary" />
      </template>
      <template #filter="{ filterModel }">
        <InputText v-model="filterModel.value" placeholder="Cari No. KK" />
      </template>
    </Column>

    <Column field="gender" header="Jenis Kelamin" :showFilterMatchModes="false" style="min-width: 10rem">
      <template #body="{ data }">{{ getGenderLabel(data.gender) }}</template>
      <template #filter="{ filterModel }">
        <Select
          v-model="filterModel.value"
          :options="genders"
          placeholder="Pilih gender"
          showClear
        />
      </template>
    </Column>

    <Column field="living_status" header="Status" :showFilterMatchModes="false" style="min-width: 10rem">
      <template #body="{ data }">
        <Tag
          :value="data.living_status ?? '-'"
          :severity="getStatusSeverity(data.living_status ?? '')"
        />
      </template>
      <template #filter="{ filterModel }">
        <Select
          v-model="filterModel.value"
          :options="livingStatuses"
          placeholder="Pilih status"
          showClear
        />
      </template>
    </Column>

    <Column header="Aksi" style="min-width: 9rem">
      <template #body="{ data }">
        <div class="flex gap-1">
          <Button
            icon="pi pi-eye"
            severity="info"
            text rounded
            v-tooltip.top="'Detail'"
            @click="emit('view', data)"
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
  </DataTable>
</template>