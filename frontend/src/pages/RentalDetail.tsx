import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { QRCodeSVG } from "qrcode.react";
import type { RentalDetail as RentalDetailData } from "../types/rentalTypes";
import api from "../lib/axios";

const STATUS_LABELS: Record<string, string> = {
    pending: 'Függőben',
    accepted: 'Elfogadva',
    active: 'Aktív',
    completed: 'Lezárva',
    rejected: 'Elutasítva'
};

export default function RentalDetail() {
    const { id } = useParams();
    const [rental, setRental] = useState<RentalDetailData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        api.get(`/api/rentals/${id}`)
            .then((res) => setRental(res.data.data))
            .catch(() => setError('Nem sikerült betölteni a bérlést.'))
            .finally(() => setLoading(false))
    }, [id]);

    if (loading) return <p className="text-secondary text-sm px-5 py-4">Betöltés...</p>
    if (error || !rental) return <p className="text-danger text-sm px-5 py-4">{error ?? 'Nem található.'}</p>

    const qrUrl = rental.handover_token
        ? `${window.location.origin}/rentals/${rental.id}/confirm-handover?token=${rental.handover_token}`
        : null;

    return (
        <div className="h-full overflow-y-auto bg-bg font-sans">
            <Link to="/profile" className="block px-5 pt-4 text-sm text-secondary">‹ Vissza</Link>

            <div className="px-5 py-4">
                <h1 className="text-xl font-extrabold text-ink mb-1">{rental.gear?.title ?? 'Eszköz'}</h1>
                <p className="text-sm text-secondary mb-1">
                    {STATUS_LABELS[rental.status] ?? rental.status} · {rental.start_date} - {rental.end_date}
                </p>
                <p className="font-mono text-sm text-ink mb-4">
                    {Number(rental.total_price).toLocaleString('hu-HU')} Ft
                </p>

                {rental.message && (
                    <p className="text-xs text-ink bg-white border border-[#e3e0da] rounded-lg p-3 b-4">„{rental.message}"</p>
                )}

                {rental.contact && (
                    <div className="border border-[#e3e0da] bg-white rounded-xl p-3.5 mb-4">
                        <h2 className="text-xs font-mono tracking-wider text-secondary mb-2">ELÉRHETŐSÉG</h2>
                        {rental.contact.address && <p className="text-sm text-ink mb-1">{rental.contact.address}</p>}
                        {rental.contact.phone
                            ? <a href={`tel: ${rental.contact.phone}`} className="text-sm font-semibold text-[#a97416]">{rental.contact.phone}</a>
                            : <p className="text-sm text-secondary">Nincs megadott telefonszám.</p>}
                    </div>
                )}

                {qrUrl && (
                    <div className="border border-[#e3e0da] bg-white rounded-xl p-4 flex flex-col items-center">
                        <h2 className="text-xs font-mono tracking-wider text-sec mb-3">ÁTVÉTELI KÓD</h2>
                        <QRCodeSVG value={qrUrl} size={200}/>
                        <p className="text-xs text-secondary mt-3 text-center">
                            Találkozáskor a bérlő beolvassa ezt a kódot a telefonjával.
                        </p>
                    </div>
                )}

                {rental.status === 'accepted' && !qrUrl && (
                    <p className="text-xs text-secondary">
                        Találkozáskor a tulajdonos QR-kódját kell beolvasnod a telefonod kamerájával.
                    </p>
                )}
            </div>
        </div>
    )
}