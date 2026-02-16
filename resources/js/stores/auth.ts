import { me } from "@/service/authService";
import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null as null | {
      id: number;
      email: string;
      role: string;
    },
    loaded: false,
  }),

  actions: {
    async fetchMe() {
      this.user = await me();
      this.loaded = true;
    },

    clear() {
      this.user = null;
      this.loaded = false;
    },
  },
});
