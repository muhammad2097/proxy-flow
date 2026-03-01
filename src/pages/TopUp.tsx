import { useState, useEffect } from 'react';
import { useAuthStore } from '../store/authStore';
import { useNavigate } from 'react-router-dom';
import Layout from '../components/layout/Layout';
import { Wallet } from 'lucide-react';

export default function TopUp() {
  const [amount, setAmount] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { user, topup } = useAuthStore();
  const navigate = useNavigate();
  // Mandatory check: Redirect if not logged in
  useEffect(() => {
    if (!user) {
      navigate('/login');
    }
  }, [user, navigate]);

    const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
        await topup(parseFloat(amount));
        // The Header will update automatically because the store state changed
        navigate('/profile'); 
    } catch (err: any) {
        setError(err.message);
    } finally {
        setLoading(false);
    }
    };

  return (
    <Layout>
      <div className="min-h-screen bg-gradient-to-br from-dark via-gray-900 to-black flex items-center justify-center px-4">
        <div className="bg-white/10 backdrop-blur-sm rounded-xl p-8 w-full max-w-md border border-gray-700">
          <div className="flex justify-center mb-4">
            <div className="p-3 bg-indigo-500/20 rounded-full">
              <Wallet className="w-8 h-8 text-indigo-400" />
            </div>
          </div>
          
          <h2 className="text-3xl font-bold text-center mb-2 text-white">Add Credits</h2>
          <p className="text-gray-400 text-center mb-6 text-sm">
            Top up your balance to purchase proxies instantly.
          </p>
          
          {error && (
            <div className="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded mb-4 text-sm text-center">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="relative">
              <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
              <input
                type="number"
                placeholder="Enter Amount"
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
                className="w-full pl-8 pr-4 py-3 bg-white/20 rounded-lg text-white placeholder-gray-400 border border-gray-600 focus:border-accent focus:outline-none text-lg font-semibold"
                disabled={loading}
              />
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-gradient-to-r from-blue-600 to-orange-500 text-white font-bold py-3 rounded-xl hover:shadow-lg transform hover:scale-105 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
            >
              {loading ? 'Processing...' : 'Recharge Now'}
            </button>
          </form>

          <button 
            onClick={() => navigate(-1)} 
            className="w-full mt-4 text-gray-400 hover:text-white text-sm transition"
          >
            Cancel and Go Back
          </button>
        </div>
      </div>
    </Layout>
  );
}