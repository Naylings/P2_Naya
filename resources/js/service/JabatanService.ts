import apiClient from "./api";

export default {
  async getJabatan() {
    const response = await apiClient.get("/jabatan");
    return response.data;
  }
};