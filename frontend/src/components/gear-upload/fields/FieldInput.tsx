import FieldLabel from "./FieldLabel";

interface FieldInputProps {
    label: string;
    value: string;
    onChange: (v:string) => void;
    type?:string;
}

export default function FieldInput({label, value, onChange, type='text'}: FieldInputProps){
    return(
        <div>
            <FieldLabel>{label}</FieldLabel>
            <input type={type} value={value} onChange={(e) => onChange(e.target.value)}
            className="w-full px-4 py-3 rounded-xl border border-[#d6d2cb] text-sm text-ink outline-none focus:bprder-ink"/>
        </div>
    )
}