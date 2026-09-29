import { useEffect, useState, useCallback } from "react";
import type { Gear } from "../../types/gearTypes";
import api from "../../lib/axios";
import GearStatusRow from "./GearStatusRow.tsx";

export default function MyGearList() {
    const [gears, setGears] = useState<Gear[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const fetchMyGears = useCallback(() => {
        setLoading(true);
        setError(null);
        api.get('/api/mygears')
            .then((res) => setGears(res.data.data))
            .catch(() => setError("Nem sikerült betölteni az eszközeidet."))
            .finally(() => setLoading(false));
    }, [])

    useEffect(() => {
        fetchMyGears();
    }, [fetchMyGears])

    if (loading) return <p className="text-secondary text-sm px-5 py-4">Betöltés...</p>
    if (error) return <p className="text-danger text-sm px-5 py-4">{error}</p>
    if (gears.length === 0) return <p className="text-secondary text-sm px-5 py-4">Még nincs egyetlen feltöltött eszközöd sem.</p>

    return (
        <div className="flex flex-col gap-2 px-5 py-4">
            {gears.map((gear) => (
                <GearStatusRow key={gear.id} gear={gear} onStatusChanged={fetchMyGears} />
            ))}
        </div>
    )
}