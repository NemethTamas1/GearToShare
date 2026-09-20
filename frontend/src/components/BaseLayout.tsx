// src/components/Layout.tsx
import { Outlet } from 'react-router-dom';
import NavBar from './NavBar';

export default function Layout() {
  return (
    <div className="min-h-screen bg-bg">
      <NavBar />
      <Outlet />
    </div>
  );
}