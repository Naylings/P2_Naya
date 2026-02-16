// views/admin/rukun/composables/useRukunManagement.ts
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import RukunService, { type Rukun, type RukunCreatePayload } from '@/service/RukunService';
import { FilterMatchMode } from '@primevue/core/api';

export interface RukunForm {
  type: 'RT' | 'RW';
  no: string;
}

export function useRukunManagement() {
  const toast = useToast();
  
  // State
  const rukuns = ref<Rukun[]>([]);
  const loading = ref(false);
  
  // Form state
  const form = ref<RukunForm>({
    type: 'RT',
    no: '',
  });
  
  // Dialog state
  const formVisible = ref(false);
  
  // Filters
  const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    type: { value: null, matchMode: FilterMatchMode.EQUALS },
    no: { value: null, matchMode: FilterMatchMode.CONTAINS },
  });
  
  // Options
  const typeOptions = [
    { label: 'RT', value: 'RT' },
    { label: 'RW', value: 'RW' }
  ];
  
  // Methods
  async function loadRukuns() {
    loading.value = true;
    try {
      const response = await RukunService.getAll();
      rukuns.value = response.data;
    } catch (error) {
      toast.add({
        severity: 'error',
        summary: 'Load Failed',
        detail: 'Failed to load rukun data',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }
  
  function openCreateDialog() {
    resetForm();
    formVisible.value = true;
  }
  
  async function submitForm() {
    try {
      loading.value = true;
      
      const payload: RukunCreatePayload = {
        type: form.value.type,
        no: form.value.no,
      };
      
      const response = await RukunService.create(payload);
      
      toast.add({
        severity: 'success',
        summary: 'Success',
        detail: response.message || 'Rukun created successfully',
        life: 3000,
      });
      
      formVisible.value = false;
      resetForm();
      await loadRukuns();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error?.response?.data?.message || 'Failed to create rukun',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }
  
  async function deleteRukun(id: number) {
    try {
      loading.value = true;
      const response = await RukunService.delete(id);
      
      toast.add({
        severity: 'success',
        summary: 'Success',
        detail: response.message || 'Rukun deleted successfully',
        life: 3000,
      });
      
      await loadRukuns();
    } catch (error: any) {
      const errorMessage = error instanceof Error
        ? error.message
        : error?.response?.data?.message || 'Failed to delete rukun';
      
      toast.add({
        severity: 'error',
        summary: 'Delete Failed',
        detail: errorMessage,
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }
  
  function resetForm() {
    form.value = {
      type: 'RT',
      no: '',
    };
  }
  
  function clearFilter() {
    filters.value = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      type: { value: null, matchMode: FilterMatchMode.EQUALS },
      no: { value: null, matchMode: FilterMatchMode.CONTAINS },
    };
  }
  
  return {
    // State
    rukuns,
    loading,
    form,
    formVisible,
    filters,
    typeOptions,
    
    // Methods
    loadRukuns,
    openCreateDialog,
    submitForm,
    deleteRukun,
    resetForm,
    clearFilter,
  };
}