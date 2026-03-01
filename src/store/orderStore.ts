// src/store/orderStore.ts
import { create } from 'zustand';
import { useAuthStore } from './authStore';
import { API } from "../config/api";

interface Order {
  id: number;
  userId: number;
  proxyType: string;
  country: string;
  count: number;
  period: string;
  totalPrice: number;
  status: 'pending' | 'approved' | 'declined';
  notes?: string;
  createdAt: string;
}

interface OrderStore {
  orders: Order[];
  loading: boolean;
  fetchOrders: () => Promise<void>;
  addOrder: (order: Omit<Order, 'id' | 'userId' | 'status' | 'createdAt'>) => Promise<void>;
}

export const useOrderStore = create<OrderStore>((set) => ({
  orders: [],
  loading: false,

  fetchOrders: async () => {
    set({ loading: true });
    const { token } = useAuthStore.getState();
    const res = await fetch(API.getOrders, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const data = await res.json();
    set({ orders: data, loading: false });
  },

  addOrder: async (order) => {
    const { token } = useAuthStore.getState();
    const res = await fetch(API.addOrder, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(order),
    });

    const responseData = await res.json();

    if (!res.ok) {
      // Specifically handles the 'Insufficient balance' error from PHP
      throw new Error(responseData.error || 'Order failed');
    }

    // 1. Update the Balance in AuthStore immediately
    // We use setState to reach into the other store without a hook
    if (responseData.new_balance !== undefined) {
      useAuthStore.setState((state) => ({
        user: state.user ? { ...state.user, balance: responseData.new_balance } : null
      }));
    }
    
    // 2. Add the new order to the list
    set((state) => ({ 
      orders: [responseData.order || responseData, ...state.orders] 
    }));
  },
}));