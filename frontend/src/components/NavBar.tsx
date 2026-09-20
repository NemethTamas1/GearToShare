// src/components/NavBar.tsx
import NavBarProfile from './NavBarProfile';

export default function NavBar() {
  return (
    <header className="flex-none px-5 pt-4 pb-3.5 bg-white border-b border-[#e3e0da] flex items-center justify-between">
      <div className="font-mono font-extrabold text-[13px] tracking-[0.12em] text-ink">
        GEAR<span className="text-accent">·</span>TOSHARE
      </div>
      <NavBarProfile />
    </header>
  );
}