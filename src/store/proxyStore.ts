// src/store/proxyStore.ts
import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import countriesData from '../data/countries.json';

interface Country {
  name: string;
  code: string;
  stock: number;
}

interface ProxyType {
  id: string;
  type: 'IPv6' | 'IPv4' | 'Shared';
  country: string;
  costPerWeek: number;
  costPerMonth: number;
}

interface Order {
  id: string;
  proxyType: string;
  country: string;
  count: number;
  period: string;
  totalPrice: number;
  status: 'pending' | 'approved' | 'declined';
  createdAt: string;
  notes?: string;
}

interface ProxyStore {
  proxyTypes: ProxyType[];
  countries: Country[];
  orders: Order[];

  updateProxyCountry: (id: string, country: string) => void;
  updateProxyPricing: (id: string, costPerWeek: number, costPerMonth: number) => void;
//   addOrder: (order: Omit<Order, 'id' | 'status' | 'createdAt'>) => void;
//   updateOrderStatus: (id: string, status: 'approved' | 'declined') => void;
//   updateOrderNotes: (id: string, notes: string) => void;
}

// DEFAULTS (only used once)
const DEFAULT_PROXY_TYPES: ProxyType[] = [
  { id: '1', type: 'IPv6', country: 'Canada', costPerWeek: 0.04, costPerMonth: 0.16 },
  { id: '2', type: 'IPv4', country: 'Australia', costPerWeek: 0.35, costPerMonth: 1.50 },
  { id: '3', type: 'Shared', country: 'France', costPerWeek: 0.10, costPerMonth: 0.40 },
];

const DEFAULT_COUNTRIES: Country[] = countriesData

export const useProxyStore = create<ProxyStore>()(
  persist(
    (set, get) => ({
      // Initialize with defaults only if not in localStorage
      proxyTypes: (() => {
        const saved = localStorage.getItem('proxy-store');
        if (saved) {
          const parsed = JSON.parse(saved);          
          return parsed.state?.proxyTypes || DEFAULT_PROXY_TYPES;
        }
        return DEFAULT_PROXY_TYPES;
      })(),

      countries: DEFAULT_COUNTRIES,
      orders: [],

      updateProxyCountry: (id, country) =>
        set((state) => ({
          proxyTypes: state.proxyTypes.map((p) =>
            p.id === id ? { ...p, country } : p
          ),
        })),

      updateProxyPricing: (id, costPerWeek, costPerMonth) =>
        set((state) => ({
          proxyTypes: state.proxyTypes.map((p) =>
            p.id === id ? { ...p, costPerWeek, costPerMonth } : p
          ),
        })),

    //   addOrder: (order) =>
    //     set((state) => ({
    //       orders: [
    //         {
    //           ...order,
    //           id: Date.now().toString(),
    //           status: 'pending',
    //           createdAt: new Date().toISOString(),
    //         },
    //         ...state.orders,
    //       ],
    //     })),

    //   updateOrderStatus: (id, status) =>
    //     set((state) => ({
    //       orders: state.orders.map((o) =>
    //         o.id === id ? { ...o, status } : o
    //       ),
    //     })),

    //   updateOrderNotes: (id, notes) =>
    //     set((state) => ({
    //       orders: state.orders.map((o) =>
    //         o.id === id ? { ...o, notes } : o
    //       ),
    //     })),
    }),
    {
      name: 'proxy-store',
      // Only persist proxyTypes & orders
      partialize: (state) => ({
        proxyTypes: state.proxyTypes,
        orders: state.orders,
      }),
    }
  )
);