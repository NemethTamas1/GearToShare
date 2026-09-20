export interface Gear {
  id: number;
  title: string;
  description: string;
  category: 'hand_tool' | 'power_tool' | 'machine';
  attributes: Record<string, unknown> | null;
  price_per_day: string;
  city: string;
  status: 'available' | 'draft' | 'unavailable';
}

export interface Category {
  id: string;
  label: string;
  count: number;
}