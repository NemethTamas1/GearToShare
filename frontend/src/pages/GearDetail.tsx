import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import type { Gear } from "../types/gearTypes";
import api from "../lib/axios";

const CATEGORY_LABELS: Record<string, string> = {
    hand_tool: 'Kézi szerszám',
    cordless: 'Akkus',
    corded: 'Vezetékes',
    machine: 'Munkagép',
};

function formatFt(value: number): string {
    return `${value.toLocaleString('hu-HU')} Ft`;
};

export default function GearDetail() {
    const { id } = useParams();
    const [gear, setGear] = useState<Gear | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        api.get(`/api/gears/${id}`)
            .then((res) => setGear(res.data.data))
            .catch(() => setError("Nem sikerült betölteni a kiváladztott eszközt."))
            .finally(() => setLoading(false));
    }, [id]);


    if (loading) return <p className="text-secondary text-sm px-5 py-4">Betöltés...</p>;
    if (error || !gear) return <p className="text-danger text-sm px-5 py-4">{error ?? 'Az eszköz nem található.'}</p>;

    return (
        <div className="h-full overflow-y-auto bg-bg font-sans">
            <Link to="/" className="block px-5 pt-4 text-sm text-secondary">‹ Vissza</Link>

            <div className="mx-5 mt-3 aspect-video bg-[#eceae5] rounded-xl border border-[#e3e0da] flex items-center justify-center text-secondary text-xs">
                Fotó hamarosan
            </div>

            <div className="px-5 py-4">
                <span className="inline-block px-2 py-1 rounded-full bg-white border border-[#e3e0da] font-mono text-[9.5px] text-secondary mb-2">
                    {CATEGORY_LABELS[gear.category] ?? gear.category}
                </span>
                <h1 className="text-xl font-extrabold text-ink mb-1">{gear.title}</h1>
                <p className="text-sm text-secondary mb-4">{gear.city}</p>

                <p className="text-sm text-ink leading-relaxed mb-5">{gear.description}</p>

                {gear.attributes && Object.keys(gear.attributes).length > 0 && (
                    <div className="border-t border-[#e3e0da] pt-4 mb-5">
                        <h2 className="text-xs font-mono tracking-wider text-secondary mb-2">MŰSZAKI ADATOK</h2>
                        <dl className="flex flex-col gap-1.5 text-sm">
                            {Object.entries(gear.attributes).map(([key, value]) => (
                                <div key={key} className="flex justify-between">
                                    <dt className="text-secondary">{key}</dt>
                                    <dd className="text-ink font-medium">{String(value)}</dd>
                                </div>
                            ))}
                        </dl>
                    </div>
                )}

                <div className="border-t border-[#e3e0da] pt-4">
                    <p className="font-mono text-xl font-semibold text-ink mb-4">
                        {formatFt(Number(gear.price_per_day))}
                        <span className="text-sm text-secondary font-normal"> / nap</span>
                    </p>
                    <button
                        disabled
                        className="w-full py-4 rounded-xl bg-[#e3e0da] text-secondary font-bold text-[15.5px] cursor-not-allowed"
                    >
                        Foglalás — hamarosan
                    </button>
                </div>
            </div>
        </div>
    );
}