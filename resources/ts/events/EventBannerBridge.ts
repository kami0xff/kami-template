import type { BannerManager, BannerOptions } from '../manager/BannerManager';

export interface PlatformEvent {
    type: string;
    payload: Record<string, unknown>;
}

interface BannerEventMapping {
    template: string;
    type: BannerOptions['type'];
    content: (p: Record<string, unknown>) => string;
    timer?: number;
    background?: string;
    href?: (p: Record<string, unknown>) => string;
    actions?: (p: Record<string, unknown>) => BannerOptions['actions'];
}

const EVENT_MAP: Record<string, BannerEventMapping> = {
    'promo.sitewide': {
        template: 'promo',
        type: 'warning',
        content: (p) => `<strong>${p.discount}% off</strong> all token packages — ends in <strong>${p.duration}</strong>`,
        timer: 0,
        background: 'linear-gradient(90deg, #ff6b00, #ff3d00)',
        actions: (p) => [{
            label: 'Buy Now',
            htmlElement: 'a',
            href: (p.url as string) ?? '#',
            type: 'primary',
        }],
    },
    'system.maintenance': {
        template: 'maintenance',
        type: 'warning',
        content: (p) => `Scheduled maintenance at <strong>${p.time}</strong>. You may experience brief downtime.`,
        timer: 0,
    },
    'system.outage': {
        template: 'outage',
        type: 'danger',
        content: (p) => `We're experiencing issues with <strong>${p.service}</strong>. Our team is on it.`,
        timer: 0,
    },
    'system.resolved': {
        template: 'resolved',
        type: 'success',
        content: () => 'All systems are back to normal. Thanks for your patience!',
        timer: 8000,
    },
    'event.global': {
        template: 'event',
        type: 'info',
        content: (p) => `<strong>${p.name}</strong> is happening now — ${p.description}`,
        timer: 15000,
        href: (p) => (p.url as string) ?? '#',
        actions: (p) => [{
            label: 'Join',
            htmlElement: 'a',
            href: (p.url as string) ?? '#',
            type: 'primary',
        }],
    },
    'announcement': {
        template: 'announcement',
        type: 'info',
        content: (p) => String(p.message),
        timer: 10000,
    },
};

export class EventBannerBridge {
    private banners: BannerManager;

    constructor(banners: BannerManager) {
        this.banners = banners;
    }

    handle(event: PlatformEvent): string {
        const mapping = EVENT_MAP[event.type];

        if (!mapping) {
            return this.banners.create({
                type: 'info',
                content: (event.payload.message as string) ?? `Unknown event: ${event.type}`,
            });
        }

        return this.banners.create({
            template: mapping.template,
            type: mapping.type,
            content: mapping.content(event.payload),
            timer: mapping.timer,
            background: mapping.background,
            href: mapping.href?.(event.payload),
            actions: mapping.actions?.(event.payload),
        });
    }
}
