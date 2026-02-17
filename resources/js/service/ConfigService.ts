import apiClient from "./api";

export interface LurahConfig {
  id: number;
  name: string;
  province: string;
  city: string;
  district: string;
  pos_code: string;
  logo: string | null;
  updated_at: string;
}

export default {
  async get() {
    const response = await apiClient.get("/config");
    return response.data;
  },

  async save(data: FormData) {
    const response = await apiClient.post("/config", data, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async deleteLogo() {
    const response = await apiClient.delete("/config/logo");
    return response.data;
  },
};