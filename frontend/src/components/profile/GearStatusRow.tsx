import { useState } from "react";
import type { Gear } from "../../types/gearTypes";
import api from "../../lib/axios";

const STATUS_LABELS: Record<string, string> = {
    draft: 'Vázlat',
    available: 'Elérhető',
    unavailable: 'Szüneteltetve',
};

const STATUS_STYLES: Record<string, string> = {
    draft: 'bg-[#eceae5] text-secondary',
    available: 'bg-[#dff3ea] text-success',
    unavailable: 'bg-[#f6e2da] text-danger',
};

export default function GearStatusRow({ gear, onStatusChanged }: { gear: Gear, onStatusChanged: () => void }) {
    const [confirming, setConfirming] = useState(false);
    const [submitting, setSubmitting] = useState(false);

    const publish = async () => {
        setSubmitting(true);
        try {
            await api.patch(`/api/gears/${gear.id}/status`, { status: 'available' });
            onStatusChanged();
        } catch (error) {
            //
        } finally {
            setSubmitting(false);
            setConfirming(false);
        }
    }

    return (
        <div className="border border-[#e3e0da] rounded-xl p-3.5">
            <div className="flex items-center justify-between mb-2">
                <p className="text-sm font-semibold text-ink">{gear.title}</p>
                <span className={`px-2 py-1 rounded-full text-[10px] font-mono ${STATUS_STYLES[gear.status]}`}>
                    {STATUS_LABELS[gear.status]}
                </span>
            </div>

            {gear.status === 'draft' && !confirming && (
                <button
                    onClick={() => setConfirming(true)}
                    className="text-xs font-semibold text-[#a97416]"
                >
                    Közzététel
                </button>
            )}

            {confirming && (
                <div className="bg-bg rounded-lg p-3 mt-2">
                    <p className="text-xs text-secondary mb-2">
                        Biztos elérhetővé teszed ezt az eszközt? Mindenki látni fogja.
                    </p>
                    <div className="flex gap-2">
                        <button
                            onClick={publish}
                            disabled={submitting}
                            className="flex-1 py-2 rounded-lg bg-accent text-ink text-xs font-bold disabled:opacity-60"
                        >
                            {submitting ? 'Mentés...' : 'Igen, közzéteszem'}
                        </button>
                        <button
                            onClick={() => setConfirming(false)}
                            className="flex-1 py-2 rounded-lg border border-[#e3e0da] text-secondary text-xs font-semibold"
                        >
                            Mégse
                        </button>
                    </div>
                </div>
            )}
        </div>
    )
}