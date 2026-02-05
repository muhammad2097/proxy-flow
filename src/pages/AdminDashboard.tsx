// src/pages/AdminDashboard.tsx
import { useProxyStore } from '../store/proxyStore';
import { useAuthStore } from '../store/authStore';
import { Check, X, Edit2, Save, XCircle, DollarSign, User, Mail } from 'lucide-react';
import { useState, useEffect } from 'react';
import { getFlagSvgUrl } from '../utils/flags';
import Layout from '../components/layout/Layout';
import { useNavigate } from 'react-router-dom';
import { API } from "../config/api";

interface AdminOrder {
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
  user?: {
    name: string;
    email: string;
  };
}

export default function AdminDashboard() {
  const { proxyTypes, updateProxyPricing, countries } = useProxyStore();
  const { token, user } = useAuthStore();
  const navigate = useNavigate();

  const [orders, setOrders] = useState<AdminOrder[]>([]);
  const [loading, setLoading] = useState(true);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [notes, setNotes] = useState<Record<number, string>>({});
  const [editingPriceId, setEditingPriceId] = useState<string | null>(null);
  const [priceForm, setPriceForm] = useState<Record<string, { week: number; month: number }>>({});

  // PROTECT ADMIN PAGE
  useEffect(() => {
    if (!user) {
      navigate('/login', { replace: true });
      return;
    }
    if (user.role !== 'admin') {
      navigate('/profile', { replace: true });
      return;
    }
  }, [user, navigate]);

  useEffect(() => {
    useProxyStore.getState().fetchProxyTypes();
  }, []);

  if (!user || user.role !== 'admin') {
    return null;
  }

  // FETCH ORDERS
  useEffect(() => {
    if (!token) return;

    const fetchOrders = async () => {
      try {
        const res = await fetch(API.adminOrders, {
          headers: { Authorization: `Bearer ${token}` },
        });
        if (res.ok) {
          const data = await res.json();
          setOrders(data);
        }
      } catch (err) {
        console.error('Failed to fetch orders');
      } finally {
        setLoading(false);
      }
    };

    fetchOrders();
  }, [token]);

  // UPDATE STATUS
  const updateStatus = async (id: number, status: 'approved' | 'declined') => {
    try {
      await fetch(API.adminUpdateStatus(id), {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ status }),
      });
      setOrders(orders.map(o => o.id === id ? { ...o, status } : o));
    } catch (err) {
      console.error('Failed to update status');
    }
  };

  // SAVE NOTES
  const saveNotes = async (id: number) => {
    const note = notes[id] || '';
    try {
      await fetch(API.adminUpdateNotes(id), {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ notes: note }),
      });
      setOrders(orders.map(o => o.id === id ? { ...o, notes: note } : o));
    } catch (err) {
      console.error('Failed to save notes');
    } finally {
      setEditingId(null);
    }
  };

  // SAVE PRICING
  // const handleSavePrice = (proxyId: string) => {
  //   const { week, month } = priceForm[proxyId] || {};
  //   if (week !== undefined && month !== undefined) {
  //     updateProxyPricing(proxyId, week, month);
  //   }
  //   setEditingPriceId(null);
  // };

const handleSavePrice = async (proxyId: string) => {
  const { week, month } = priceForm[proxyId] || {};

  if (week !== undefined && month !== undefined) {
    await updateProxyPricing(proxyId, Number(week), Number(month));
  }

  setEditingPriceId(null);
};


  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-dark via-gray-900 to-black flex items-center justify-center">
        <p className="text-white">Loading orders...</p>
      </div>
    );
  }

  return (
    <Layout>
      <div className="min-h-screen bg-gradient-to-br from-dark via-gray-900 to-black text-white py-12">
        <div className="max-w-7xl mx-auto px-4">

          {/* PRICING EDITOR */}
          <div className="mb-12">
            <h2 className="text-3xl font-bold text-center mb-8 bg-clip-text text-transparent bg-gradient-to-r from-orange-500 to-red-600">
              Manage Proxy Pricing
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {proxyTypes.map((p) => {
                const isEditing = editingPriceId === p.id;
                return (
                  <div key={p.id} className="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-gray-700 hover:border-accent transition-all">
                    <div className="flex justify-between items-center mb-4">
                      <h3 className="text-xl font-bold">{p.type} Proxy</h3>
                      {isEditing ? (
                        <div className="flex gap-2">
                          <button onClick={() => handleSavePrice(p.id)} className="text-green-400"><Save className="w-5 h-5" /></button>
                          <button onClick={() => setEditingPriceId(null)} className="text-red-400"><XCircle className="w-5 h-5" /></button>
                        </div>
                      ) : (
                        <button onClick={() => {
                          setEditingPriceId(p.id);
                          setPriceForm({
                            ...priceForm,
                            [p.id]: { week: p.costPerWeek, month: p.costPerMonth }
                          });
                        }} className="text-amber-400"><Edit2 className="w-5 h-5" /></button>
                      )}
                    </div>

                    {isEditing ? (
                      <div className="space-y-3">
                        <div>
                          <label className="text-xs text-gray-400">Cost per Week ($)</label>
                          <input
                            type="number"
                            step="0.01"
                            value={priceForm[p.id]?.week || ''}
                            onChange={(e) => setPriceForm({
                              ...priceForm,
                              [p.id]: { ...priceForm[p.id], week: parseFloat(e.target.value) || 0 }
                            })}
                            className="w-full mt-1 px-3 py-2 bg-white/20 rounded-lg text-white border border-gray-600 focus:border-accent focus:outline-none"
                          />
                        </div>
                        <div>
                          <label className="text-xs text-gray-400">Cost per Month ($)</label>
                          <input
                            type="number"
                            step="0.01"
                            value={priceForm[p.id]?.month || ''}
                            onChange={(e) => setPriceForm({
                              ...priceForm,
                              [p.id]: { ...priceForm[p.id], month: parseFloat(e.target.value) || 0 }
                            })}
                            className="w-full mt-1 px-3 py-2 bg-white/20 rounded-lg text-white border border-gray-600 focus:border-accent focus:outline-none"
                          />
                        </div>
                      </div>
                    ) : (
                      <div className="space-y-2 text-sm">
                        <p className="flex items-center gap-2"><DollarSign className="w-4 h-4 text-green-400" /> Week: <span className="font-medium">${p.costPerWeek}</span></p>
                        <p className="flex items-center gap-2"><DollarSign className="w-4 h-4 text-green-400" /> Month: <span className="font-medium">${p.costPerMonth}</span></p>
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          </div>

          {/* ORDERS TABLE */}
          <div>
            <h2 className="text-3xl font-bold text-center mb-8 bg-clip-text text-transparent bg-gradient-to-r from-orange-500 to-red-600">
              Manage Orders
            </h2>
            <div className="bg-white/5 backdrop-blur-sm rounded-xl border border-gray-700 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-white/10">
                    <tr>
                      <th className="text-left py-4 px-6">ID</th>
                      <th className="text-left py-4 px-6">Customer</th>
                      <th className="text-left py-4 px-6">Type</th>
                      <th className="text-left py-4 px-6">Country</th>
                      <th className="text-left py-4 px-4">Qty × Period</th>
                      <th className="text-left py-4 px-6">Total</th>
                      <th className="text-left py-4 px-6">Status</th>
                      <th className="text-left py-4 px-6">Time</th>
                      <th className="text-left py-4 px-6">Notes</th>
                      <th className="text-left py-4 px-6">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {orders.length === 0 ? (
                      <tr>
                        <td colSpan={10} className="text-center py-12 text-gray-400">
                          No orders yet.
                        </td>
                      </tr>
                    ) : (
                      orders
                        .sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
                        .map((o) => {
                          const country = countries.find(c => c.name === o.country);
                          return (
                            <tr key={o.id} className="border-t border-gray-700 hover:bg-white/5">
                              <td className="py-4 px-6 text-sm font-mono text-orange-400">#{o.id}</td>
                              <td className="py-4 px-6">
                                <div className="flex flex-col text-xs">
                                  <div className="flex items-center gap-1">
                                    <User className="w-3 h-3 text-gray-400" />
                                    <span className="font-medium">
                                      {o?.userName || 'Unknown User'}
                                    </span>
                                  </div>
                                  <div className="flex items-center gap-1 text-gray-400">
                                    <Mail className="w-3 h-3" />
                                    <span>{o?.userEmail || '—'}</span>
                                  </div>
                                </div>
                              </td>
                              <td className="py-4 px-6">{o.proxyType}</td>
                              <td className="py-4 px-6">
                                <div className="flex items-center gap-2">
                                  {country && <img src={getFlagSvgUrl(country.code)} alt="" className="w-5 h-4 rounded-sm" />}
                                  <span>{o.country}</span>
                                </div>
                              </td>
                              <td className="py-4 px-6">{o.count} × {o.period}</td>
                              <td className="py-4 px-6 font-semibold">${Number(o.totalPrice).toFixed(2)}</td>
                              <td className="py-4 px-6">
                                <span className={`px-3 py-1 rounded-full text-xs font-medium ${
                                  o.status === 'approved' ? 'bg-green-500/20 text-green-300' :
                                  o.status === 'declined' ? 'bg-red-500/20 text-red-300' :
                                  'bg-amber-500/20 text-amber-300'
                                }`}>
                                  {o.status}
                                </span>
                              </td>
                              <td className="py-4 px-6 text-xs text-gray-400">
                                {new Date(o.createdAt).toLocaleString('en-US', {
                                  month: 'short',
                                  day: 'numeric',
                                  hour: '2-digit',
                                  minute: '2-digit',
                                })}
                              </td>
                              <td className="py-4 px-6 max-w-xs">
                                {editingId === o.id ? (
                                  <textarea
                                    value={notes[o.id] ?? o.notes ?? ''}
                                    onChange={(e) => setNotes({ ...notes, [o.id]: e.target.value })}
                                    className="w-full p-2 bg-white/10 rounded text-xs resize-none h-20 focus:outline-none focus:ring-1 focus:ring-accent"
                                    placeholder="IP: 1.2.3.4:8080\nuser:pass..."
                                  />
                                  
                                ) : (
                                  <pre className="text-xs text-gray-400 font-mono whitespace-pre-wrap">
                                    {o.notes || '—'}
                                  </pre>
                                )}
                              </td>
                              <td className="py-4 px-6">
                                <div className="flex gap-2">
                                  {o.status === 'pending' && (
                                    <>
                                      <button onClick={() => updateStatus(o.id, 'approved')} className="text-green-400 hover:text-green-300"><Check className="w-5 h-5" /></button>
                                      <button onClick={() => updateStatus(o.id, 'declined')} className="text-red-400 hover:text-red-300"><X className="w-5 h-5" /></button>
                                    </>
                                  )}
                                  {editingId === o.id ? (
                                    <>
                                      <button onClick={() => saveNotes(o.id)} className="text-blue-400 hover:text-blue-300"><Save className="w-5 h-5" /></button>
                                      <button onClick={() => setEditingId(null)} className="text-gray-400 hover:text-gray-300"><XCircle className="w-5 h-5" /></button>
                                    </>
                                  ) : (
                                    <button onClick={() => {
                                      setEditingId(o.id);
                                      setNotes({ ...notes, [o.id]: o.notes || '' });
                                    }} className="text-amber-400 hover:text-amber-300"><Edit2 className="w-5 h-5" /></button>
                                  )}
                                </div>
                              </td>
                            </tr>
                          );
                        })
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
}