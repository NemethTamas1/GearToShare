import axios from "axios";
import { useState } from "react";
import { useParams, useSearchParams } from "react-router-dom";
import api from "../lib/axios";

export default function ConfirmHandover() {
    const { id } = useParams();
    const [params] = useSearchParams();
    const token = params.get('token');
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const confirm = async () => {
        setSubmitting(true);
        setError(null);

        try {
            const res = await api.post(`/api/rentals/${id}/confirm-handover`, { token });
            window.location.href = res.data.redirect_url;
        } catch (err) {
            setError(
                axios.isAxiosError(err)
                    ? err.response?.data?.message ?? 'Nem sikerült elindítani a fizetést.'
                    : 'Nem sikerült elindítani a fizetést.'
            );
        } finally {
            setSubmitting(false);
        }
    };

    if (!token) return <p className="text-danger text-sm px-5 py-4">Hiányzó átvételi kód.</p>

    return (
        <div className="h-full bg-bg font-sans px-5 py-8 flex flex-col gap-4">
            <h1 className="text-lg font-extrabold text-ink">Átvétel megerősítése</h1>
            <p className="text-sm text-secondary">
                Megerősíted, hogy átvetted az eszközt? A következő lépésben a fizetés indul.
            </p>
            {error && <p className="text-danger text-sm">{error}</p>}
            <button
                onClick={confirm}
                disabled={submitting}
                className="w-full py-4 rounded-xl bg-accent text-ink font-bold text-[15.5px] disabled:opacity-60"
            >
                {submitting ? 'Mentés...' : 'Igen, átvettem'}
            </button>
        </div>
    );
}
