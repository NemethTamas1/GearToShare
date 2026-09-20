export interface Gear {
  id: string;
  name: string;
  location: string;
  pricePerDay: number;
  rating: number;
  reviewCount: number;
  available: boolean;
  photoUrl?: string;
}

export interface Category {
  id: string;
  label: string;
  count: number;
}
