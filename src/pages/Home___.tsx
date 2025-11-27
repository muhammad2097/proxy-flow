// src/pages/Home.tsx
import ProxyCard from '../components/ProxyCard';
import Header from '../components/layout/Header';
import Footer from '../components/layout/Footer';
import { useProxyStore } from '../store/proxyStore';
import { Zap, Shield, Globe } from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Home() {
  const { proxyTypes } = useProxyStore();

  // Live Counters State
  const [counters, setCounters] = useState({
    users: 808468,
    sold: 35317245,
    inWork: 447799,
    orders: 10312093,
  });

  // Auto-increment every 3–7 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      setCounters(prev => ({
        users: prev.users + Math.floor(Math.random() * 50) + 10,
        sold: prev.sold + Math.floor(Math.random() * 800) + 200,
        inWork: prev.inWork + Math.floor(Math.random() * 100) + 30,
        orders: prev.orders + Math.floor(Math.random() * 500) + 100,
      }));
    }, Math.random() * 4000 + 3000); // 3–7 sec

    return () => clearInterval(interval);
  }, []);

  // Format number with spaces
  const formatNumber = (num: number) => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

  return (
    <>
      <Header />
      <main className="min-h-screen bg-black text-white overflow-hidden">
        {/* HERO STATS SECTION */}
        <section className="relative py-16 bg-gradient-to-b from-black via-gray-950 to-black">
          <div className="max-w-7xl mx-auto px-4 text-center">
            {/* Title */}
            <h1 className="text-6xl md:text-8xl font-black tracking-tighter mb-2 bg-clip-text text-transparent bg-gradient-to-r from-orange-500 to-red-600">
              BUY PROXY
            </h1>
            <p className="text-xl md:text-2xl font-light tracking-widest text-gray-400 uppercase">
              Personal Proxy Servers HTTPS/SOCKS5
            </p>

            {/* Live Counters */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mt-16">
              {[
                { value: counters.users, label: 'USERS HAVE CHOSEN US' },
                { value: counters.sold, label: 'SOLD PROXIES' },
                { value: counters.inWork, label: 'PROXIES IN WORK' },
                { value: counters.orders, label: 'ORDERS PROCESSED' },
              ].map((stat, i) => (
                <div
                  key={i}
                  className="group transform transition-all duration-300 hover:scale-105"
                >
                  <div className="text-4xl md:text-5xl font-bold text-orange-500 tracking-wide mb-2">
                    <span className="inline-block animate-pulse">
                      {formatNumber(stat.value)}
                    </span>
                  </div>
                  <p className="text-xs md:text-sm font-medium text-gray-500 uppercase tracking-widest">
                    {stat.label}
                  </p>
                </div>
              ))}
            </div>
          </div>

          {/* Animated Background Grid */}
          <div className="absolute inset-0 opacity-20 pointer-events-none">
            <div className="h-full w-full bg-gradient-to-t from-orange-900/20 via-transparent to-transparent"></div>
          </div>
        </section>

        {/* PROXY CARDS */}
        <section className="py-20 bg-gradient-to-b from-black to-gray-950">
          <div className="max-w-7xl mx-auto px-4">
            <h2 className="text-3xl font-bold text-center mb-12 text-white">Choose Your Proxy Plan</h2>
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