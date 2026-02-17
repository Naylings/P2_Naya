import { ref, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import WargaService from '@/service/WargaService';
import type { Warga, WargaForm } from '@/service/WargaService';
import dayjs from 'dayjs';
import { FilterMatchMode } from '@primevue/core/api';

export type { Warga, WargaForm };

export function useWargaManagement() {
  const toast = useToast();
  const confirm = useConfirm();

  // State
  const wargas = ref<Warga[]>([]);
  const loading = ref(false);
  const loadingDetail = ref(false);

  // Form state
  const isEditMode = ref(false);
  const editWargaId = ref<number | null>(null);
  const form = ref<WargaForm>({
    nik: '',
    no_kk: '',
    name: '',
    gender: '',
    birth_place: '',
    birth_date: null,
    religious: '',
    education: '',
    living_status: 'hidup',
    married_status: '',
    occupation: '',
    blood_type: '',
  });

  // Dialog state
  const formVisible = ref(false);
  const detailVisible = ref(false);
  const importVisible = ref(false);
  const selectedWarga = ref<Warga | null>(null);

  // Filters
  const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    nik: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    no_kk: { value: null, matchMode: FilterMatchMode.CONTAINS },
    gender: { value: null, matchMode: FilterMatchMode.EQUALS },
    living_status: { value: null, matchMode: FilterMatchMode.EQUALS },
  });

  // Computed
  const dialogTitle = computed(() =>
    isEditMode.value ? 'Edit Data Warga' : 'Tambah Warga Baru'
  );

  const submitButtonLabel = computed(() =>
    isEditMode.value ? 'Update' : 'Simpan'
  );

  // Methods
  async function loadWargas() {
    loading.value = true;
    try {
      const response = await WargaService.getAll();
      wargas.value = response.data;
    } catch {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: 'Gagal memuat data warga',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }

  async function loadWargaDetail(id: number) {
    loadingDetail.value = true;
    try {
      const response = await WargaService.getById(id);
      selectedWarga.value = response.data;
    } catch {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: 'Gagal memuat detail warga',
        life: 3000,
      });
      throw new Error('Failed to load');
    } finally {
      loadingDetail.value = false;
    }
  }

  function openCreateDialog() {
    resetForm();
    isEditMode.value = false;
    editWargaId.value = null;
    formVisible.value = true;
  }

  async function openEditDialog(wargaId: number) {
    isEditMode.value = true;
    editWargaId.value = wargaId;
    loading.value = true;
    try {
      const response = await WargaService.getById(wargaId);
      const data = response.data;
      form.value = {
        nik: data.nik,
        no_kk: data.no_kk ?? '',
        name: data.name,
        gender: data.gender,
        birth_place: data.birth_place,
        birth_date: data.birth_date ? new Date(data.birth_date) : null,
        religious: data.religious ?? '',
        education: data.education ?? '',
        living_status: data.living_status ?? 'hidup',
        married_status: data.married_status ?? '',
        occupation: data.occupation ?? '',
        blood_type: data.blood_type ?? '',
      };
      formVisible.value = true;
    } catch {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: 'Gagal memuat data warga',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }

  async function submitForm() {
    loading.value = true;
    try {
      const payload = {
        ...form.value,
        no_kk: form.value.no_kk || null,
        birth_date: form.value.birth_date
          ? dayjs(form.value.birth_date).format('YYYY-MM-DD')
          : null,
      };

      if (isEditMode.value && editWargaId.value) {
        await WargaService.update(editWargaId.value, payload);
        toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data warga berhasil diupdate', life: 3000 });
      } else {
        await WargaService.create(payload);
        toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data warga berhasil ditambahkan', life: 3000 });
      }

      formVisible.value = false;
      resetForm();
      await loadWargas();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error?.response?.data?.message || 'Gagal menyimpan data warga',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }

  function confirmDelete(wargaId: number, event: Event) {
    confirm.require({
      target: event.currentTarget as HTMLElement,
      message: 'Yakin ingin menghapus data warga ini?',
      icon: 'pi pi-exclamation-triangle',
      rejectProps: { label: 'Batal', severity: 'secondary', outlined: true },
      acceptProps: { label: 'Hapus', severity: 'danger' },
      accept: () => deleteWarga(wargaId),
    });
  }

  async function deleteWarga(id: number) {
    loading.value = true;
    try {
      await WargaService.delete(id);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data warga berhasil dihapus', life: 3000 });
      await loadWargas();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: error?.response?.data?.message || 'Gagal menghapus data warga',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }

  function resetForm() {
    form.value = {
      nik: '',
      no_kk: '',
      name: '',
      gender: '',
      birth_place: '',
      birth_date: null,
      religious: '',
      education: '',
      living_status: 'hidup',
      married_status: '',
      occupation: '',
      blood_type: '',
    };
    isEditMode.value = false;
    editWargaId.value = null;
  }

  function clearFilter() {
    filters.value = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      nik: { value: null, matchMode: FilterMatchMode.CONTAINS },
      name: { value: null, matchMode: FilterMatchMode.CONTAINS },
      no_kk: { value: null, matchMode: FilterMatchMode.CONTAINS },
      gender: { value: null, matchMode: FilterMatchMode.EQUALS },
      living_status: { value: null, matchMode: FilterMatchMode.EQUALS },
    };
  }

  return {
    // State
    wargas, loading, loadingDetail,
    form, isEditMode,
    formVisible, detailVisible, importVisible,
    selectedWarga, filters,
    // Computed
    dialogTitle, submitButtonLabel,
    // Methods
    loadWargas, loadWargaDetail,
    openCreateDialog, openEditDialog,
    submitForm, confirmDelete,
    resetForm, clearFilter,
  };
}