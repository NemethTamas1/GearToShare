import type { GearFormData, Category } from "../../types/gearFormTypes";
import SummaryRow from "./fields/SummaryRow";

const CATEGORY_LABELS: Record<Category, string> = {
    hand_tool: 'Kézi szerszám',
    cordless: 'Akkus',
    corded: 'Vezetékes',
    machine: 'Munkagép'
};

interface StepSummaryProps {
    form: GearFormData;
    error: string | null;
};

export default function StepSummary({ form, error }: StepSummaryProps) {
    return (
        <div className="flex flex-col gap-3">
            <h2 className="text-xl font-extrabold text-ink mb-1">Összegzés</h2>
            <SummaryRow label="Megnevezés" value={form.title} />
            <SummaryRow label="Kategória" value={form.category ? CATEGORY_LABELS[form.category] : '-'} />
            <SummaryRow label="Város" value={form.city} />
            <SummaryRow label="Ár" value={`${form.price_per_day} Ft/nap`} />
            {error && <p className="text-danger text-sm">{error}</p>}
        </div>
    )
}