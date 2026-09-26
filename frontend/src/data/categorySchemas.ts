import type { CategoryField, Category } from '../types/gearFormTypes';

export const CATEGORY_LABELS: Record<Category, string> = {
  hand_tool: 'Kézi szerszám',
  cordless: 'Akkus szerszám',
  corded: 'Vezetékes szerszám',
  machine: 'Munkagép',
};

const MODES_FIELD: CategoryField = {
  key: 'modes',
  label: 'Üzemmódok',
  type: 'multiselect',
  options: [
    { value: 'drill', label: 'Fúrás' },
    { value: 'hammer_drill', label: 'Ütvefúrás' },
    { value: 'chiseling', label: 'Vésés' },
    { value: 'screwdriving', label: 'Csavarozás' },
    { value: 'cutting', label: 'Vágás' },
    { value: 'grinding', label: 'Csiszolás' },
    { value: 'sanding', label: 'Finomcsiszolás' },
  ],
};

export const CATEGORY_SCHEMAS: Record<Category, CategoryField[]> = {
  hand_tool: [],
  cordless: [
    { key: 'battery_capacity_mah', label: 'Akkumulátor kapacitás (mAh)', type: 'number' },
    MODES_FIELD,
  ],
  corded: [
    { key: 'power_w', label: 'Teljesítmény (W)', type: 'number' },
    MODES_FIELD,
  ],
  machine: [
    { key: 'load_capacity_kg', label: 'Terhelhetőség (kg)', type: 'number' },
    {
      key: 'fuel_type',
      label: 'Üzemanyag típusa',
      type: 'select',
      options: [
        { value: 'benzin', label: 'Benzin' },
        { value: 'dizel', label: 'Dízel' },
        { value: 'elektromos', label: 'Elektromos' },
      ],
    },
    { key: 'fuel_tank_l', label: 'Tank térfogat (l)', type: 'number' },
    { key: 'horsepower', label: 'Lóerő', type: 'number' },
  ],
};