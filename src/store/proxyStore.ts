// src/store/proxyStore.ts
import { create } from "zustand";
import { persist } from "zustand/middleware";
import countriesData from "../data/countries.json";
import { API } from "../config/api";
import { useAuthStore } from "./authStore";

interface Country {
  name: string;
  code: string;
  stock: number;
}

interface ProxyType {
  id: string;
  type: "IPv6" | "IPv4" | "Shared";
  country: string;
  costPerWeek: number;
  costPerMonth: number;
}

interface ProxyStore {
  proxyTypes: ProxyType[];
  countries: Country[];

  // Load from backend
  fetchProxyTypes: () => Promise<void>;

  // Admin actions
  updateProxyCountry: (id: string, country: string) => Promise<void>;
  updateProxyPricing: (
    id: string,
    costPerWeek: number,
    costPerMonth: number
  ) => Promise<void>;

  addProxyType: (
    type: string,
    country: string,
    costPerWeek: number,
    costPerMonth: number
  ) => Promise<void>;
}

export const useProxyStore = create<ProxyStore>()(
  persist(
    (set, get) => ({
      proxyTypes: [],

      countries: countriesData,

      /**
       * Load pricing from backend (public API)
       */
      fetchProxyTypes: async () => {
        const res = await fetch(API.pricingGet);
        const data = await res.json();

        // Format matches your old state exactly
        const normalized: ProxyType[] = data.map((item: any) => ({
          id: String(item.id),
          type: item.type,
          country: item.country,
          costPerWeek: Number(item.costPerWeek),
          costPerMonth: Number(item.costPerMonth),
        }));

        set({ proxyTypes: normalized });
      },

      /**
       * Admin: Update only the country
       */
      updateProxyCountry: async (id, country) => {
        const { token } = useAuthStore.getState();
        const item = get().proxyTypes.find((p) => p.id === id);
        if (!item) return;

        await fetch(API.pricingUpdate(Number(id)), {
          method: "PATCH",
          headers: { 
            "Content-Type": "application/json", 
            "Authorization": `Bearer ${token}`},
          body: JSON.stringify({
            type: item.type,
            country,
            costPerWeek: item.costPerWeek,
            costPerMonth: item.costPerMonth,
          }),
        });

        // Update local state after successful API
        set((state) => ({
          proxyTypes: state.proxyTypes.map((p) =>
            p.id === id ? { ...p, country } : p
          ),
        }));
      },

      /**
       * Admin: Update pricing (week + month)
       */
      updateProxyPricing: async (id, costPerWeek, costPerMonth) => {
        const { token } = useAuthStore.getState();
        const item = get().proxyTypes.find((p) => p.id === id);
        if (!item) return;

        await fetch(API.pricingUpdate(Number(id)), {
          method: "PATCH",
          headers: { 
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`   // 🟢 ADD THIS
           },
          body: JSON.stringify({
            type: item.type,
            country: item.country,
            costPerWeek,
            costPerMonth,
          }),
        });

        // Update local state
        set((state) => ({
          proxyTypes: state.proxyTypes.map((p) =>
            p.id === id ? { ...p, costPerWeek, costPerMonth } : p
          ),
        }));
      },

      /**
       * Admin: Add new Proxy Pricing
       */
      addProxyType: async (type, country, costPerWeek, costPerMonth) => {
        const res = await fetch(API.pricingAdd, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ type, country, costPerWeek, costPerMonth }),
        });

        const data = await res.json();

        // Add to local store
        set((state) => ({
          proxyTypes: [
            ...state.proxyTypes,
            {
              id: String(data.id),
              type,
              country,
              costPerWeek,
              costPerMonth,
            },
          ],
        }));
      },
    }),
    {
      name: "proxy-store",
      partialize: (state) => ({
        proxyTypes: state.proxyTypes, // persist fetched pricing
      }),
    }
  )
);
