export interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    renter_rating: string | null;
    lender_rating: string | null;
    created_at: string;
    updated_at: string;
};

export interface RegisterPayload {
    name: string;
    email: string;
    phone: string;
    password: string;
    password_confirmation: string;
};

export interface LoginPayload {
    email: string;
    password: string;
};

export interface AuthContextType {
    user: User | null;
    loading: boolean;
    register: (data: RegisterPayload) => Promise<void>;
    login: (data: LoginPayload) => Promise<void>;
    logout: () => Promise<void>;
};