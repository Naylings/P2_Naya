import apiClient from "./api";

export interface Warga {
  id: number;
  nik: string;
  no_kk: string | null;
  name: string;
  gender: 'L' | 'P';
  birth_place: string;
  birth_date: string;
  religious: string | null;
  education: string | null;
  living_status: 'hidup' | 'meninggal' | 'pindah' | 'tidak_diketahui' | null;
  married_status: string | null;
  occupation: string | null;
  blood_type: string | null;
  family?: {
    id: number;
    no_kk: string;
    address: string;
  } | null;
}

export interface WargaForm {
  nik: string;
  no_kk: string;
  name: string;
  gender: 'L' | 'P' | '';
  birth_place: string;
  birth_date: Date | null;
  religious: string;
  education: string;
  living_status: string;
  married_status: string;
  occupation: string;
  blood_type: string;
}

export default {
  async getAll() {
    const response = await apiClient.get("/warga");
    return response.data;
  },

  async getById(id: number) {
    const response = await apiClient.get(`/warga/${id}`);
    return response.data;
  },

  async create(data: any) {
    const response = await apiClient.post("/warga", data);
    return response.data;
  },

  async update(id: number, data: any) {
    const response = await apiClient.put(`/warga/${id}`, data);
    return response.data;
  },

  async delete(id: number) {
    const response = await apiClient.delete(`/warga/${id}`);
    return response.data;
  },

  async import(file: File) {
    const formData = new FormData();
    formData.append("file", file);
    const response = await apiClient.post("/warga/import", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async downloadTemplate() {
    const response = await apiClient.get("/warga/template", {
      responseType: "blob",
    });
    return response.data;
  },
};