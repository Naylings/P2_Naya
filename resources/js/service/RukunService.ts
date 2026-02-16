import apiClient from "./api";

export interface Rukun {
    id: number;
    type: 'RT' | 'RW';
    no: string;
    created_at?: string;
    updated_at?: string;
}

export interface RukunCreatePayload {
    type: 'RT' | 'RW';
    no: number | string;
}

export default {
    async getAll() {
        const response = await apiClient.get("/rukun");
        return response.data;
    },
    
    async create(data: RukunCreatePayload) {
        const response = await apiClient.post('/rukun', data);
        return response.data;
    },
    
    async delete(id: number) {
        const response = await apiClient.post(`/rukun/${id}`);
        return response.data;
    },
};

