<script setup lang="ts">
import type { User } from '../composables/useUserManagement';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

interface Props {
  visible: boolean;
  loading: boolean;
  user: User | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  'update:visible': [value: boolean];
  'edit': [userId: number];
}>();

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

function handleEdit() {
  if (!props.user) return;
  emit('edit', props.user.id);
}
</script>

<template>
  <Drawer
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    position="right"
    header="User Details"
    style="width: 30rem"
  >
    <template v-if="loading">
      <div class="flex justify-center items-center h-full">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
      </div>
    </template>

    <template v-else-if="user">
      <div class="flex flex-col gap-6">
        <!-- Header with Status -->
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-2xl font-bold mb-2">{{ user.name }}</h3>
            <p class="text-gray-500 text-sm">{{ user.email }}</p>
          </div>
          <Tag :value="user.status" :severity="getSeverity(user.status)" />
        </div>

        <div class="border-t pt-4"></div>

        <!-- User Information Grid -->
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-600">NIP</label>
            <p class="text-base mt-1">{{ user.nip }}</p>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-600">NIK</label>
            <p class="text-base mt-1">{{ user.nik }}</p>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-600">Jabatan</label>
            <p class="text-base mt-1">{{ user.jabatan }}</p>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-600">No. HP</label>
            <p class="text-base mt-1">{{ user.no_hp }}</p>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-600">
              Tanggal Lahir
            </label>
            <p class="text-base mt-1">
              {{ dayjs(user.birth_date).locale('id').format('DD MMMM YYYY') }}
            </p>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-600">Alamat</label>
            <p class="text-base mt-1">{{ user.address }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="border-t pt-4 flex justify-end gap-2 mt-auto">
          <Button
            label="Close"
            severity="secondary"
            outlined
            @click="emit('update:visible', false)"
          />
          <Button
            label="Edit User"
            icon="pi pi-pencil"
            severity="warn"
            @click="handleEdit"
          />
        </div>
      </div>
    </template>
  </Drawer>
</template>