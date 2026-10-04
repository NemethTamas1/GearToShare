import { useAuth } from '../context/AuthContext';
import MyGearList from '../components/profile/MyGearList';
import IncomingRentalsList from '../components/profile/IncomingRentalsList';


export default function Profile() {

  const { user } = useAuth();

  if (!user) return <p>Nincs bejelentkezve.</p>;

  const initials = user.name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .slice(0, 2)
    .toUpperCase();

  return (
    <div className="h-full overflow-y-auto bg-bg font-sans">
      <header className="px-5 pt-6 pb-4 bg-white border-b border-[#e3e0da] flex items-center gap-3.5">
        <div className="w-14 h-14 rounded-full bg-[#eceae5] border border-[#e3e0da] flex items-center justify-center text-lg font-semibold text-secondary">
          {initials}
        </div>
        <div>
          <p className="text-lg font-bold text-ink">{user.name}</p>
          <p className="text-sm text-secondary">{user.email}</p>
        </div>
      </header>

      <div className="grid grid-cols-3 gap-px bg-[#e3e0da] mx-5 mt-4 rounded-xl overflow-hidden border border-[#e3e0da]">
        <StatCell value="—" label="ÉRTÉKELÉS" />
        <StatCell value="—" label="BÉRLÉS" />
        <StatCell value="—" label="ESZKÖZ" />
      </div>

      <div className="mx-5 mt-4 bg-ink rounded-xl px-5 py-5">
        <p className="font-mono text-[9.5px] tracking-widest text-accent mb-2">EZ A HÓNAP</p>
        <p className="text-2xl font-extrabold text-white mb-1">— Ft</p>
        <p className="text-xs text-[#b8b4ac]">Minta adat — a bevétel-számítás jövőbeli munka</p>
      </div>

      <IncomingRentalsList />

      <section className="mt-5">
        <h2 className="px-5 pb-1 text-sm font-bold text-ink">Eszközeim</h2>
        <MyGearList />
      </section>
    </div>
  );
}

function StatCell({ value, label }: { value: string; label: string }) {
  return (
    <div className="bg-white px-3 py-3.5 text-center">
      <p className="text-xl font-extrabold text-ink font-mono">{value}</p>
      <p className="text-[9px] tracking-wider text-secondary mt-0.5">{label}</p>
    </div>
  );
}