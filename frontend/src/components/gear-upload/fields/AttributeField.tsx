import type { CategoryField } from "../../../types/gearFormTypes";
import FieldLabel from "./FieldLabel";
import FieldInput from "./FieldInput";

interface AttributeFieldProps {
    field: CategoryField;
    value: unknown;
    onChange: (v: unknown) => void;
}

export default function AttributeField({ field, value, onChange }: AttributeFieldProps) {
    if (field.type === 'number') return <FieldInput label={field.label} value={String(value ?? '')} onChange={(v) => onChange(Number(v))} type="number" />

    if (field.type === 'select') {
        return (
            <div>
                <FieldLabel>{field.label}</FieldLabel>
                <select value={String(value ?? '')} onChange={(e) => onChange(e.target.value)}
                    className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] text-sm text-ink outline-none">
                    <option value="">Válassz...</option>
                    {field.options?.map((opt) => (
                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                    ))}
                </select>
            </div>
        )
    }

    const selected: string[] = Array.isArray(value) ? value as string[] : [];
    const toggle = (v: string) => {
        onChange(selected.includes(v) ? selected.filter((s) => s !== v) : [...selected, v]);
    };

    return (
        <div>
            <FieldLabel>{field.label}</FieldLabel>
            <div className="flex flex-wrap gap-2">
                {field.options?.map((opt) => (
                    <button key={opt.value} type="button" onClick={() => toggle(opt.value)}
                        className={`px-3 py-1.5 rounded-full text-xs font-medium border ${selected.includes(opt.value) ? 'bg-ink text-white border-ink' : 'bg-white text-secondary border-[#e3e0da]'
                            }`}>{opt.label}</button>
                ))}
            </div>
        </div>
    )
}