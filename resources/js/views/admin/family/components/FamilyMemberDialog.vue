<script setup lang="ts">
import type { Family, FamilyMember, AvailableHead } from '../composables/useFamilyManagement';

interface Props {
  visible: boolean;
  loading: boolean;
  family: Family | null;
  availableHeads: AvailableHead[];
  wargaWithoutFamily: any[];
  selectedNewMemberId: number | null;
  selectedNewHeadId: number | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:visible': [value: boolean];
  'update:selectedNewMemberId': [value: number | null];
  'update:selectedNewHeadId': [value: number | null];
  'addMember': [];
  'removeMember': [memberId: number, event: Event];
  'setHead': [];
}>();
</script>

<template>
  <Dialog
    :header="`Kelola Anggota — KK ${family?.no_kk ?? ''}`"
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    :style="{ width: '60vw' }"
    :breakpoints="{ '960px': '90vw', '640px': '95vw' }"
    :modal="true"
    :dismissableMask="true"
  >
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-10">
      <i class="pi pi-spin pi-spinner" style="font-size: 2rem" />
    </div>

    <div v-else-if="family" class="flex flex-col gap-6">

      <!-- === SET KEPALA KELUARGA === -->
      <div class="border rounded-lg p-4">
        <h4 class="font-semibold text-sm mb-3 flex items-center gap-2">
          <i class="pi pi-star text-yellow-500" />
          Kepala Keluarga
        </h4>

        <div class="flex items-center gap-3 mb-3 p-2 bg-gray-50 rounded-lg">
          <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm">
            {{ family.family_head_name?.charAt(0)?.toUpperCase() ?? '?' }}
          </div>
          <div>
            <p class="text-sm font-medium">
              {{ family.family_head_name && family.family_head_name !== '-'
                ? family.family_head_name
                : 'Belum ada kepala keluarga' }}
            </p>
            <p class="text-xs text-gray-500">Kepala keluarga saat ini</p>
          </div>
        </div>

        <!-- Ganti kepala keluarga -->
        <div v-if="availableHeads.length > 0" class="flex gap-2 items-end">
          <div class="flex-1 flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-600">
              Ganti Kepala Keluarga
              <span class="text-gray-400">(dari anggota keluarga ini)</span>
            </label>
            <Select
              :modelValue="selectedNewHeadId"
              @update:modelValue="emit('update:selectedNewHeadId', $event)"
              :options="availableHeads"
              optionLabel="name"
              optionValue="id"
              placeholder="Pilih dari anggota keluarga"
              showClear
              class="w-full"
            >
              <template #option="{ option }">
                <div class="flex items-center gap-2">
                  <span class="font-medium">{{ option.name }}</span>
                  <span class="text-xs text-gray-400">{{ option.nik }}</span>
                </div>
              </template>
            </Select>
          </div>
          <Button
            icon="pi pi-check"
            label="Tetapkan"
            severity="info"
            :disabled="!selectedNewHeadId"
            @click="emit('setHead')"
          />
        </div>

        <!-- Jika belum ada anggota, tidak bisa set kepala -->
        <div v-else>
          <Message severity="warn" :closable="false">
            <span class="text-sm">
              Tambahkan anggota ke keluarga ini terlebih dahulu sebelum menentukan kepala keluarga.
            </span>
          </Message>
        </div>
      </div>

      <!-- === DAFTAR ANGGOTA === -->
      <div class="border rounded-lg p-4">
        <h4 class="font-semibold text-sm mb-3 flex items-center gap-2">
          <i class="pi pi-users text-blue-500" />
          Daftar Anggota
          <Badge :value="family.members?.length ?? 0" severity="info" />
        </h4>

        <div v-if="!family.members?.length" class="text-gray-500 text-sm text-center py-4">
          Belum ada anggota terdaftar.
        </div>

        <div v-else class="flex flex-col gap-2 max-h-52 overflow-y-auto pr-1">
          <div
            v-for="member in family.members"
            :key="member.id"
            class="flex items-center justify-between p-2 rounded-lg border"
            :class="member.is_head ? 'bg-blue-50 border-blue-200' : 'bg-gray-50'"
          >
            <div class="flex items-center gap-3">
              <div
                class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                :class="member.is_head ? 'bg-blue-500' : 'bg-gray-400'"
              >
                {{ member.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <p class="text-sm font-medium">{{ member.name }}</p>
                <p class="text-xs text-gray-500">{{ member.nik }}</p>
              </div>
              <Tag v-if="member.is_head" value="Kepala" severity="info" class="text-xs" />
            </div>

            <Button
              v-if="!member.is_head"
              icon="pi pi-user-minus"
              severity="danger"
              text rounded size="small"
              v-tooltip.left="'Keluarkan dari KK'"
              @click="emit('removeMember', member.id, $event)"
            />
            <span v-else class="text-xs text-gray-400 italic">tidak bisa dilepas</span>
          </div>
        </div>
      </div>

      <!-- === TAMBAH ANGGOTA === -->
      <div class="border rounded-lg p-4">
        <h4 class="font-semibold text-sm mb-3 flex items-center gap-2">
          <i class="pi pi-user-plus text-green-500" />
          Tambah Anggota
        </h4>

        <div class="flex gap-2 items-end">
          <div class="flex-1 flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-600">
              Warga tanpa KK
              <span class="text-gray-400">({{ wargaWithoutFamily.length }} tersedia)</span>
            </label>
            <Select
              :modelValue="selectedNewMemberId"
              @update:modelValue="emit('update:selectedNewMemberId', $event)"
              :options="wargaWithoutFamily"
              optionLabel="name"
              optionValue="id"
              placeholder="Pilih warga yang belum punya KK"
              filter
              showClear
              class="w-full"
              :emptyMessage="'Tidak ada warga tanpa KK'"
            >
              <template #option="{ option }">
                <div class="flex items-center gap-2">
                  <span class="font-medium">{{ option.name }}</span>
                  <span class="text-xs text-gray-400">{{ option.nik }}</span>
                </div>
              </template>
            </Select>
          </div>
          <Button
            icon="pi pi-plus"
            label="Tambahkan"
            severity="success"
            :disabled="!selectedNewMemberId"
            @click="emit('addMember')"
          />
        </div>

        <small class="text-gray-400 mt-2 block">
          Hanya warga yang belum terdaftar di KK manapun yang dapat ditambahkan.
        </small>
      </div>

    </div>

    <template #footer>
      <Button
        label="Tutup"
        severity="secondary"
        outlined
        @click="emit('update:visible', false)"
      />
    </template>
  </Dialog>
</template>