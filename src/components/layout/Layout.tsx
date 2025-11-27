// src/components/layout/Layout.tsx
import Header from './Header';
import Footer from './Footer';
import { ReactNode } from 'react';
import WhatsappFloat from '../WhatsappFloat';

interface LayoutProps {
  children: ReactNode;
}

export default function Layout({ children }: LayoutProps) {
  return (
    <div className="min-h-screen flex flex-col">
      <Header />
      <main className="flex-1">{children}</main>
      <WhatsappFloat />
      <Footer />
    </div>
  );
}