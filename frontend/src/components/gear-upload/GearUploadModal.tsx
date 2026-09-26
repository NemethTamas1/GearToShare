// src/components/GearUploadModal.tsx
import { useState } from 'react';
import type { GearFormData, CategoryField } from '../../types/gearFormTypes';
import { CATEGORY_SCHEMAS } from '../../data/categorySchemas';
import api from '../../lib/axios';
import StepPhotos from './StepPhotos';
import StepBasicInfo from './StepBasicInfo';
import StepAttributes from './StepAttributes';
import StepLocationPrice from './StepLocationPrice';
import StepSummary from './StepSummary';  

const INITIAL_STATE: GearFormData = {
  title: '',
  description: '',
  city: '',
  address: '',
  price_per_day: '',
  category: null,
  attributes: {},
};

const TOTAL_STEPS = 5;

export default function GearUploadModal({ onClose }: { onClose: () => void }) {
  const [step, setStep] = useState(1);
  const [form, setForm] = useState<GearFormData>(INITIAL_STATE);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const currentSchema: CategoryField[] = form.category ? CATEGORY_SCHEMAS[form.category] : [];

  const updateField = <K extends keyof GearFormData>(key: K, value: GearFormData[K]) => {
    setForm((prev) => ({ ...prev, [key]: value }));
  };

  const updateAttribute = (key: string, value: unknown) => {
    setForm((prev) => ({ ...prev, attributes: { ...prev.attributes, [key]: value } }));
  };

  const goNext = () => setStep((s) => Math.min(s + 1, TOTAL_STEPS));
  const goBack = () => setStep((s) => Math.max(s - 1, 1));

  const handleSubmit = async () => {
    setSubmitting(true);
    setError(null);
    try {
      await api.post('/api/gears', {
        title: form.title,
        description: form.description,
        city: form.city,
        address: form.address,
        price_per_day: form.price_per_day,
        category: form.category,
        attributes: currentSchema.length > 0 ? form.attributes : null,
      });
      onClose();
    } catch {
      setError('Nem sikerült létrehozni a hirdetést.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-xl w-full max-w-md flex flex-col max-h-[90vh]">
        <header className="flex-none px-5 pt-5 pb-3 border-b border-[#e3e0da]">
          <div className="flex items-center justify-between mb-3">
            <button onClick={step === 1 ? onClose : goBack} className="text-secondary text-sm">
              {step === 1 ? '✕' : '‹'}
            </button>
            <span className="font-semibold text-sm text-ink">Új eszköz</span>
            <span className="font-mono text-xs text-[#8b877f]">{step}/{TOTAL_STEPS}</span>
          </div>
          <div className="flex gap-1">
            {Array.from({ length: TOTAL_STEPS }).map((_, i) => (
              <div key={i} className={`h-1 flex-1 rounded-full ${i < step ? 'bg-ink' : 'bg-[#e3e0da]'}`} />
            ))}
          </div>
        </header>

        <div className="flex-1 overflow-y-auto px-5 py-5">
          {step === 1 && <StepPhotos />}
          {step === 2 && <StepBasicInfo form={form} updateField={updateField}/>}
          {step === 3 && <StepAttributes schema={currentSchema} values={form.attributes} updateAttribute={updateAttribute} />}
          {step === 4 && <StepLocationPrice form={form} updateField={updateField}/>}

          {step === 5 && <StepSummary form={form} error={error} />}
        </div>

        <footer className="flex-none px-5 py-4 border-t border-[#e3e0da]">
          <button
            onClick={step === TOTAL_STEPS ? handleSubmit : goNext}
            disabled={submitting}
            className="w-full py-4 rounded-xl bg-accent text-ink font-bold text-[15.5px] disabled:opacity-60"
          >
            {step === TOTAL_STEPS ? (submitting ? 'Mentés...' : 'Hirdetés közzététele') : 'Tovább'}
          </button>
        </footer>
      </div>
    </div>
  );
}