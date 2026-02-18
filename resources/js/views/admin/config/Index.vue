<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import { useToast } from "primevue/usetoast";
import ConfigService from "@/service/ConfigService";
import type { LurahConfig } from "@/service/ConfigService";

const toast = useToast();

const loading = ref(false);
const loadingSave = ref(false);
const config = ref<LurahConfig | null>(null);

// Form fields
const form = ref({
  name: "",
  province: "",
  city: "",
  district: "",
  pos_code: "",
  address: "",
  contact: "",
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
        name: response.data.name,
        province: response.data.province,
        city: response.data.city,
        district: response.data.district,
        pos_code: response.data.pos_code,
        address: response.data.address ?? "",
        contact: response.data.contact ?? "",
      };
      existingLogo.value = response.data.logo ?? null;
    }
  } catch {
    toast.add({
      severity: "error",
      summary: "Gagal",
      detail: "Gagal memuat konfigurasi",
      life: 3000,
    });
  } finally {
    loading.value = false;
  }
}

// ===================== LOGO =====================
function onLogoSelect(event: any) {
  const file: File = event.files[0];
  if (!file) return;
  logoFile.value = file;
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
    toast.add({
      severity: "success",
      summary: "Berhasil",
      detail: "Logo berhasil dihapus",
      life: 3000,
    });
  } catch {
    toast.add({
      severity: "error",
      summary: "Gagal",
      detail: "Gagal menghapus logo",
      life: 3000,
    });
  }
}

// ===================== SAVE =====================
async function handleSave() {
  loadingSave.value = true;
  try {
    const formData = new FormData();
    formData.append("name", form.value.name);
    formData.append("province", form.value.province);
    formData.append("city", form.value.city);
    formData.append("district", form.value.district);
    formData.append("pos_code", form.value.pos_code);
    formData.append("address", form.value.address);
    formData.append("contact", form.value.contact);

    if (logoFile.value) {
      formData.append("logo", logoFile.value);
    }

    const response = await ConfigService.save(formData);
    config.value = response.data;
    existingLogo.value = response.data.logo ?? null;
    logoFile.value = null;
    logoPreview.value = null;

    toast.add({
      severity: "success",
      summary: "Berhasil",
      detail: "Konfigurasi berhasil disimpan",
      life: 3000,
    });
  } catch (error: any) {
    const message =
      error?.response?.data?.message || "Gagal menyimpan konfigurasi";
    toast.add({
      severity: "error",
      summary: "Gagal",
      detail: message,
      life: 4000,
    });
  } finally {
    loadingSave.value = false;
  }
}

onBeforeMount(() => loadConfig());
</script>

<template>
  <div class="card">
    <Toast />

    <!-- Header -->
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

    <!-- Content -->
    <template v-else>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- ===== FORM (kiri, 2/3) ===== -->
        <div class="lg:col-span-2 flex flex-col gap-5">
          <!-- Provinsi & Kota -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="flex flex-col gap-2">
              <label class="font-semibold text-sm">
                Nama Kelurahan<span class="text-red-500">*</span>
              </label>
              <InputText
                v-model="form.name"
                placeholder="Contoh: Sukamaju"
                class="w-full"
              />
            </div>
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
                placeholder="Contoh: Bandung"
                class="w-full"
              />
            </div>

            <!-- Kecamatan & Kontak -->
            <div class="flex flex-col gap-2">
              <label class="font-semibold text-sm">
                Kecamatan <span class="text-red-500">*</span>
              </label>
              <InputText
                v-model="form.district"
                placeholder="Contoh: Coblong"
                class="w-full"
              />
            </div>

            <div class="flex flex-col gap-2">
              <label class="font-semibold text-sm">
                Kontak <span class="text-red-500">*</span>
              </label>
              <InputMask
                v-model="form.contact"
                mask="(999) 9999-9999"
                placeholder="Contoh: (022) 1234-5678"
                :unmask="true"
                class="w-full"
              />
            </div>

            <!-- Kode Pos & (spacer) -->
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

            <!-- Alamat Lengkap -->
            <div class="flex flex-col gap-2 md:col-span-2">
              <label class="font-semibold text-sm">
                Alamat Lengkap <span class="text-red-500">*</span>
              </label>
              <Textarea
                v-model="form.address"
                rows="3"
                autoResize
                placeholder="Contoh: Jl. Sukamaju No. 10 RT 01 RW 02"
                class="w-full"
              />
            </div>
          </div>
        </div>

        <!-- ===== LOGO (kanan, 1/3) ===== -->
        <div class="flex flex-col gap-4">
          <div>
            <label class="font-semibold text-sm block mb-1">Logo Surat</label>
            <p class="text-xs text-gray-400">
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

          <!-- Upload -->
          <FileUpload
            mode="basic"
            accept="image/*"
            :maxFileSize="2097152"
            chooseLabel="Pilih Logo"
            :auto="false"
            @select="onLogoSelect"
            @clear="onLogoClear"
            class="w-full"
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

      <!-- ===== FOOTER: Tombol Simpan ===== -->
      <Divider class="mt-6" />
      <div class="flex justify-end pt-2">
        <Button
          icon="pi pi-save"
          label="Simpan Konfigurasi"
          severity="success"
          :loading="loadingSave"
          @click="handleSave"
        />
      </div>
    </template>
  </div>
</template>
