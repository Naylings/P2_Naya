import apiClient from './api';

export const login = async (email: string, password: string ) => {
  const res = await apiClient.post('/auth/login', { email, password });
    localStorage.setItem('token', res.data.token);
    return res.data.user;
};

export const logout = async () => {
  await apiClient.post('/auth/logout');
    localStorage.removeItem('token');
};

export const me = async () => {
  const res = await apiClient.get('/auth/me');
    return res.data;
};
