import { useState } from "react";
import type { Rental } from "../../types/rentalTypes";
import api from "../../lib/axios";

function formatFt(value: number): string {
    return `${value.toLocaleString('hu-HU')} Ft`;
}

export default function IncomingRentalRow({ rental, onDecided }: { rental: Rental, onDecided: () => void }) {

    const [submitting, setSubmitting] = useState<'accepted' | 'rejected' | null>(null);

    const decide = async (status: 'accepted' | 'rejected') => {
        setSubmitting(status);
        try {
            await api.patch(`/api/rentals/${rental.id}`, { status });
            onDecided();
        } catch (error) {
            //
        } finally {
            setSubmitting(null);
        }
    };

    return (
        <div className="border border-[#d69b28] rounded-xl p-3.5">
            <div className="flex items-center justify-between mb-1.5">
                <p className="text-sm font-semibold text-ink">{rental.gear?.title ?? 'Eszköz'}</p>
                <span className="px-2 py-1 rounded-full bg-[#fdf3e2] text-[#a97416] text-[10px] font-mono">
                    pending
                </span>
            </div>
            <p className="text-xs text-secondary mb-1">
                {rental.renter?.name ?? 'Ismeretlen'} · {rental.start_date} – {rental.end_date}
            </p>
            <p className="text-xs text-secondary mb-3">{formatFt(Number(rental.total_price))}</p>

            {rental.message && (
                <p className="text-xs text-ink bg-bg rounded-lg p-2 mb-3">„{rental.message}"</p>
            )}

            <div className="flex gap-2">
                <button
                    onClick={() => decide('accepted')}
                    disabled={submitting !== null}
                    className="flex-1 py-2.5 rounded-lg bg-ink text-white text-xs font-bold disabled:opacity-60"
                >
                    {submitting === 'accepted' ? 'Mentés...' : 'Jóváhagyás'}
                </button>
                <button
                    onClick={() => decide('rejected')}
                    disabled={submitting !== null}
                    className="flex-1 py-2.5 rounded-lg border border-[#e3e0da] text-secondary text-xs font-semibold disabled:opacity-60"
                >
                    {submitting === 'rejected' ? 'Mentés...' : 'Elutasítás'}
                </button>
            </div>
        </div>
    )
}