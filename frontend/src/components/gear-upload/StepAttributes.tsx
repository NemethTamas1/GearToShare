import type { CategoryField } from "../../types/gearFormTypes";
import AttributeField from "./fields/AttributeField";

interface StepAttributeProps {
    schema: CategoryField[];
    values: Record<string, unknown>
    updateAttribute: (key: string, value: unknown) => void;
};

export default function StepAttributes({ schema, values, updateAttribute }: StepAttributeProps) {
    return (
        <div className="flex flex-col gap-3.5">
            <h2 className="text-xl font-extrabold text-ink mb-1">Műszaki adatok</h2>
            {schema.length === 0 && (
                <p className="text-sm text-secondary">Ehhez a kategóriához nincs kiegészítő</p>
            )}
            {schema.map((field) => (
                <AttributeField
                    key={field.key}
                    field={field}
                    value={values[field.key]}
                    onChange={(v) => updateAttribute(field.key, v)} />
            ))}

        </div>
    )
}
