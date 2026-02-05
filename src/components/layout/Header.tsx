// src/components/layout/Header.tsx
import { Link, useNavigate } from "react-router-dom";
import {
  LogIn,
  UserPlus,
  Moon,
  Sun,
  LogOut,
  Package,
  Shield,
  User,
} from "lucide-react";
import { useDarkMode } from "../../hooks/useDarkMode";
import { useAuthStore } from "../../store/authStore";
const whatsappNumber = import.meta.env.VITE_WHATSAPP_NUMBER;

export default function Header() {
  const { isDark, toggle } = useDarkMode();
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();

  return (
    <header className="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <Link to="/" className="flex items-center gap-3 group">
            <div className="h-12 w-14 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center shadow-md group-hover:shadow-xl transition-all">
              <img
                src="/logo.JPG"
                alt="Proxies911 Logo"
                className="h-18 w-18 object-contain"
              />
            </div>

            <span className="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 transition">
              Proxies<span className="text-indigo-600">911</span>
            </span>
          </Link>

          <nav className="hidden md:flex space-x-8">
            <Link
              to="/"
              className="text-gray-700 dark:text-gray-300 hover:text-primary font-medium"
            >
              Buy Proxy
            </Link>
            <Link
              to="/pricing"
              className="text-gray-700 dark:text-gray-300 hover:text-primary font-medium"
            >
              Pricing
            </Link>
            <Link
              to="/faq"
              className="text-gray-700 dark:text-gray-300 hover:text-primary font-medium"
            >
              FAQ
            </Link>
            {/* {user?.role === 'admin' && (
              <Link to="/admin" className="text-gray-700 dark:text-gray-300 hover:text-primary font-medium">Admin</Link>
            )} */}
          </nav>

          <div className="flex items-center space-x-3">
            <button
              onClick={toggle}
              className="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700"
            >
              {isDark ? (
                <Sun className="w-5 h-5 text-yellow-400" />
              ) : (
                <Moon className="w-5 h-5 text-gray-700" />
              )}
            </button>

            {user ? (
              <div className="flex items-center space-x-4">
                {/* USER MENU */}
                <div className="flex items-center space-x-2 text-sm">
                  <User className="w-4 h-4 text-gray-600 dark:text-gray-400" />
                  <span className="font-medium text-gray-800 dark:text-gray-200">
                    {user.name}
                  </span>
                </div>

                {/* ORDERS & PROFILE/ADMIN */}
                <div className="flex items-center space-x-2">
                  {user.role === "user" && (
                    <Link
                      to="/profile"
                      className="flex items-center space-x-1 text-gray-600 dark:text-gray-400 hover:text-primary"
                      title="My Orders"
                    >
                      <Package className="w-5 h-5" />
                      <span className="hidden sm:inline">Orders</span>
                    </Link>
                  )}
                  {user.role === "admin" && (
                    <Link
                      to="/admin"
                      className="flex items-center space-x-1 text-purple-600 dark:text-purple-400 hover:text-purple-700"
                      title="Admin Panel"
                    >
                      <Shield className="w-5 h-5" />
                      <span className="hidden sm:inline">Admin</span>
                    </Link>
                  )}
                </div>
                <a
                  href={`https://wa.me/${whatsappNumber}?text=Hi%20Proxies911%20Support!`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center space-x-1 text-green-600 hover:text-green-700"
                >
                  <svg
                    className="w-6 h-6"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                  </svg>
                  <span className="hidden sm:inline font-medium">
                    Contact Us
                  </span>
                </a>
                {/* LOGOUT */}
                <button
                  onClick={() => {
                    logout();
                    navigate("/login");
                  }}
                  className="flex items-center space-x-1 text-red-600 dark:text-red-400 hover:text-red-700"
                >
                  <LogOut className="w-5 h-5" />
                  <span className="hidden sm:inline">Logout</span>
                </button>
              </div>
            ) : (
              <>
                <Link
                  to="/login"
                  className="flex items-center space-x-1 text-gray-700 dark:text-gray-300 hover:text-primary"
                >
                  <LogIn className="w-5 h-5" />
                  <span className="hidden sm:inline">Login</span>
                </Link>
                <Link
                  to="/signup"
                  className="flex items-center space-x-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                  <UserPlus className="w-5 h-5" />
                  <span className="hidden sm:inline">Sign Up</span>
                </Link>
              </>
            )}
          </div>
        </div>
      </div>
    </header>
  );
}
