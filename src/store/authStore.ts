// src/store/authStore.ts
import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface User {
  id: number;
  name: string;
  email: string;
  role: 'user' | 'admin';
}

interface AuthStore {
  user: User | null;
  token: string | null;
  login: (email: string, password: string) => Promise<void>;
  signup: (name: string, email: string, password: string) => Promise<void>;
  logout: () => void;
  loadUser: () => Promise<void>;
}

export const useAuthStore = create<AuthStore>()(
  persist(
    (set) => ({
      user: null,
      token: null,

      signup: async (name, email, password) => {
        const res = await fetch('http://localhost:5000/api/signup', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, email, password }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Signup failed');
        set({ user: data.user, token: data.token });
      },

      login: async (email, password) => {
        const res = await fetch('http://localhost:5000/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Login failed');
        set({ user: data.user, token: data.token });
      },

      logout: () => set({ user: null, token: null }),

      loadUser: async () => {
        const saved = localStorage.getItem('auth-storage');
        if (!saved) return;
        const { token } = JSON.parse(saved).state;
        if (!token) return;

        const res = await fetch('http://localhost:5000/api/me', {
          headers: { Authorization: `Bearer ${token}` },
        });
        if (res.ok) {
          const user = await res.json();
          set({ user, token });
        } else {
          set({ user: null, token: null });
        }
      },
    }),
    { name: 'auth-storage' }
  )
);