import type { GearFormData, Category } from "../../types/gearFormTypes";
import FieldInput from "./fields/FieldInput";
import FieldTextarea from "./fields/FieldTextArea";
import FieldLabel from "./fields/FieldLabel";

const CATEGORY_LABELS: Record<Category, string> = {
    hand_tool: 'Kézi szerszám',
    cordless: 'Akkus',
    corded: 'Vezetékes',
    machine: 'Munkagép'
};

interface StepBasicInfoProps {
    form: GearFormData;
    updateField: <K extends keyof GearFormData>(key: K, value: GearFormData[K]) => void;
}

export default function StepBasicInfo({ form, updateField }: StepBasicInfoProps) {
    return (
        <div className="flex flex-col gap-3.5">
            <h2 className="text-xl font-extrabold text-ink mb-1">Alapadatok</h2>
            <FieldInput label="Megnevezés" value={form.title} onChange={(v) => updateField('title', v)} />
            <FieldTextarea label="Leírás" value={form.description} onChange={(v) => updateField('description', v)} />

            <div>
                <FieldLabel>Kategória</FieldLabel>
                <div className="grid grid-cols-2 gap-2">
                    {(['hand_tool', 'cordless', 'corded', 'machine'] as const).map((cat) => (
                        <button
                            key={cat}
                            onClick={()=> {updateField('category', cat)}}
                            className={`py-2.5 px-2 rounded-xl text-center text-xs font-semibold border ${form.category === cat
                                ? 'border-2 border-ink bg-white text-ink'
                                : 'border-[#e3e0da] bg-white text-secondary'
                                }`}>{CATEGORY_LABELS[cat]}
                        </button>
                    ))}
                </div>

            </div>
        </div>
    )
}