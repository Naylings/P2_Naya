import { ref, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import FamilyService from '@/service/FamilyService';
import WargaService from '@/service/WargaService';
import RukunService from '@/service/RukunService';
import type { Family, FamilyForm, FamilyMember, AvailableHead } from '@/service/FamilyService';
import type { Rukun } from '@/service/RukunService';
import { FilterMatchMode } from '@primevue/core/api';

export type { Family, FamilyForm, FamilyMember, AvailableHead };

export function useFamilyManagement() {
  const toast = useToast();
  const confirm = useConfirm();

  // State
  const families = ref<Family[]>([]);
  const rtList = ref<Rukun[]>([]);
  const rwList = ref<Rukun[]>([]);
  const loading = ref(false);
  const loadingDetail = ref(false);
  const loadingMembers = ref(false);

  // Form state
  const isEditMode = ref(false);
  const editFamilyId = ref<number | null>(null);
  const form = ref<FamilyForm>({
    no_kk: '',
    rt_id: null,
    rw_id: null,
    address: '',
    family_head_id: null,
  });

  // Dialog state
  const formVisible = ref(false);
  const memberVisible = ref(false);

  // Detail / expand state
  const expandedRows = ref<Record<number, boolean>>({});
  const familyDetail = ref<Record<number, Family>>({});

  // Member management state
  const selectedFamily = ref<Family | null>(null);
  const availableHeads = ref<AvailableHead[]>([]);
  const wargaWithoutFamily = ref<any[]>([]);
  const selectedNewMemberId = ref<number | null>(null);
  const selectedNewHeadId = ref<number | null>(null);

  // Filters
  const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    no_kk: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rt_no: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rw_no: { value: null, matchMode: FilterMatchMode.CONTAINS },
    family_head_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
  });

  // Computed
  const dialogTitle = computed(() =>
    isEditMode.value ? 'Edit Data Keluarga' : 'Tambah Keluarga Baru'
  );
  const submitButtonLabel = computed(() =>
    isEditMode.value ? 'Update' : 'Simpan'
  );

  // Methods
  async function loadFamilies() {
    loading.value = true;
    try {
      const response = await FamilyService.getAll();
      families.value = response.data;
    } catch {
      toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat data keluarga', life: 3000 });
    } finally {
      loading.value = false;
    }
  }

  async function loadRukun() {
    try {
      const response = await RukunService.getAll();
      rtList.value = response.data.filter((r: Rukun) => r.type === 'RT');
      rwList.value = response.data.filter((r: Rukun) => r.type === 'RW');
    } catch {
      toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat data RT/RW', life: 3000 });
    }
  }

  async function loadFamilyDetail(familyId: number) {
    loadingDetail.value = true;
    try {
      const response = await FamilyService.getById(familyId);
      familyDetail.value[familyId] = response.data;
    } catch {
      toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat detail keluarga', life: 3000 });
    } finally {
      loadingDetail.value = false;
    }
  }

  async function onRowExpand(event: any) {
    const familyId = event.data.id;
    await loadFamilyDetail(familyId);
  }

  function openCreateDialog() {
    resetForm();
    isEditMode.value = false;
    editFamilyId.value = null;
    formVisible.value = true;
  }

  async function openEditDialog(familyId: number) {
    isEditMode.value = true;
    editFamilyId.value = familyId;
    loading.value = true;
    try {
      const response = await FamilyService.getById(familyId);
      const data = response.data;
      form.value = {
        no_kk: data.no_kk,
        rt_id: data.rt_id,
        rw_id: data.rw_id,
        address: data.address,
        family_head_id: data.family_head_id,
      };
      formVisible.value = true;
    } catch {
      toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat data keluarga', life: 3000 });
    } finally {
      loading.value = false;
    }
  }

  async function submitForm() {
    loading.value = true;
    try {
      if (isEditMode.value && editFamilyId.value) {
        await FamilyService.update(editFamilyId.value, form.value);
        toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data keluarga berhasil diupdate', life: 3000 });
      } else {
        await FamilyService.create(form.value);
        toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data keluarga berhasil ditambahkan', life: 3000 });
      }
      formVisible.value = false;
      resetForm();
      await loadFamilies();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error?.response?.data?.message || 'Gagal menyimpan data keluarga',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }

  function confirmDelete(familyId: number, event: Event) {
    confirm.require({
      target: event.currentTarget as HTMLElement,
      message: 'Yakin ingin menghapus data keluarga ini? Semua anggota akan dilepas dari KK ini.',
      icon: 'pi pi-exclamation-triangle',
      rejectProps: { label: 'Batal', severity: 'secondary', outlined: true },
      acceptProps: { label: 'Hapus', severity: 'danger' },
      accept: () => deleteFamily(familyId),
    });
  }

  async function deleteFamily(id: number) {
    loading.value = true;
    try {
      await FamilyService.delete(id);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data keluarga berhasil dihapus', life: 3000 });
      await loadFamilies();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: error?.response?.data?.message || 'Gagal menghapus data keluarga',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }

  // Member management
  async function openMemberDialog(family: Family) {
    selectedFamily.value = family;
    loadingMembers.value = true;
    memberVisible.value = true;
    selectedNewMemberId.value = null;
    selectedNewHeadId.value = null;
    try {
      // Load detail (with available_heads) dan warga tanpa KK
      const [detailRes, wargaRes] = await Promise.all([
        FamilyService.getById(family.id),
        WargaService.getAll(),
      ]);
      selectedFamily.value = detailRes.data;
      availableHeads.value = detailRes.data.available_heads ?? [];
      wargaWithoutFamily.value = wargaRes.data.filter(
        (w: any) => !w.no_kk
      );
    } catch {
      toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat data anggota', life: 3000 });
    } finally {
      loadingMembers.value = false;
    }
  }

  async function addMember() {
    if (!selectedFamily.value || !selectedNewMemberId.value) return;
    loadingMembers.value = true;
    try {
      await FamilyService.addMember(selectedFamily.value.id, selectedNewMemberId.value);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Anggota berhasil ditambahkan', life: 3000 });
      selectedNewMemberId.value = null;
      // Refresh data
      await openMemberDialog(selectedFamily.value);
      await loadFamilies();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: error?.response?.data?.message || 'Gagal menambahkan anggota',
        life: 3000,
      });
    } finally {
      loadingMembers.value = false;
    }
  }

  function confirmRemoveMember(memberId: number, event: Event) {
    confirm.require({
      target: event.currentTarget as HTMLElement,
      message: 'Yakin ingin mengeluarkan anggota ini dari keluarga?',
      icon: 'pi pi-exclamation-triangle',
      rejectProps: { label: 'Batal', severity: 'secondary', outlined: true },
      acceptProps: { label: 'Keluarkan', severity: 'danger' },
      accept: () => removeMember(memberId),
    });
  }

  async function removeMember(memberId: number) {
    if (!selectedFamily.value) return;
    loadingMembers.value = true;
    try {
      await FamilyService.removeMember(selectedFamily.value.id, memberId);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Anggota berhasil dikeluarkan', life: 3000 });
      await openMemberDialog(selectedFamily.value);
      await loadFamilies();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: error?.response?.data?.message || 'Gagal mengeluarkan anggota',
        life: 3000,
      });
    } finally {
      loadingMembers.value = false;
    }
  }

  async function setHead() {
    if (!selectedFamily.value || !selectedNewHeadId.value) return;
    loadingMembers.value = true;
    try {
      await FamilyService.setHead(selectedFamily.value.id, selectedNewHeadId.value);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Kepala keluarga berhasil diubah', life: 3000 });
      selectedNewHeadId.value = null;
      await openMemberDialog(selectedFamily.value);
      await loadFamilies();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Gagal',
        detail: error?.response?.data?.message || 'Gagal mengubah kepala keluarga',
        life: 3000,
      });
    } finally {
      loadingMembers.value = false;
    }
  }

  function resetForm() {
    form.value = {
      no_kk: '',
      rt_id: null,
      rw_id: null,
      address: '',
      family_head_id: null,
    };
    isEditMode.value = false;
    editFamilyId.value = null;
  }

  function clearFilter() {
    filters.value = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      no_kk: { value: null, matchMode: FilterMatchMode.CONTAINS },
      rt_no: { value: null, matchMode: FilterMatchMode.CONTAINS },
      rw_no: { value: null, matchMode: FilterMatchMode.CONTAINS },
      family_head_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    };
  }

  return {
    // State
    families, rtList, rwList,
    loading, loadingDetail, loadingMembers,
    form, isEditMode,
    formVisible, memberVisible,
    expandedRows, familyDetail,
    selectedFamily, availableHeads, wargaWithoutFamily,
    selectedNewMemberId, selectedNewHeadId,
    filters,
    // Computed
    dialogTitle, submitButtonLabel,
    // Methods
    loadFamilies, loadRukun, loadFamilyDetail,
    onRowExpand, openCreateDialog, openEditDialog,
    submitForm, confirmDelete,
    openMemberDialog, addMember, confirmRemoveMember, setHead,
    resetForm, clearFilter,
  };
}