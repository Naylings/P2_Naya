import axios from "axios";

// Buat instance axios baru
const apiClient = axios.create({
  baseURL: "/api", // atau base URL API Anda jika berbeda
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

// Tambahkan interceptor untuk request
apiClient.interceptors.request.use(
  (config) => {
    // Ambil token dari localStorage
    const token = localStorage.getItem("token");

    // Jika token ada, tambahkan ke header Authorization
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
  },
  (error) => {
    // Lakukan sesuatu jika ada error pada request
    return Promise.reject(error);
  },
);

export default apiClient;
