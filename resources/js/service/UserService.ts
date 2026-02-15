import apiClient from "./api";

export default {
  async getAll() {
    const response = await apiClient.get("/users");
    return response.data;
  },
  async getById(id: number) {
    const response = await apiClient.get(`/users/${id}`);
    return response.data;
  },
  async create(data: any) {
    const response = await apiClient.post("/users", data);
    return response.data;
  },
  async update(id: number, data: any) {
    const response = await apiClient.put(`/users/${id}`, data);
    return response.data;
  },
  async toggleStatus(id: number) {
    const response = await apiClient.patch(`/users/${id}/toggle-status`);
    return response.data;
  },
};