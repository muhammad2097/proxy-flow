export default function Footer() {
  return (
    <footer className="bg-dark text-light py-12 mt-20 dark:bg-gray-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 className="text-lg font-bold mb-4">ProxyFlow</h3>
            <p className="text-sm text-gray-400 dark:text-gray-500">Premium proxy solutions for privacy, speed, and reliability.</p>
          </div>
          <div>
            <h4 className="font-semibold mb-3 dark:text-gray-300">Products</h4>
            <ul className="space-y-2 text-sm text-gray-400 dark:text-gray-500">
              <li><a href="#" className="hover:text-accent">IPv4 Proxy</a></li>
              <li><a href="#" className="hover:text-accent">IPv6 Proxy</a></li>
              <li><a href="#" className="hover:text-accent">Shared Proxy</a></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-3 dark:text-gray-300">Company</h4>
            <ul className="space-y-2 text-sm text-gray-400 dark:text-gray-500">
              <li><a href="#" className="hover:text-accent">About</a></li>
              <li><a href="#" className="hover:text-accent">Contact</a></li>
              <li><a href="#" className="hover:text-accent">API</a></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-3 dark:text-gray-300">Support</h4>
            <ul className="space-y-2 text-sm text-gray-400 dark:text-gray-500">
              <li><a href="#" className="hover:text-accent">Help Center</a></li>
              <li><a href="#" className="hover:text-accent">Status</a></li>
              <li><a href="#" className="hover:text-accent">Live Chat</a></li>
            </ul>
          </div>
        </div>
        <div className="mt-8 pt-8 border-t border-gray-800 text-center text-sm text-gray-500 dark:border-gray-700">
          © 2025 ProxyFlow. All rights reserved.
        </div>
      </div>
    </footer>
  );
}