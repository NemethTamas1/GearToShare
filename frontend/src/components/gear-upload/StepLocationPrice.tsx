import type { GearFormData } from "../../types/gearFormTypes";
import FieldInput from "./fields/FieldInput";

interface StepLocationPriceProps {
    form: GearFormData;
    updateField: <K extends keyof GearFormData>(key: K, value: GearFormData[K]) => void;
};

export default function StepLocationPrice({ form, updateField }: StepLocationPriceProps) {
    return (
        <div className="flex flex-col gap-3.5">
            <h2 className="text-xl font-extrabold text-ink mb-1">Helyszín és ár</h2>
            <FieldInput label="Város" value={form.city} onChange={(v) => updateField('city', v)} />
            <div>
                <FieldInput label="Cím" value={form.address} onChange={(v) => updateField('address', v)} />
                <p className="text-xs text-[#8b877f] mt-1">A pontos cím csak jóváhagyott bérlőnek jelenik meg.</p>
            </div>
            <FieldInput label="Napi bérleti díj (Ft)" value={form.price_per_day} onChange={(v) => updateField('price_per_day', v)} type='number' />
        </div>
    )
}