import { Link, useNavigate } from "react-router-dom";
import {
  LogIn, UserPlus, Moon, Sun, LogOut,
  Package, Shield, User, Wallet, PlusCircle
} from "lucide-react";
import { useDarkMode } from "../../hooks/useDarkMode";
import { useAuthStore } from "../../store/authStore";

const whatsappNumber = import.meta.env.VITE_WHATSAPP_NUMBER;

export default function Header() {
  const { isDark, toggle } = useDarkMode();
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();

  return (
    <header className="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          
          {/* LOGO */}
          <Link to="/" className="flex items-center gap-3 group">
            <div className="h-12 w-14 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center shadow-md group-hover:shadow-xl transition-all">
              <img src="/logo.JPG" alt="Logo" className="h-18 w-18 object-contain" />
            </div>
            <span className="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">
              Proxies<span className="text-indigo-600">911</span>
            </span>
          </Link>

          {/* ATTRACTIVE BALANCE SECTION (Logged In Only) */}
          {user && (
            <div className="hidden md:flex items-center gap-4 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 px-4 py-2 rounded-2xl">
              <div className="flex flex-col items-end">
                <span className="text-[10px] uppercase tracking-wider text-indigo-500 dark:text-indigo-400 font-bold">Balance</span>
                <span className="text-lg font-black text-gray-900 dark:text-white leading-none">
                  ${Number(user?.balance || 0).toFixed(2)}
                </span>
              </div>
              <Link 
                to="/topup" 
                className="bg-indigo-600 hover:bg-indigo-700 text-white p-2 rounded-xl transition-all shadow-lg shadow-indigo-500/20 active:scale-95"
                title="Recharge Balance"
              >
                <PlusCircle className="w-5 h-5" />
              </Link>
            </div>
          )}

          <div className="flex items-center space-x-3">
            <button onClick={toggle} className="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
              {isDark ? <Sun className="w-5 h-5 text-yellow-400" /> : <Moon className="w-5 h-5 text-gray-700" />}
            </button>

            {user ? (
              <div className="flex items-center space-x-4">
                <div className="flex flex-col items-end hidden lg:flex">
                   <span className="text-xs text-gray-500 dark:text-gray-400">Welcome,</span>
                   <span className="text-sm font-bold text-gray-800 dark:text-gray-200">{user.name}</span>
                </div>

                <div className="flex items-center space-x-2">
                  {user.role === "user" && (
                    <Link to="/profile" className="flex items-center space-x-1 text-gray-600 dark:text-gray-400 hover:text-indigo-600" title="My Orders">
                      <Package className="w-5 h-5" />
                    </Link>
                  )}
                  {user.role === "admin" && (
                    <Link to="/admin" className="text-purple-600 dark:text-purple-400 hover:text-purple-700" title="Admin Panel">
                      <Shield className="w-5 h-5" />
                    </Link>
                  )}
                  <button 
                    onClick={() => { logout(); navigate("/login"); }}
                    className="text-red-600 dark:text-red-400 hover:text-red-700 p-1"
                  >
                    <LogOut className="w-5 h-5" />
                  </button>
                </div>
              </div>
            ) : (
              <div className="flex gap-2">
                <Link to="/login" className="flex items-center space-x-1 text-gray-700 dark:text-gray-300 hover:text-indigo-600">
                  <LogIn className="w-5 h-5" />
                  <span className="hidden sm:inline">Login</span>
                </Link>
                <Link to="/signup" className="flex items-center space-x-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                  <UserPlus className="w-5 h-5" />
                  <span className="hidden sm:inline">Sign Up</span>
                </Link>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
}