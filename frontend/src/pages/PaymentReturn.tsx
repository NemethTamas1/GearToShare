import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import api from '../lib/axios';

type Status = 'loading' | 'success' | 'error';

export default function PaymentReturn() {
  const { id } = useParams();
  const [status, setStatus] = useState<Status>('loading');
  const [message, setMessage] = useState<string | null>(null);

  useEffect(() => {
    api.post(`/api/rentals/${id}/complete-payment`)
      .then(() => setStatus('success'))
      .catch((err) => {
        setMessage(
          axios.isAxiosError(err)
            ? err.response?.data?.message ?? 'Nem sikerült ellenőrizni a fizetést.'
            : 'Nem sikerült ellenőrizni a fizetést.'
        );
        setStatus('error');
      });
  }, [id]);

  return (
    <div className="h-full bg-bg font-sans px-5 py-8 flex flex-col gap-4">
      {status === 'loading' && <p className="text-secondary text-sm">Fizetés ellenőrzése...</p>}
      {status === 'success' && (
        <>
          <p className="text-success text-base font-semibold">Fizetés sikeres, a bérlés elindult.</p>
          <Link to={`/rentals/${id}`} className="text-sm font-semibold text-[#a97416]">Bérlés megtekintése</Link>
        </>
      )}
      {status === 'error' && (
        <>
          <p className="text-danger text-sm">{message}</p>
          <Link to={`/rentals/${id}`} className="text-sm font-semibold text-[#a97416]">Vissza a bérléshez</Link>
        </>
      )}
    </div>
  );
}