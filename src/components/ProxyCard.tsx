// src/components/ProxyCard.tsx
import { Globe, Users, Shield, ChevronDown, Calendar, Clock, CalendarDays } from 'lucide-react';
import { useProxyStore } from '../store/proxyStore';
import { useState, useRef, useEffect } from 'react';
import { getFlagSvgUrl } from '../utils/flags';
import { useOrderStore } from '../store/orderStore';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../store/authStore';

const whatsappNumber = import.meta.env.VITE_WHATSAPP_NUMBER;

interface Props {
  proxyId: string; // ← CHANGE: Only pass ID
}

const icons = { IPv6: Globe, IPv4: Shield, Shared: Users };

const periodOptions = [
  { value: '3 days', label: '3 days', icon: Clock },
  { value: '1 week', label: '1 week', icon: Calendar },
  { value: '1 month', label: '1 month', icon: CalendarDays },
] as const;

const periodMultiplier: Record<string, number> = {
  '3 days': 1,
  '1 week': 1,
  '1 month': 1,
};

export default function ProxyCard({ proxyId }: Props) {
  const { proxyTypes, countries, updateProxyCountry } = useProxyStore();
  
  useEffect(() => {
    useProxyStore.getState().fetchProxyTypes();
  }, []);
  
  // ← ALWAYS GET FRESH PROXY FROM STORE
  const freshProxy = proxyTypes.find(p => p.id === proxyId)!;
  if (!freshProxy) return null;

  const Icon = icons[freshProxy.type];
  const selectedCountry = countries.find(c => c.name === freshProxy.country)!;

  const [count, setCount] = useState(1);
  const [period, setPeriod] = useState<'3 days' | '1 week' | '1 month'>('1 week');
  const [isCountryOpen, setIsCountryOpen] = useState(false);
  const [isPeriodOpen, setIsPeriodOpen] = useState(false);

  const countryRef = useRef<HTMLDivElement>(null);
  const periodRef = useRef<HTMLDivElement>(null);

  const selectedPeriod = periodOptions.find(p => p.value === period)!;

  // Cost per unit based on period selection
  let costPerUnit = 0;

  if (period === '1 week') {
    costPerUnit = freshProxy.costPerWeek;
  } 
  else if (period === '1 month') {
    costPerUnit = freshProxy.costPerMonth;
  } 
  else if (period === '3 days') {
    // 3-day cost derived from weekly cost
    costPerUnit = freshProxy.costPerWeek * (3 / 7);
  }

  // Calculate total
  const totalPrice = Number((costPerUnit * count).toFixed(2));

    const { user } = useAuthStore();
    const navigate = useNavigate();
    const { addOrder } = useOrderStore();

    const handleBuy = async () => {
    // 1. CHECK IF LOGGED IN
    if (!user) {
        alert('Please login to place an order.');
        navigate('/login', { replace: true });
        return;
    }

    try {
        // 2. PLACE ORDER
        const order = {
          proxyType: freshProxy.type,
          country: freshProxy.country,
          count,
          period,
          totalPrice,
        };

        await addOrder(order);

    // === WHATSAPP MESSAGE ===
    const message = `
*New Order Placed!*

*Customer:* ${user.name}
*Email:* ${user.email}

*Proxy:* ${order.proxyType}
*Country:* ${order.country}
*Quantity:* ${order.count} × ${order.period}
*Total:* $${order.totalPrice}

*Time:* ${new Date().toLocaleString()}
    `.trim();

        const encodedMessage = encodeURIComponent(message);
        const whatsappUrl = `https://wa.me/$${whatsappNumber}?text=${encodedMessage}`;

        // === OPEN WHATSAPP WITH MESSAGE ===
        const link = document.createElement('a');
        link.href = whatsappUrl;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        alert('Order placed! Details sent to WhatsApp.');
        navigate('/profile');
    } catch (error: any) {
        // 4. ERROR HANDLING
        if (error.message?.includes('token')) {
        alert('Session expired. Please login again.');
        navigate('/login', { replace: true });
        } else {
        alert(error.message || 'Failed to place order');
        }
    }
    };

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (
        countryRef.current && !countryRef.current.contains(event.target as Node) &&
        periodRef.current && !periodRef.current.contains(event.target as Node)
      ) {
        setIsCountryOpen(false);
        setIsPeriodOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow border border-gray-100">
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl">
            <Icon className="w-6 h-6 text-white" />
          </div>
          <div>
            <h3 className="text-xl font-bold text-gray-900">{freshProxy.type} Proxy</h3>
            <p className="text-sm text-gray-500">
              {freshProxy.type === 'IPv6' && 'Best for IPv6 sites'}
              {freshProxy.type === 'IPv4' && 'Premium dedicated'}
              {freshProxy.type === 'Shared' && 'Up to 3 users'}
            </p>
          </div>
        </div>
      </div>

      <div className="space-y-5 mb-6">
        <div ref={countryRef}>
          <label className="block text-sm font-medium text-gray-700 mb-2">Country</label>
          <div className="relative">
            <button
              onClick={() => {
                setIsCountryOpen(!isCountryOpen);
                setIsPeriodOpen(false);
              }}
              className="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500"
            >
              <span className="flex items-center gap-2">
                <img src={getFlagSvgUrl(selectedCountry.code)} alt="" className="w-6 h-5 rounded-sm" />
                <span>{selectedCountry.name}</span>
              </span>
              <ChevronDown className={`w-5 h-5 transition-transform ${isCountryOpen ? 'rotate-180' : ''}`} />
            </button>

            {isCountryOpen && (
              <div className="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                {countries.map((c) => (
                  <button
                    key={c.code}
                    onClick={() => {
                      updateProxyCountry(freshProxy.id, c.name);
                      setIsCountryOpen(false);
                    }}
                    className="w-full px-4 py-3 flex items-center gap-2 hover:bg-gray-50 text-left"
                  >
                    <img src={getFlagSvgUrl(c.code)} alt="" className="w-6 h-5 rounded-sm" />
                    <span>{c.name}</span>
                  </button>
                ))}
              </div>
            )}
          </div>

          <div className="mt-2 flex items-center justify-between px-1">
            <span className="text-xs text-gray-500">Available:</span>
            <span className={`text-sm font-bold flex items-center gap-2 ${selectedCountry.stock > 100 ? 'text-green-600' : 'text-orange-600'}`}>
              <img src={getFlagSvgUrl(selectedCountry.code)} alt="" className="w-5 h-4 rounded-sm" />
              {selectedCountry.stock.toLocaleString()}
            </span>
          </div>
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Count</label>
            <input
              type="number"
              min="1"
              value={count}
              onChange={(e) => setCount(Math.max(1, parseInt(e.target.value) || 1))}
              className="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900"
            />
          </div>

          <div ref={periodRef}>
            <label className="block text-sm font-medium text-gray-700 mb-2">Period</label>
            <div className="relative">
              <button
                onClick={() => {
                  setIsPeriodOpen(!isPeriodOpen);
                  setIsCountryOpen(false);
                }}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500"
              >
                <span className="flex items-center gap-2">
                  <selectedPeriod.icon className="w-5 h-5 text-blue-600" />
                  <span>{selectedPeriod.label}</span>
                </span>
                <ChevronDown className={`w-5 h-5 transition-transform ${isPeriodOpen ? 'rotate-180' : ''}`} />
              </button>

              {isPeriodOpen && (
                <div className="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">
                  {periodOptions.map((p) => (
                    <button
                      key={p.value}
                      onClick={() => {
                        setPeriod(p.value);
                        setIsPeriodOpen(false);
                      }}
                      className="w-full px-4 py-3 flex items-center gap-2 hover:bg-gray-50 text-left"
                    >
                      <p.icon className="w-5 h-5 text-blue-600" />
                      <span>{p.label}</span>
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        <div className="flex justify-between items-center py-4 px-5 bg-gray-50 rounded-lg">
          <span className="font-semibold text-gray-700">Total Price</span>
          <span className="text-2xl font-bold text-blue-600">${totalPrice}</span>
        </div>
      </div>

      <button
        onClick={handleBuy}
        className="w-full bg-gradient-to-r from-blue-600 to-orange-500 text-white font-bold py-3 rounded-xl hover:shadow-lg transform hover:scale-105 transition-all"
      >
        Buy Now
      </button>
    </div>
  );
}