export interface User {
    ID: number;
    email: string;
    phone: string;
    status: 'Active' | 'Inactive';
    refer: number;
    invite_code: string;
    country: string;
    role: 'user' | 'admin';
    date: string;
}

export interface AuthUser extends User {
    earnings: Earning | null;
}

export interface Earning {
    ID: number;
    email: string;
    balance: string;
    withdraw: string;
    referral: string;
    bonus: string;
    deposit: string;
    totals: string;
    roll: boolean;
}

export interface Order {
    ID: number;
    email: string;
    package: string;
    daily: string;
    totals: string;
    price: string | null;
    status: 'Active' | 'Inactive';
    cycle: number;
    amount: string;
    earnings: string;
    last_claimed_at: string | null;
    tasks_per_day: number;
    task_category: string | null;
    tasks_claimed_today: number;
    fx_rate: string | null;
}

export interface Package {
    id: number;
    name: string;
    amount: string;
    daily: string;
    days: number;
    image: string | null;
    active: boolean;
    tasks_per_day: number;
    task_category: string | null;
}

export interface Transaction {
    ID: number;
    type: 'Deposit' | 'Deposits' | 'Withdraw' | 'Referral';
    method: 'mpesa' | 'crypto';
    email: string;
    amount: string;
    status: 'Pending' | 'Success' | 'Failed' | 'Approved' | 'Rejected';
    phone: string;
    payout_address: string | null;
    details: string;
    RecAmount: string;
    fx_rate: string | null;
    tracking_id: string | null;
    date: string;
}

export interface Coupon {
    id: number;
    code: string;
    amount: string;
    expires_at: string | null;
    max_uses: number;
    used_count: number;
}

export interface CouponUse {
    id: number;
    coupon_id: number;
    user_email: string;
    used_at: string;
}

export interface PasswordRecoveryRequest {
    id: number;
    name: string;
    email: string;
    phone: string;
    network: string;
    status: 'Pending' | 'Resolved';
    resolved_at: string | null;
    created_at: string;
}

export interface WithdrawalAccount {
    id: number;
    email: string;
    method: 'mpesa' | 'crypto';
    phone: string | null;
    name: string | null;
    crypto_address: string | null;
}

export interface Wallet extends Earning {
    user: User;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}
