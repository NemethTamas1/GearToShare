import { useCallback, useEffect, useState } from 'react';
import type { Gear, Category } from '../types/gearTypes.tsx';
import api from '../lib/axios.ts';
import GearUploadModal from '../components/gear-upload/GearUploadModal.tsx';

function formatFt(value: number): string {
  return `${value.toLocaleString('hu-HU')} Ft`;
}

interface HomeProps {
  cityName?: string;
  categories?: Category[];
  onSearchFocus?: () => void;
  onFindGear?: () => void;
  onListGear?: () => void;
}

export default function Home({
  cityName = 'Budapesten',
  onFindGear,
}: HomeProps) {
  const [gears, setGears] = useState<Gear[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [showUpload, setShowUpload] = useState(false);

  const fetchGears = useCallback(() => {
    setLoading(true);
    setError(null);
    api.get('/api/gears')
      .then((res) => setGears(res.data.data))
      .catch(() => setError('Nem sikerült lekérdezni az eszközöket.'))
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    fetchGears();
  }, [fetchGears]);

  return (
    <div className="h-full flex flex-col overflow-hidden bg-bg font-sans">
      <div className="flex-1 px-5 pt-4 overflow-y-auto">
        <section className="bg-ink rounded-xl px-5 py-5.5 mb-5">
          <p className="font-mono text-[9.5px] tracking-widest text-accent m-0 mb-2.5">
            {loading ? '' : `${gears.length.toLocaleString('hu-HU')} GÉP ${cityName.toUpperCase()}`}
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
              onClick={() => setShowUpload(true)}
            >
              Bérbe adnék
            </button>
          </div>
        </section>

        <div className="flex items-baseline justify-between mb-3">
          <h2 className="m-0 text-base font-bold tracking-[-0.02em] text-ink">{cityName}, most szabad</h2>
          <a className="text-[11.5px] font-semibold text-[#a97416] cursor-pointer">Összes →</a>
        </div>

        <div className="flex flex-col gap-3 pb-4">
          {loading && <p className="text-secondary text-sm">Betöltés...</p>}
          {error && <p className="text-danger text-sm">{error}</p>}

          {!loading && !error && gears.map((gear) => (
            <button
              key={gear.id}
              className="text-left cursor-pointer p-0 bg-white border border-[#e3e0da] rounded-[11px] overflow-hidden"
            >
              <div className="h-33 bg-[#eceae5] flex items-start p-2.5">
                <span className="px-2 py-1 rounded-full bg-white border border-[#e3e0da] font-mono text-[9.5px] text-success">
                  SZABAD
                </span>
              </div>
              <div className="px-3.75 pt-3.5 pb-3.75">
                <p className="text-[15px] leading-[1.3] font-semibold text-ink m-0 mb-1">{gear.title}</p>
                <p className="text-xs text-[#8b877f] m-0 mb-2.5">{gear.city}</p>
                <div className="flex items-baseline justify-between">
                  <span className="font-mono text-[16.5px] font-semibold text-ink">
                    {formatFt(Number(gear.price_per_day))}
                    <span className="text-xs text-[#8b877f] font-normal"> / nap</span>
                  </span>
                </div>
              </div>
            </button>
          ))}
        </div>
      </div>
      {showUpload && <GearUploadModal onClose={() => setShowUpload(false)} />}
    </div>
  );
}
