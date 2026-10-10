import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import type { Rental } from '../../types/rentalTypes';
import api from '../../lib/axios';
import { useAuth } from '../../context/AuthContext';

const STATUS_LABELS: Record<string, string> = {
  pending: 'Függőben',
  accepted: 'Elfogadva',
  active: 'Folyamatban',
  completed: 'Lezárva',
  rejected: 'Elutasítva',
};

export default function MyRentalsList() {
  const { user } = useAuth();
  const [rentals, setRentals] = useState<Rental[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api.get('/api/myrentals')
      .then((res) => setRentals(res.data.data))
      .catch(() => setError('Nem sikerült betölteni a bérléseket.'))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <p className="text-secondary text-sm px-5 py-4">Betöltés...</p>;
  if (error) return <p className="text-danger text-sm px-5 py-4">{error}</p>;
  if (rentals.length === 0) return <p className="text-secondary text-sm px-5 py-4">Még nincs bérlésed.</p>;

  return (
    <div className="flex flex-col gap-2 px-5 py-4">
      {rentals.map((r) => (
        <Link
          key={r.id}
          to={`/rentals/${r.id}`}
          className="border border-[#e3e0da] rounded-xl p-3.5 block"
        >
          <div className="flex items-center justify-between mb-1">
            <p className="text-sm font-semibold text-ink">{r.gear?.title ?? 'Eszköz'}</p>
            <span className="text-[10px] font-mono text-secondary">
              {STATUS_LABELS[r.status] ?? r.status}
            </span>
          </div>
          <p className="text-xs text-secondary">
            {r.renter_id === user?.id ? 'Te bérled' : `Bérlő: ${r.renter?.name ?? '—'}`} · {r.start_date} – {r.end_date}
          </p>
        </Link>
      ))}
    </div>
  );
}