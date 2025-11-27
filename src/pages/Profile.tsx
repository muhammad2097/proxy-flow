// src/pages/Profile.tsx
import { useProxyStore } from '../store/proxyStore';
import { CheckCircle, XCircle, Clock, User, Package, DollarSign } from 'lucide-react';
import { getFlagSvgUrl } from '../utils/flags';
import { useOrderStore } from '../store/orderStore';
import { useEffect } from 'react';
import Layout from '../components/layout/Layout';
import { useAuthStore } from '../store/authStore';
import { useNavigate } from 'react-router-dom';

export default function Profile() {
  const { orders, fetchOrders, loading } = useOrderStore();
  const { user } = useAuthStore();
  const navigate = useNavigate();

  // PROTECT PAGE — ONLY LOGGED-IN USERS
  useEffect(() => {
    if (!user) {
      navigate('/login', { replace: true });
      return;
    }
    fetchOrders();
  }, [user, navigate, fetchOrders]);

  // Show nothing while redirecting
  if (!user) {
    return null;
  }

  const userOrders = orders;

  return (
    <Layout>
      <div className="min-h-screen bg-gradient-to-br from-dark via-gray-900 to-black text-white py-12">
        <div className="max-w-7xl mx-auto px-4">
          <div className="text-center mb-12">
            <div className="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-accent to-orange-500 rounded-full mb-4">
              <User className="w-10 h-10 text-dark" />
            </div>
            <h1 className="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-orange-400 to-red-600">
              My Proxy Orders
            </h1>
            <p className="text-gray-400 mt-2">Track your purchases and delivery details</p>
          </div>

          <div className="bg-white/5 backdrop-blur-sm rounded-xl border border-gray-700 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-white/10">
                  <tr>
                    <th className="text-left py-4 px-6">Order ID</th>
                    <th className="text-left py-4 px-6">Proxy</th>
                    <th className="text-left py-4 px-6">Country</th>
                    <th className="text-left py-4 px-6">Quantity</th>
                    <th className="text-left py-4 px-6">Total</th>
                    <th className="text-left py-4 px-6">Status</th>
                    <th className="text-left py-4 px-6">Time</th>
                    <th className="text-left py-4 px-6">Delivery Notes</th>
                  </tr>
                </thead>
                <tbody>
                  {userOrders.length === 0 ? (
                    <tr>
                      <td colSpan={8} className="text-center py-12 text-gray-500">
                        No orders yet. <a href="/" className="text-accent hover:underline">Buy your first proxy!</a>
                      </td>
                    </tr>
                  ) : (
                    [...userOrders]
                      .sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
                      .map((o) => {
                        const country = useProxyStore.getState().countries.find(c => c.name === o.country);
                        return (
                          <tr key={o.id} className="border-t border-gray-700 hover:bg-white/5 transition-all">
                            <td className="py-4 px-6 text-sm font-mono text-orange-400">#{o.id}</td>
                            <td className="py-4 px-6">
                              <div className="flex items-center gap-2">
                                <Package className="w-4 h-4 text-accent" />
                                <span className="font-medium">{o.proxyType}</span>
                              </div>
                            </td>
                            <td className="py-4 px-6">
                              <div className="flex items-center gap-2">
                                {country && <img src={getFlagSvgUrl(country.code)} alt="" className="w-5 h-4 rounded-sm" />}
                                <span>{o.country}</span>
                              </div>
                            </td>
                            <td className="py-4 px-6">{o.count} × {o.period}</td>
                            <td className="py-4 px-6">
                              <div className="flex items-center gap-1">
                                <DollarSign className="w-4 h-4 text-green-400" />
                                <span className="font-semibold">{o.totalPrice}</span>
                              </div>
                            </td>
                            <td className="py-4 px-6">
                              <div className="flex items-center gap-2">
                                {o.status === 'approved' && <CheckCircle className="w-5 h-5 text-green-400" />}
                                {o.status === 'declined' && <XCircle className="w-5 h-5 text-red-400" />}
                                {o.status === 'pending' && <Clock className="w-5 h-5 text-amber-400" />}
                                <span className={`px-3 py-1 rounded-full text-xs font-medium ${
                                  o.status === 'approved' ? 'bg-green-500/20 text-green-300' :
                                  o.status === 'declined' ? 'bg-red-500/20 text-red-300' :
                                  'bg-amber-500/20 text-amber-300'
                                }`}>
                                  {o.status}
                                </span>
                              </div>
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
                              {o.notes ? (
                                <div className="bg-white/10 rounded-lg p-3">
                                  <pre className="text-xs font-mono text-gray-300 whitespace-pre-wrap">
                                    {o.notes}
                                  </pre>
                                </div>
                              ) : (
                                <span className="text-xs text-gray-500 italic">No notes yet</span>
                              )}
                            </td>
                          </tr>
                        );
                      })
                  )}
                </tbody>
              </table>
            </div>
          </div>

          {/* <div className="text-center mt-12">
            <p className="text-sm text-gray-400">
              Need help? <a href="/contact" className="text-accent hover:underline">Contact Support</a>
            </p>
          </div> */}
        </div>
      </div>
    </Layout>
  );
}