// src/pages/Home.tsx
import ProxyCard from '../components/ProxyCard';
import Header from '../components/layout/Header';
import Footer from '../components/layout/Footer';
import { useProxyStore } from '../store/proxyStore';
import { Zap, Shield, Globe, Download, Users, BarChart3, Award, Clock, Globe2 } from 'lucide-react';
import { useState, useEffect, useRef } from 'react';
import WhatsappFloat from '../components/WhatsappFloat';

export default function Home() {
  const { proxyTypes } = useProxyStore();
  const planSectionRef = useRef<HTMLDivElement>(null);

  // Live Stats
  const [stats, setStats] = useState({
    customers: 64773,
    requests: 650000000000,
    globalIPs: 80000000,
    uptime: 99.97,
  });

  // Auto-increment
  useEffect(() => {
    const interval = setInterval(() => {
      setStats(prev => ({
        customers: prev.customers + Math.floor(Math.random() * 10) + 5,
        requests: prev.requests + Math.floor(Math.random() * 1000000000) + 500000000,
        globalIPs: prev.globalIPs + Math.floor(Math.random() * 100000) + 50000,
        uptime: 99.97,
      }));
    }, Math.random() * 4000 + 3000);
    return () => clearInterval(interval);
  }, []);

  const formatNumber = (num: number) => num.toLocaleString();

  // Smooth Scroll to "Choose Your Plan"
  const scrollToPlans = () => {
    planSectionRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  };

  // Use Cases
  const useCases = [
    { Icon: BarChart3, label: 'Pricing Intelligence' },
    { Icon: Shield, label: 'Anti-Fraud' },
    { Icon: Users, label: 'Social Media' },
    { Icon: Globe2, label: 'Public Data' },
    { Icon: Award, label: 'Reputation Management' },
  ];

  return (
    <>
      <Header />
      <main className="min-h-screen">
        {/* HERO */}
        <section className="bg-gradient-to-br from-dark via-primary to-accent py-16 text-white">
          <div className="max-w-7xl mx-auto px-4 text-center">
            <h1 className="text-4xl md:text-5xl font-bold mb-4">Buy Premium Proxies</h1>
            <p className="text-lg mb-6 max-w-2xl mx-auto">
              High-speed, secure proxies for scraping, browsing, and automation. 80M+ IPs from 195 countries.
            </p>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
              <div className="flex items-center justify-center space-x-2 text-sm">
                <Zap className="w-5 h-5" /> <span>99.9% Uptime</span>
              </div>
              <div className="flex items-center justify-center space-x-2 text-sm">
                <Shield className="w-5 h-5" /> <span>Encrypted</span>
              </div>
              <div className="flex items-center justify-center space-x-2 text-sm">
                <Globe className="w-5 h-5" /> <span>{formatNumber(stats.globalIPs)} IPs</span>
              </div>
              <div className="flex items-center justify-center space-x-2 text-sm">
                <Users className="w-5 h-5" /> <span>{formatNumber(stats.customers)} Customers</span>
              </div>
            </div>

            {/* <div className="bg-white/10 rounded-lg p-4 mb-8 max-w-md mx-auto">
              <p className="text-sm font-medium mb-2">10 Proxies Free Today. No credit card required</p>
              <button
                onClick={scrollToPlans}
                className="bg-accent text-dark font-bold px-6 py-2 rounded-lg hover:bg-amber-500 transition"
              >
                Start for Free
              </button>
            </div> */}
          </div>
        </section>

        {/* USE CASES */}
        <section className="py-12 bg-light dark:bg-gray-800">
          <div className="max-w-7xl mx-auto px-4">
            <h2 className="text-2xl font-bold text-center mb-8 text-dark dark:text-light">Built for Your Needs</h2>
            <div className="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
              {useCases.map(({ Icon, label }, i) => (
                <div key={i} className="flex flex-col items-center">
                  <Icon className="w-8 h-8 text-primary mb-2" />
                  <span className="text-sm font-medium">{label}</span>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* OUR PROXY SOLUTIONS → CTA BUTTONS SCROLL TO PLANS */}
        <section className="py-16 bg-dark text-light">
          <div className="max-w-7xl mx-auto px-4">
            <h2 className="text-3xl font-bold text-center mb-8">Our Proxy Solutions</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
              {[
                { type: 'IPv6', price: '$0.35/week', desc: '400K+ Fast IPs • 99.97% Uptime', cta: 'Buy Now' },
                { type: 'IPv4', price: '$1.50/week', desc: 'Static Residential • Unlimited Bandwidth', cta: 'Buy Now' },
                { type: 'Shared', price: '$0.10/week', desc: '80M+ Rotating IPs • 195 Countries', cta: 'Buy Now' },
              ].map((plan, i) => (
                <div key={i} className="bg-white/10 rounded-xl p-6 text-center border border-gray-700">
                  <h3 className="text-xl font-bold mb-4">{plan.type} Proxy</h3>
                  <p className="text-2xl font-bold text-accent mb-2">{plan.price}</p>
                  <p className="text-sm text-gray-400 mb-6">{plan.desc}</p>
                  <button
                    onClick={scrollToPlans}
                    className="bg-accent text-dark font-bold px-6 py-2 rounded-lg hover:bg-amber-500 transition w-full"
                  >
                    {plan.cta}
                  </button>
                  <p className="text-xs text-gray-500 mt-4">Private/Dedicated Available</p>
                </div>
              ))}
            </div>

            <div className="text-center">
              <button className="bg-transparent border border-accent text-accent font-bold px-8 py-3 rounded-lg hover:bg-accent hover:text-dark transition">
                Looking for a Custom Solution? Contact Us
              </button>
            </div>
          </div>
        </section>

        {/* CHOOSE YOUR PLAN (SCROLL TARGET) */}
        <section ref={planSectionRef} className="py-20 bg-light dark:bg-gray-800">
          <div className="max-w-7xl mx-auto px-4">
            <h2 className="text-3xl font-bold text-center mb-12 text-dark dark:text-light">Choose Your Proxy Plan</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {proxyTypes.map((proxy) => (
              <ProxyCard key={proxy.id} proxyId={proxy.id} />  // ← Only ID
            ))}
            </div>
          </div>
        </section>

        {/* TRUST BAR */}
        <section className="py-8 bg-dark text-light border-t border-gray-700">
          <div className="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div className="flex items-center space-x-6 text-sm">
              <div className="flex items-center space-x-2"><Clock className="w-4 h-4" /> <span>24/7 Expert Support</span></div>
              <div className="flex items-center space-x-2"><Download className="w-4 h-4" /> <span>Browser Extension</span></div>
              <div className="flex items-center space-x-2"><BarChart3 className="w-4 h-4" /> <span>Extensive API Docs</span></div>
            </div>
            <div className="text-center md:text-right">
              <p className="text-lg font-bold text-accent">Trusted by {formatNumber(stats.customers)} Customers Worldwide</p>
            </div>
          </div>
        </section>
      </main>
      <WhatsappFloat />
      <Footer />
    </>
  );
}