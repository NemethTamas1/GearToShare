export default function SummaryRow({ label, value }: { label: string, value: string }) {
    return (
        <div className="flex justify-between py-2 border-b border-[#e3e0da] text-sm">
            <span className="text-secondary">{label}</span>
            <span className="text-ink font-medium">{value}</span>
        </div>
    )
}