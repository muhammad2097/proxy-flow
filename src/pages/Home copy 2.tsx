// src/pages/Home.tsx
import ProxyCard from '../components/ProxyCard';
import Header from '../components/layout/Header';
import Footer from '../components/layout/Footer';
import { useProxyStore } from '../store/proxyStore';
import { Zap, Shield, Globe } from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Home() {
  const { proxyTypes } = useProxyStore();

  // Live Stats
  const [stats, setStats] = useState({
    users: 808468,
    sold: 35317245,
    inWork: 447799,
    orders: 10312093,
  });

  // Auto-increment every 3–7 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      setStats(prev => ({
        users: prev.users + Math.floor(Math.random() * 40) + 15,
        sold: prev.sold + Math.floor(Math.random() * 900) + 300,
        inWork: prev.inWork + Math.floor(Math.random() * 120) + 40,
        orders: prev.orders + Math.floor(Math.random() * 600) + 200,
      }));
    }, Math.random() * 15000 + 8000);

    return () => clearInterval(interval);
  }, []);

  const format = (n: number) => n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

  return (
    <>
      <Header />
      <main className="min-h-screen">
        {/* HERO SECTION */}
        <section className="bg-gradient-to-br from-dark via-primary to-accent py-20 text-white">
          <div className="max-w-7xl mx-auto px-4 text-center">
            <h1 className="text-5xl md:text-6xl font-bold mb-6">Buy Premium Proxies</h1>
            <p className="text-xl mb-8 max-w-3xl mx-auto">
              High-speed, secure, and anonymous proxies for scraping, browsing, and automation.
            </p>

            {/* Feature Icons */}
            <div className="flex justify-center space-x-8 text-sm md:text-base mb-12">
              <div className="flex items-center space-x-2"><Zap className="w-5 h-5" /> <span>99.9% Uptime</span></div>
              <div className="flex items-center space-x-2"><Shield className="w-5 h-5" /> <span>Encrypted</span></div>
              <div className="flex items-center space-x-2"><Globe className="w-5 h-5" /> <span>100+ Locations</span></div>
            </div>

            {/* LIVE STATS */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10">
              {[
                { value: stats.users, label: 'USERS HAVE CHOSEN US' },
                { value: stats.sold, label: 'SOLD PROXIES' },
                { value: stats.inWork, label: 'PROXIES IN WORK' },
                { value: stats.orders, label: 'ORDERS PROCESSED' },
              ].map((stat, i) => (
                <div key={i} className="group">
                  <div className="text-3xl md:text-4xl font-bold text-orange-400 tracking-wide mb-1 transform transition-all duration-300 group-hover:scale-110">
                    <span className="inline-block animate-pulse">
                      {format(stat.value)}
                    </span>
                  </div>
                  <p className="text-xs font-medium text-gray-200 uppercase tracking-widest">
                    {stat.label}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* PROXY CARDS */}
        <section className="py-20 bg-light dark:bg-gray-800">
          <div className="max-w-7xl mx-auto px-4">
            {/* <h2 className="text-3xl font-bold text-center mb-12 text-dark dark:text-light">
              Choose Your Proxy Plan
            </h2> */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {proxyTypes.map((proxy) => (
                <ProxyCard key={proxy.id} proxy={proxy} />
              ))}
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </>
  );
}