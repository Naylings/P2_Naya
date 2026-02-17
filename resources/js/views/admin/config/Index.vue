<script setup lang="ts">
import { ref, onBeforeMount } from 'vue';
import { useToast } from 'primevue/usetoast';
import ConfigService from '@/service/ConfigService';
import type { LurahConfig } from '@/service/ConfigService';

const toast = useToast();

const loading = ref(false);
const loadingSave = ref(false);
const config = ref<LurahConfig | null>(null);

// Form fields
const form = ref({
  name: '',
  province: '',
  city: '',
  district: '',
  pos_code: '',
});

// Logo state
const logoFile = ref<File | null>(null);
const logoPreview = ref<string | null>(null);
const existingLogo = ref<string | null>(null);

// ===================== LOAD =====================
async function loadConfig() {
  loading.value = true;
  try {
    const response = await ConfigService.get();
    if (response.data) {
      config.value = response.data;
      form.value = {
        name:     response.data.name,
        province: response.data.province,
        city:     response.data.city,
        district: response.data.district,
        pos_code: response.data.pos_code,
      };
      existingLogo.value = response.data.logo ?? null;
    }
  } catch {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat konfigurasi', life: 3000 });
  } finally {
    loading.value = false;
  }
}

// ===================== LOGO =====================
function onLogoSelect(event: any) {
  const file: File = event.files[0];
  if (!file) return;

  logoFile.value = file;

  // Preview
  const reader = new FileReader();
  reader.onload = (e) => {
    logoPreview.value = e.target?.result as string;
  };
  reader.readAsDataURL(file);
}

function onLogoClear() {
  logoFile.value = null;
  logoPreview.value = null;
}

async function handleDeleteLogo() {
  try {
    await ConfigService.deleteLogo();
    existingLogo.value = null;
    logoFile.value = null;
    logoPreview.value = null;
    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Logo berhasil dihapus', life: 3000 });
  } catch {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menghapus logo', life: 3000 });
  }
}

// ===================== SAVE =====================
async function handleSave() {
  loadingSave.value = true;
  try {
    const formData = new FormData();
    formData.append('name',     form.value.name);
    formData.append('province', form.value.province);
    formData.append('city',     form.value.city);
    formData.append('district', form.value.district);
    formData.append('pos_code', form.value.pos_code);

    if (logoFile.value) {
      formData.append('logo', logoFile.value);
    }

    const response = await ConfigService.save(formData);

    config.value = response.data;
    existingLogo.value = response.data.logo ?? null;
    logoFile.value = null;
    logoPreview.value = null;

    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Konfigurasi berhasil disimpan', life: 3000 });
  } catch (error: any) {
    const message = error?.response?.data?.message || 'Gagal menyimpan konfigurasi';
    toast.add({ severity: 'error', summary: 'Gagal', detail: message, life: 4000 });
  } finally {
    loadingSave.value = false;
  }
}

onBeforeMount(() => loadConfig());


function onFileInputChange(event: Event) {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];
  if (!file) return;

  // Validasi ukuran
  if (file.size > 2 * 1024 * 1024) {
    toast.add({ severity: 'warn', summary: 'File terlalu besar', detail: 'Ukuran logo maksimal 2MB', life: 3000 });
    input.value = '';
    return;
  }

  // Validasi tipe
  const allowed = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/jpg'];
  if (!allowed.includes(file.type)) {
    toast.add({ severity: 'warn', summary: 'Format tidak didukung', detail: 'Gunakan JPG, PNG, atau SVG', life: 3000 });
    input.value = '';
    return;
  }

  logoFile.value = file;

  const reader = new FileReader();
  reader.onload = (e) => {
    logoPreview.value = e.target?.result as string;
  };
  reader.readAsDataURL(file);

  // Reset input agar bisa pilih file yang sama lagi
  input.value = '';
}


function triggerFileInput() {
  fileInputRef.value?.click();
}
</script>

<template>
  <div class="card">
    <Toast />

    <div class="flex justify-between items-center mb-6">
      <div>
        <div class="font-semibold text-xl">Konfigurasi Aplikasi</div>
        <p class="text-gray-500 text-sm mt-1">
          Pengaturan data kelurahan yang ditampilkan di aplikasi
        </p>
      </div>
      <div v-if="config?.updated_at" class="text-xs text-gray-400">
        Terakhir diubah: {{ config.updated_at }}
      </div>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="flex flex-col gap-4">
      <Skeleton height="2.5rem" />
      <Skeleton height="2.5rem" />
      <Skeleton height="2.5rem" />
      <Skeleton height="2.5rem" />
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- ===== FORM (kiri, 2/3) ===== -->
      <div class="lg:col-span-2 flex flex-col gap-5">

        <div class="flex flex-col gap-2">
          <label class="font-semibold text-sm">
            Nama Kelurahan <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="form.name"
            placeholder="Contoh: Sukamaju"
            class="w-full"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="flex flex-col gap-2">
            <label class="font-semibold text-sm">
              Provinsi <span class="text-red-500">*</span>
            </label>
            <InputText
              v-model="form.province"
              placeholder="Contoh: Jawa Barat"
              class="w-full"
            />
          </div>

          <div class="flex flex-col gap-2">
            <label class="font-semibold text-sm">
              Kota / Kabupaten <span class="text-red-500">*</span>
            </label>
            <InputText
              v-model="form.city"
              placeholder="Contoh: Kota Bandung"
              class="w-full"
            />
          </div>

          <div class="flex flex-col gap-2">
            <label class="font-semibold text-sm">
              Kecamatan <span class="text-red-500">*</span>
            </label>
            <InputText
              v-model="form.district"
              placeholder="Contoh: Kecamatan Coblong"
              class="w-full"
            />
          </div>

          <div class="flex flex-col gap-2">
            <label class="font-semibold text-sm">
              Kode Pos <span class="text-red-500">*</span>
            </label>
            <InputText
              v-model="form.pos_code"
              placeholder="Contoh: 40132"
              maxlength="10"
              class="w-full"
            />
          </div>
        </div>

        <!-- Save button -->
        <div class="flex justify-end pt-2">
          <Button
            icon="pi pi-save"
            label="Simpan Konfigurasi"
            severity="success"
            :loading="loadingSave"
            @click="handleSave"
          />
        </div>
      </div>

      <!-- ===== LOGO (kanan, 1/3) ===== -->
      <div class="flex flex-col gap-4">
        <div>
          <label class="font-semibold text-sm block mb-2">Logo Kelurahan</label>
          <p class="text-xs text-gray-400 mb-3">
            Format: JPG, PNG, SVG. Maks. 2MB.
          </p>
        </div>

        <!-- Preview logo -->
        <div
          class="w-full aspect-square max-w-45 mx-auto rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden bg-gray-50"
        >
          <img
            v-if="logoPreview || existingLogo"
            :src="logoPreview ?? existingLogo ?? ''"
            alt="Logo preview"
            class="w-full h-full object-contain p-2"
          />
          <div v-else class="text-center text-gray-300">
            <i class="pi pi-image" style="font-size: 2.5rem" />
            <p class="text-xs mt-2">Belum ada logo</p>
          </div>
        </div>

        <input
          ref="fileInputRef"
          type="file"
          accept="image/jpeg,image/jpg,image/png,image/svg+xml"
          class="hidden"
          @change="onFileInputChange"
        />

        <Button
          icon="pi pi-upload"
          :label="logoFile ? logoFile.name : 'Pilih Logo'"
          severity="secondary"
          outlined
          class="w-full"
          @click="triggerFileInput"
        />

        <p v-if="logoFile" class="text-xs text-green-600 text-center">
          {{ (logoFile.size / 1024).toFixed(1) }} KB — siap diupload saat simpan
        </p>

        <Button
          v-if="logoPreview"
          icon="pi pi-times"
          label="Batalkan"
          severity="secondary"
          outlined
          size="small"
          @click="onLogoClear"
        />


        <!-- Hapus logo yang sudah ada -->
        <Button
          v-if="existingLogo && !logoPreview"
          icon="pi pi-trash"
          label="Hapus Logo"
          severity="danger"
          outlined
          size="small"
          @click="handleDeleteLogo"
        />

        <!-- Batalkan pilihan baru -->
        <Button
          v-if="logoPreview"
          icon="pi pi-times"
          label="Batalkan"
          severity="secondary"
          outlined
          size="small"
          @click="onLogoClear"
        />
      </div>

    </div>
  </div>
</template>