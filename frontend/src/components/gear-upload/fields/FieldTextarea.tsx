import FieldLabel from "./FieldLabel";

interface FieldTextareaProps {
    label: string;
    value: string;
    onChange:(v:string)=>void;
}

export default function FieldTextarea({label, value, onChange}: FieldTextareaProps){
    return(
        <div>
            <FieldLabel>{label}</FieldLabel>
            <textarea value={value} onChange={(e) => onChange(e.target.value)} rows={3}
                className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] text-sm text-ink outline-none focus:border-ink resize-none"/>
        </div>
    )
}