export type FieldType = 'number' | 'select' | 'multiselect';

export interface FieldOption {
  value: string;
  label: string;
}

export interface CategoryField {
  key: string;
  label: string;
  type: FieldType;
  options?: FieldOption[];
}

export type Category = 'hand_tool' | 'cordless' | 'corded' | 'machine';

export interface GearFormData {
  title: string;
  description: string;
  city: string;
  address: string;
  price_per_day: string;
  category: Category | null;
  attributes: Record<string, unknown>;
}