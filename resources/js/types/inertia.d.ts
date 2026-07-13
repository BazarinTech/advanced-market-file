import { AuthUser } from './models';

export interface Flash {
    success: string | null;
    error: string | null;
    success_withdrawal: string | null;
    success_banner: string | null;
    success_links: string | null;
    success_claim_image: string | null;
    success_logo: string | null;
    success_crypto: string | null;
}

export interface SharedProps {
    auth: {
        user: AuthUser | null;
    };
    flash: Flash;
    platformLogo: string | null;
    usdRate: number;
}

declare module '@inertiajs/core' {
    interface PageProps extends SharedProps {}
}
