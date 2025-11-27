// src/pages/OrdersTable.tsx
import { useProxyStore } from '../store/proxyStore';
import { Check, X } from 'lucide-react';
import { getFlagSvgUrl } from '../utils/flags';

export default function OrdersTable() {
  const { orders, updateOrderStatus, countries } = useProxyStore();

  if (orders.length === 0) {
    return (
      <div className="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h2 className="text-xl font-semibold mb-4 text-dark dark:text-light">Orders</h2>
        <p className="text-gray-500 dark:text-gray-400">No orders yet.</p>
      </div>
    );
  }

  return (
    <div className="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
      <h2 className="text-xl font-semibold mb-4 text-dark dark:text-light">Orders</h2>
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-gray-200 dark:border-gray-700">
              <th className="text-left py-3">ID</th>
              <th className="text-left py-3">Type</th>
              <th className="text-left py-3">Country</th>
              <th className="text-left py-3">Qty × Period</th>
              <th className="text-left py-3">Total</th>
              <th className="text-left py-3">Status</th>
              <th className="text-left py-3">Date</th>
              <th className="text-left py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {orders.map((o) => {
              const country = countries.find(c => c.name === o.country);
              const flagUrl = country ? getFlagSvgUrl(country.code) : '';
              return (
                <tr key={o.id} className="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td className="py-3 text-sm">#{o.id.slice(-6)}</td>
                  <td className="py-3">{o.proxyType}</td>
                  <td className="py-3">
                    <span className="inline-flex items-center gap-2">
                      {flagUrl && (
                        <img
                          src={flagUrl}
                          alt={o.country}
                          className="w-6 h-5 rounded-sm"
                        />
                      )}
                      <span>{o.country}</span>
                    </span>
                  </td>
                  <td className="py-3">{o.count} × {o.period}</td>
                  <td className="py-3 font-semibold">${o.totalPrice}</td>
                  <td className="py-3">
                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                      o.status === 'approved' ? 'bg-green-100 text-green-800' :
                      o.status === 'declined' ? 'bg-red-100 text-red-800' :
                      'bg-yellow-100 text-yellow-800'
                    }`}>
                      {o.status}
                    </span>
                  </td>
                  <td className="py-3 text-sm">{o.createdAt}</td>
                  <td className="py-3 flex space-x-1">
                    {o.status === 'pending' && (
                      <>
                        <button onClick={() => updateOrderStatus(o.id, 'approved')} className="text-green-600"><Check className="w-5 h-5" /></button>
                        <button onClick={() => updateOrderStatus(o.id, 'declined')} className="text-red-600"><X className="w-5 h-5" /></button>
                      </>
                    )}
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}