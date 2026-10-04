import { useEffect, useState, useCallback } from 'react';
import type { Rental } from '../../types/rentalTypes';
import api from '../../lib/axios';
import IncomingRentalRow from './IncomingRentalRow';

export default function IncomingRentalsList() {
  const [rentals, setRentals] = useState<Rental[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchIncoming = useCallback(() => {
    setLoading(true);
    setError(null);
    api.get('/api/rentals/incoming')
      .then((res) => setRentals(res.data.data))
      .catch(() => setError('Nem sikerült betölteni a kérelmeket.'))
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    fetchIncoming();
  }, [fetchIncoming]);

  if (loading) return null;
  if (error) return <p className="text-danger text-sm px-5">{error}</p>;
  if (rentals.length === 0) return null;

  return (
    <section className="px-5 py-4">
      <h2 className="text-sm font-bold text-ink mb-2">
        {rentals.length} új kérelmed van
      </h2>
      <div className="flex flex-col gap-2.5">
        {rentals.map((rental) => (
          <IncomingRentalRow key={rental.id} rental={rental} onDecided={fetchIncoming} />
        ))}
      </div>
    </section>
  );
}