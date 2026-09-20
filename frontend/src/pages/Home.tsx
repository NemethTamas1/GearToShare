import { useState } from 'react';
import type { Gear, Category } from '../types/gearTypes.tsx';
import { useAuth } from '../context/AuthContext.tsx';
import { useNavigate } from 'react-router-dom';


const CATEGORIES: Category[] = [
  { id: 'hand', label: 'Kézi szerszám', count: 612 },
  { id: 'electric', label: 'Elektromos', count: 498 },
  { id: 'battery', label: 'Akkus', count: 377 },
  { id: 'garden', label: 'Kerti gép', count: 214 },
  { id: 'heavy', label: 'Munkagép', count: 141 },
];

const NEARBY_GEARS: Gear[] = [
  { id: 'gear-1042', name: 'Bosch GSH 11 VC vésőkalapács', location: 'Budapest, XIII. kerület', pricePerDay: 8900, rating: 4.9, reviewCount: 23, available: true },
  { id: 'gear-0871', name: 'Makita DHP484 akkus fúró', location: 'Budapest, II. kerület', pricePerDay: 2400, rating: 4.8, reviewCount: 61, available: true },
  { id: 'gear-0512', name: 'Wacker Neuson lapvibrátor', location: 'Budaörs', pricePerDay: 12000, rating: 5.0, reviewCount: 9, available: true },
];

function formatFt(value: number): string {
  return `${value.toLocaleString('hu-HU')} Ft`;
}

interface GearHomeMobileProps {
  cityName?: string;
  gearsInCity?: number;
  gears?: Gear[];
  categories?: Category[];
  onSearchFocus?: () => void;
  onFindGear?: () => void;
  onListGear?: () => void;
  onGearSelect?: (gearId: string) => void;
  onNavigate?: (tab: 'browse' | 'favorites' | 'rentals' | 'profile') => void;
}

export default function GearHomeMobile({
  cityName = 'Budapesten',
  gearsInCity = 1842,
  gears = NEARBY_GEARS,
  categories = CATEGORIES,
  onSearchFocus,
  onFindGear,
  onListGear,
  onGearSelect,
}: GearHomeMobileProps) {
  const [activeCategory, setActiveCategory] = useState<string | null>(null);
  const { logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="h-full flex flex-col overflow-hidden bg-bg font-sans">
      <header className="flex-none px-5 pt-4 pb-3.5 bg-white border-b border-[#e3e0da]">
        <div className="flex items-center justify-between mb-3">
          <div className="font-mono font-extrabold text-[13px] tracking-[0.12em] text-ink">
            GEAR<span className="text-accent">·</span>TOSHARE
          </div>
          <button
            className="w-7.5 h-7.5 rounded-full bg-[#eceae5] border border-[#e3e0da] flex items-center justify-center text-[11px] font-semibold text-secondary"
            aria-label="Profil"
          >
            TN
          </button>
        </div>
        <button
          className="w-full flex items-center gap-2.5 px-3.5 py-2.5 bg- border border-[#d6d2cb] rounded-[9px] text-left"
          onClick={onSearchFocus}
        >
          <span className="w-3.25 h-3.25 rounded-full border-2 border-[#8b877f] flex-none" />
          <span className="flex-1 text-sm text-[#8b877f]">Mit keresel?</span>
        </button>
      </header>

      <div className="flex-1 px-5 pt-4 overflow-y-auto">
        <section className="bg-ink rounded-xl px-5 py-5.5 mb-5">
          <p className="font-mono text-[9.5px] tracking-widest text-accent m-0 mb-2.5">
            {gearsInCity.toLocaleString('hu-HU')} GÉP {cityName.toUpperCase()}
          </p>
          <h1 className="text-[23px] font-extrabold leading-[1.15] tracking-tight text-white m-0 mb-2">
            Hétvégi munka, hétköznapi ár.
          </h1>
          <p className="text-[13px] leading-relaxed text-[#b8b4ac] m-0 mb-4">
            Napi bérlés, jóváhagyás után az átadásnál fizetsz.
          </p>
          <div className="flex gap-2">
            <button
              className="cursor-pointer px-3.75 py-2.5 rounded-[7px] font-bold text-[12.5px] bg-accent text-ink"
              onClick={onFindGear}
            >
              Gépet keresek
            </button>
            <button
              className="cursor-pointer px-3.75 py-2.5 rounded-[7px] font-semibold text-[12.5px] bg-transparent border border-[#3a3833] text-white"
              onClick={onListGear}
            >
              Bérbe adnék
            </button>
          </div>
        </section>

        <nav className="flex gap-2 overflow-x-auto mb-5 pb-0.5 [scrollbar-none] [&::-webkit-scrollbar]:hidden" aria-label="Kategóriák">
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setActiveCategory(activeCategory === cat.id ? null : cat.id)}
              className={`cursor-pointer px-3.25 py-2 rounded-full text-xs whitespace-nowrap flex-none ${activeCategory === cat.id
                  ? 'bg-ink border border-ink text-white font-semibold'
                  : 'bg-white border border-[#e3e0da] text-secondary font-medium'
                }`}
            >
              {cat.label}
            </button>
          ))}
        </nav>

        <div className="flex items-baseline justify-between mb-3">
          <h2 className="m-0 text-base font-bold tracking-[-0.02em] text-ink">{cityName}, most szabad</h2>
          <a className="text-[11.5px] font-semibold text-[#a97416] cursor-pointer">Összes →</a>
        </div>

        <div className="flex flex-col gap-3 pb-4">
          {gears.map((gear) => (
            <button
              key={gear.id}
              onClick={() => onGearSelect?.(gear.id)}
              className="text-left cursor-pointer p-0 bg-white border border-[#e3e0da] rounded-[11px] overflow-hidden"
            >
              <div
                className="h-33 bg-[#eceae5] bg-cover bg-center flex items-start p-2.5"
                style={gear.photoUrl ? { backgroundImage: `url(${gear.photoUrl})` } : undefined}
              >
                <span
                  className={`px-2 py-1 rounded-full bg-white border border-[#e3e0da] font-mono text-[9.5px] ${gear.available ? 'text-success' : 'text-[#8a6412]'
                    }`}
                >
                  {gear.available ? 'SZABAD' : 'FOGLALT'}
                </span>
              </div>
              <div className="px-3.75 pt-3.5 pb-3.75">
                <p className="text-[15px] leading-[1.3] font-semibold text-ink m-0 mb-1">{gear.name}</p>
                <p className="text-xs text-[#8b877f] m-0 mb-2.5">{gear.location}</p>
                <div className="flex items-baseline justify-between">
                  <div>
                    <span className="font-mono text-[16.5px] font-semibold text-ink">{formatFt(gear.pricePerDay)}</span>
                    <span className="text-xs text-[#8b877f]"> / nap</span>
                  </div>
                  <span className="font-mono text-xs font-medium text-secondary">★ {gear.rating.toFixed(1)} · {gear.reviewCount}</span>
                </div>
              </div>
            </button>
          ))}
        </div>
      </div>

      <button onClick={handleLogout}> Kijelentkezés </button>
    </div>
  );
}
