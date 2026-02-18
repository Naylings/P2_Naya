import apiClient from './api';

export interface LetterDetail {
  no_surat:     string;
  jenis_surat:  string;
  perihal:      string;
  nama_pemohon: string;
  isi:          string;
  [key: string]: any; // field tambahan per jenis surat
}

export interface LetterPayload {
  doc_type: string;
  detail:   LetterDetail;
}

export interface LetterLog {
  id:         number;
  doc_type:   string;
  detail:     LetterDetail;
  local_file: string;
  created_at: string;
}

export interface StoreResponse {
  message:  string;
  id:       number;
  file_url: string;
}

export interface CountResponse {
  count: number;
}

const LetterService = {
  async store(payload: LetterPayload): Promise<StoreResponse> {
    const { data } = await apiClient.post<StoreResponse>('/doc', payload);
    return data;
  },

  async index(docType?: string): Promise<LetterLog[]> {
    const { data } = await apiClient.get<LetterLog[]>('/doc', {
      params: docType ? { doc_type: docType } : {},
    });
    return data;
  },

  async count(docType: string): Promise<number> {
    const { data } = await apiClient.get<CountResponse>('/doc/count', {
      params: { doc_type: docType },
    });
    return data.count;
  },

  streamUrl(id: number): string {
    return `/api/doc/${id}/stream`;
  },

  async regenerate(id: number): Promise<{ message: string; file_url: string }> {
    const { data } = await apiClient.post(`/doc/${id}/regenerate`);
    return data;
  },

  async destroy(id: number): Promise<void> {
    await apiClient.delete(`/doc/${id}`);
  },
};

export default LetterService;