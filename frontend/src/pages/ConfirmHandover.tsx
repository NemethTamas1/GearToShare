import axios from "axios";
import { useState } from "react";
import { useParams, useSearchParams, Link } from "react-router-dom";
import api from "../lib/axios";

export default function ConfirmHandover() {
    const { id } = useParams();
    const [params] = useSearchParams();
    const token = params.get('token');
    const [submitting, setSubmitting] = useState(false);
    const [done, setDone] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const confirm = async () => {
        setSubmitting(true);
        setError(null);

        try {
            await api.post(`/api/rentals/${id}/confirm-handover`, { token });
            setDone(true);
        } catch (err) {
            setError(
                axios.isAxiosError(err)
                    ? err.response?.data?.message ?? 'Nem sikerült megerősíteni az tátvételt.'
                    : 'Nem sikerült megerősíteni az átvételt'
            );
        } finally {
            setSubmitting(false);
        }
    };

    if (!token) return <p className="text-danger text-sm px-5 py-4">Hiányzó átvételi kód.</p>

    return (
        <div className="h-full bg-bg font-sans px-5 py-8 flex flex-col gap-4">
            {done ? (<>
                <p className="text-success text-base font-semibold">Átvétel megerősítve, a bérlés elindult.</p>
                <Link to={`/rentals/${id}`} className="text-sm font-semibold text-[#a97416]">Bérlés megtekintése</Link>
            </>) : (<>
                <h1 className="text-lg font-extrabold text-ink">Átvétel megerősítése</h1>
                <p className="text-sm text-secondary">Megerősíted, hogy átvetted az eszközt?</p>
                {error && <p className="text-danger text-sm">{error}</p>}
                <button onClick={confirm} disabled={submitting} className="w-full py-4 rounded-xl bg-accent text-ink font-bold text-[15.5px] disabled:opacity-60">
                    {submitting ? 'Mentés...' : 'Igen, átvettem'}
                </button>
            </>)}
        </div>
    )
}
