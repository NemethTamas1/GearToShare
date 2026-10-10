export interface Rental {
    id: number;
    gear_id: number;
    renter_id: number;
    start_date: string;
    end_date: string;
    total_price: string;
    status: string;
    message: string | null;
    gear?: { id: number; title: string };
    renter?: { id: number; name: string };
}

export interface RentalDetail extends Rental {
  contact?: { address?: string; phone: string | null };
  handover_token?: string;
}