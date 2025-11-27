// src/pages/Signup.tsx
import { useState } from 'react';
import { useAuthStore } from '../store/authStore';
import { useNavigate, Link } from 'react-router-dom';
import Layout from '../components/layout/Layout';

export default function Signup() {
  const [form, setForm] = useState({ name: '', email: '', password: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { signup } = useAuthStore();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    if (!form.name || !form.email || !form.password) {
      setError('All fields are required');
      setLoading(false);
      return;
    }

    try {
      await signup(form.name, form.email, form.password);
      navigate('/profile');
    } catch (err: any) {
      setError(err.message || 'Signup failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Layout>
        <div className="min-h-screen bg-gradient-to-br from-dark via-gray-900 to-black flex items-center justify-center px-4">
        <div className="bg-white/10 backdrop-blur-sm rounded-xl p-8 w-full max-w-md border border-gray-700">
            <h2 className="text-3xl font-bold text-center mb-6 text-white">Create Account</h2>
            
            {error && (
            <div className="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded mb-4 text-sm">
                {error}
            </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
            <input
                type="text"
                placeholder="Full Name"
                value={form.name}
                onChange={(e) => setForm({ ...form, name: e.target.value })}
                className="w-full px-4 py-3 bg-white/20 rounded-lg text-white placeholder-gray-400 border border-gray-600 focus:border-accent focus:outline-none"
                disabled={loading}
            />
            <input
                type="email"
                placeholder="Email Address"
                value={form.email}
                onChange={(e) => setForm({ ...form, email: e.target.value })}
                className="w-full px-4 py-3 bg-white/20 rounded-lg text-white placeholder-gray-400 border border-gray-600 focus:border-accent focus:outline-none"
                disabled={loading}
            />
            <input
                type="password"
                placeholder="Password"
                value={form.password}
                onChange={(e) => setForm({ ...form, password: e.target.value })}
                className="w-full px-4 py-3 bg-white/20 rounded-lg text-white placeholder-gray-400 border border-gray-600 focus:border-accent focus:outline-none"
                disabled={loading}
            />

            <button
                type="submit"
                disabled={loading}
                className="w-full bg-gradient-to-r from-blue-600 to-orange-500 text-white font-bold py-3 rounded-xl hover:shadow-lg transform hover:scale-105 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
            >
                {loading ? 'Creating Account...' : 'Sign Up'}
            </button>
            </form>

            <p className="text-center text-sm text-gray-400 mt-6">
            Already have an account?{' '}
            <Link to="/login" className="text-accent hover:underline font-medium">
                Login here
            </Link>
            </p>
        </div>
        </div>
    </Layout>
  );
}