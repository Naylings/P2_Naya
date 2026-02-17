<script setup lang="ts">
import type { Warga } from '../composables/useWargaManagement';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

interface Props {
  visible: boolean;
  loading: boolean;
  warga: Warga | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:visible': [value: boolean];
  'edit': [wargaId: number];
}>();

function getStatusSeverity(status: string) {
  switch (status) {
    case 'hidup': return 'success';
    case 'meninggal': return 'danger';
    case 'pindah': return 'warn';
    case 'tidak_diketahui': return 'secondary';
    default: return undefined;
  }
}

function getGenderLabel(gender: string) {
  return gender === 'L' ? 'Laki-laki' : 'Perempuan';
}
</script>

<template>
  <Drawer
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    position="right"
    header="Detail Warga"
    style="width: 30rem"
  >
    <!-- Loading -->
    <template v-if="loading">
      <div class="flex justify-center items-center h-full">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem" />
      </div>
    </template>

    <!-- Content -->
    <template v-else-if="warga">
      <div class="flex flex-col gap-6">

        <!-- Header -->
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-2xl font-bold mb-1">{{ warga.name }}</h3>
            <p class="text-gray-500 text-sm">NIK: {{ warga.nik }}</p>
          </div>
          <Tag
            :value="warga.living_status ?? '-'"
            :severity="getStatusSeverity(warga.living_status ?? '')"
          />
        </div>

        <div class="border-t pt-1" />

        <!-- KK Info -->
        <div class="bg-gray-50 rounded-lg p-3">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kartu Keluarga</label>
          <p v-if="warga.no_kk" class="text-base font-medium mt-1">{{ warga.no_kk }}</p>
          <Tag v-else value="Belum terdaftar di KK manapun" severity="secondary" class="mt-1" />
        </div>

        <!-- Data Pribadi -->
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-600">Jenis Kelamin</label>
            <p class="text-base mt-1">{{ getGenderLabel(warga.gender) }}</p>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-600">Tempat, Tanggal Lahir</label>
            <p class="text-base mt-1">
              {{ warga.birth_place }},
              {{ dayjs(warga.birth_date).locale('id').format('DD MMMM YYYY') }}
            </p>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-600">Agama</label>
            <p class="text-base mt-1">{{ warga.religious ?? '-' }}</p>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-600">Pendidikan</label>
            <p class="text-base mt-1">{{ warga.education ?? '-' }}</p>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-600">Status Pernikahan</label>
            <p class="text-base mt-1">{{ warga.married_status ?? '-' }}</p>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-600">Pekerjaan</label>
            <p class="text-base mt-1">{{ warga.occupation ?? '-' }}</p>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-600">Golongan Darah</label>
            <p class="text-base mt-1">{{ warga.blood_type ?? '-' }}</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="border-t pt-4 flex justify-end gap-2 mt-auto">
          <Button
            label="Tutup"
            severity="secondary"
            outlined
            @click="emit('update:visible', false)"
          />
          <Button
            label="Edit"
            icon="pi pi-pencil"
            severity="warn"
            @click="emit('edit', warga.id)"
          />
        </div>
      </div>
    </template>
  </Drawer>
</template>