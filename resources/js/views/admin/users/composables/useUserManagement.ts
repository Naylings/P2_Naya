// views/admin/users/composables/useUserManagement.ts
import { ref, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import UserService from '@/service/UserService';
import JabatanService from '@/service/JabatanService';
import dayjs from 'dayjs';
import { FilterMatchMode } from '@primevue/core/api';

export interface User {
  id: number;
  email: string;
  nip: string;
  name: string;
  jabatan: string;
  jabatan_slug: string;
  no_hp: string;
  status: string;
  address?: string;
  birth_date?: string;
  nik?: string;
}

export interface UserForm {
  email: string;
  password: string;
  password_confirmation: string;
  jabatan_id: number | null;
  nip: string;
  name: string;
  no_hp: string;
  address: string;
  birth_date: Date | null;
  nik: string;
  status: string;
}

export function useUserManagement() {
  const toast = useToast();
  
  // State
  const users = ref<User[]>([]);
  const jabatans = ref<any[]>([]);
  const loading = ref(false);
  const loadingDetail = ref(false);
  
  // Form state
  const isEditMode = ref(false);
  const editUserId = ref<number | null>(null);
  const form = ref<UserForm>({
    email: '',
    password: '',
    password_confirmation: '',
    jabatan_id: null,
    nip: '',
    name: '',
    no_hp: '',
    address: '',
    birth_date: null,
    nik: '',
    status: 'inactive',
  });
  
  // Dialog & Drawer state
  const formVisible = ref(false);
  const detailVisible = ref(false);
  const selectedUser = ref<User | null>(null);
  
  // Filters
  const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    nip: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    jabatan: { value: null, matchMode: FilterMatchMode.CONTAINS },
    no_hp: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
  });
  
  // Computed
  const dialogTitle = computed(() => 
    isEditMode.value ? 'Edit User' : 'Create New User'
  );
  
  const submitButtonLabel = computed(() => 
    isEditMode.value ? 'Update User' : 'Save User'
  );
  
  // Methods
  async function loadUsers() {
    loading.value = true;
    try {
      const response = await UserService.getAll();
      users.value = response.data
        .filter((user: any) => user.jabatan?.slug !== 'administrator')
        .map((user: any) => ({
          id: user.id,
          email: user.email,
          nip: user.detail?.nip || '-',
          name: user.detail?.name || '-',
          jabatan: user.jabatan?.name || '-',
          jabatan_slug: user.jabatan?.slug || '',
          no_hp: user.detail?.no_hp || '-',
          status: user.detail?.status || 'inactive',
        }));
    } catch (error) {
      toast.add({
        severity: 'error',
        summary: 'Load Failed',
        detail: 'Failed to load users data',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }
  
  async function loadJabatan() {
    try {
      const response = await JabatanService.getJabatan();
      jabatans.value = response.data.filter(
        (jabatan: any) => jabatan.slug !== 'administrator'
      );
    } catch (error) {
      toast.add({
        severity: 'error',
        summary: 'Load Failed',
        detail: 'Failed to load jabatan',
        life: 3000,
      });
    }
  }
  
  async function loadUserDetail(userId: number) {
    loadingDetail.value = true;
    try {
      const response = await UserService.getById(userId);
      const userData = response.data;
      
      selectedUser.value = {
        id: userData.id,
        email: userData.email,
        nip: userData.detail?.nip || '-',
        name: userData.detail?.name || '-',
        jabatan: userData.jabatan?.name || '-',
        jabatan_slug: userData.jabatan?.slug || '',
        no_hp: userData.detail?.no_hp || '-',
        status: userData.detail?.status || 'inactive',
        address: userData.detail?.address || '-',
        birth_date: userData.detail?.birth_date || '-',
        nik: userData.detail?.nik || '-',
      };
    } catch (error) {
      toast.add({
        severity: 'error',
        summary: 'Load Failed',
        detail: 'Failed to load user details',
        life: 3000,
      });
      throw error;
    } finally {
      loadingDetail.value = false;
    }
  }
  
  function openCreateDialog() {
    resetForm();
    isEditMode.value = false;
    editUserId.value = null;
    formVisible.value = true;
  }
  
  async function openEditDialog(userId: number) {
    isEditMode.value = true;
    editUserId.value = userId;
    
    try {
      loading.value = true;
      const response = await UserService.getById(userId);
      const userData = response.data;
      
      form.value = {
        email: userData.email,
        password: '',
        password_confirmation: '',
        jabatan_id: userData.jabatan?.id || null,
        nip: userData.detail?.nip || '',
        name: userData.detail?.name || '',
        no_hp: userData.detail?.no_hp || '',
        address: userData.detail?.address || '',
        birth_date: userData.detail?.birth_date 
          ? new Date(userData.detail.birth_date) 
          : null,
        nik: userData.detail?.nik || '',
        status: userData.detail?.status || 'inactive',
      };
      
      formVisible.value = true;
    } catch (error) {
      toast.add({
        severity: 'error',
        summary: 'Load Failed',
        detail: 'Failed to load user data',
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }
  
  async function submitForm() {
    try {
      loading.value = true;
      
      const payload = {
        ...form.value,
        birth_date: form.value.birth_date
          ? dayjs(form.value.birth_date).format('YYYY-MM-DD')
          : null,
      };
      
      if (isEditMode.value && editUserId.value) {
        await UserService.update(editUserId.value, payload);
        toast.add({
          severity: 'success',
          summary: 'Success',
          detail: 'User updated successfully',
          life: 3000,
        });
      } else {
        await UserService.create(payload);
        toast.add({
          severity: 'success',
          summary: 'Success',
          detail: 'User created successfully',
          life: 3000,
        });
      }
      
      formVisible.value = false;
      resetForm();
      await loadUsers();
    } catch (error: any) {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error?.response?.data?.message || 
          `Failed to ${isEditMode.value ? 'update' : 'create'} user`,
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }
  
  async function toggleStatus(userId: number) {
    try {
      loading.value = true;
      const response = await UserService.toggleStatus(userId);
      toast.add({
        severity: 'success',
        summary: 'Status Updated',
        detail: response.data.message || 'User status updated successfully',
        life: 3000,
      });
      await loadUsers();
    } catch (error: any) {
      const errorMessage = error instanceof Error
        ? error.message
        : error?.response?.data?.message || 'Failed to update user status';
      
      toast.add({
        severity: 'error',
        summary: 'Update Failed',
        detail: errorMessage,
        life: 3000,
      });
    } finally {
      loading.value = false;
    }
  }
  
  function resetForm() {
    form.value = {
      email: '',
      password: '',
      password_confirmation: '',
      jabatan_id: null,
      nip: '',
      name: '',
      no_hp: '',
      address: '',
      birth_date: null,
      nik: '',
      status: 'inactive',
    };
    isEditMode.value = false;
    editUserId.value = null;
  }
  
  function clearFilter() {
    filters.value = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      email: { value: null, matchMode: FilterMatchMode.CONTAINS },
      nip: { value: null, matchMode: FilterMatchMode.CONTAINS },
      name: { value: null, matchMode: FilterMatchMode.CONTAINS },
      jabatan: { value: null, matchMode: FilterMatchMode.CONTAINS },
      no_hp: { value: null, matchMode: FilterMatchMode.CONTAINS },
      status: { value: null, matchMode: FilterMatchMode.EQUALS },
    };
  }
  
  return {
    // State
    users,
    jabatans,
    loading,
    loadingDetail,
    form,
    isEditMode,
    formVisible,
    detailVisible,
    selectedUser,
    filters,
    
    // Computed
    dialogTitle,
    submitButtonLabel,
    
    // Methods
    loadUsers,
    loadJabatan,
    loadUserDetail,
    openCreateDialog,
    openEditDialog,
    submitForm,
    toggleStatus,
    resetForm,
    clearFilter,
  };
}