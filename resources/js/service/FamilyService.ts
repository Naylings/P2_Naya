import apiClient from "./api";

export interface Family {
  id: number;
  no_kk: string;
  rt_id: number;
  rw_id: number;
  rt_no: string;
  rw_no: string;
  address: string;
  family_head_id: number | null;
  family_head_name: string;
  members_count: number;
  members?: FamilyMember[];
  available_heads?: AvailableHead[];
}

export interface FamilyMember {
  id: number;
  name: string;
  nik: string;
  is_head: boolean;
}

export interface AvailableHead {
  id: number;
  name: string;
  nik: string;
}

export interface FamilyForm {
  no_kk: string;
  rt_id: number | null;
  rw_id: number | null;
  address: string;
  family_head_id: number | null;
}

export default {
  async getAll() {
    const response = await apiClient.get("/family");
    return response.data;
  },

  async getById(id: number) {
    const response = await apiClient.get(`/family/${id}`);
    return response.data;
  },

  async create(data: any) {
    const response = await apiClient.post("/family", data);
    return response.data;
  },

  async update(id: number, data: any) {
    const response = await apiClient.put(`/family/${id}`, data);
    return response.data;
  },

  async delete(id: number) {
    const response = await apiClient.delete(`/family/${id}`);
    return response.data;
  },

  async setHead(familyId: number, wargaId: number) {
    const response = await apiClient.post(`/family/${familyId}/set-head`, {
      warga_id: wargaId,
    });
    return response.data;
  },

  async addMember(familyId: number, wargaId: number) {
    const response = await apiClient.post(`/family/${familyId}/add-member`, {
      warga_id: wargaId,
    });
    return response.data;
  },

  async removeMember(familyId: number, wargaId: number) {
    const response = await apiClient.delete(`/family/${familyId}/remove-member`, {
      data: { warga_id: wargaId },
    });
    return response.data;
  },
};