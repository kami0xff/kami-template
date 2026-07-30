import type { ToastNotificationManager } from '../manager/ToastNotificationManager';

export interface PlatformEvent {
    type: string;
    payload: Record<string, unknown>;
}

interface ToastMapping {
    toastType: 'success' | 'error' | 'warning' | 'info';
    title: (p: Record<string, unknown>) => string;
    message: (p: Record<string, unknown>) => string;
    duration?: number;
    actions?: (p: Record<string, unknown>) => Array<{ label: string; onClick: () => void; primary?: boolean }>;
}

const EVENT_MAP: Record<string, ToastMapping> = {
    'event.started': {
        toastType: 'info',
        title: () => 'Event Started',
        message: (p) => `<strong>${p.name}</strong> is now live! Join before it ends.`,
        actions: (p) => [{
            label: 'Join Now',
            onClick: () => { if (p.url) window.location.href = p.url as string; },
            primary: true,
        }],
    },
    'event.ended': {
        toastType: 'info',
        title: () => 'Event Ended',
        message: (p) => `<strong>${p.name}</strong> has ended. Thanks for participating!`,
    },
    'event.winner': {
        toastType: 'success',
        title: () => 'You Won!',
        message: (p) => `Congratulations! You won <strong>${p.prize}</strong> in <strong>${p.event}</strong>.`,
        duration: 10000,
    },
    'promo.discount': {
        toastType: 'warning',
        title: () => 'Special Offer',
        message: (p) => `<strong>${p.discount}% off</strong> tokens for the next <strong>${p.duration}</strong>. Don't miss out!`,
        actions: (p) => [{
            label: 'Buy Now',
            onClick: () => { if (p.url) window.location.href = p.url as string; },
            primary: true,
        }],
        duration: 8000,
    },
    'performer.online': {
        toastType: 'success',
        title: (p) => `${p.name} is Live`,
        message: (p) => `<strong>${p.name}</strong> just started streaming.`,
        actions: (p) => [{
            label: 'Watch',
            onClick: () => { if (p.url) window.location.href = p.url as string; },
            primary: true,
        }],
    },
    'performer.new': {
        toastType: 'info',
        title: () => 'New Performer',
        message: (p) => `<strong>${p.name}</strong> just joined the platform. Be the first to say hi!`,
        actions: (p) => [{
            label: 'View Profile',
            onClick: () => { if (p.url) window.location.href = p.url as string; },
        }],
    },
    'tokens.received': {
        toastType: 'success',
        title: () => 'Tokens Received',
        message: (p) => `You received <strong>${p.amount} tokens</strong> from <strong>${p.from}</strong>.`,
    },
    'tokens.low': {
        toastType: 'warning',
        title: () => 'Low Balance',
        message: (p) => `You have <strong>${p.remaining} tokens</strong> left. Top up to stay in the show.`,
        actions: () => [{
            label: 'Buy Tokens',
            onClick: () => window.modals?.toggle('buy-tokens'),
            primary: true,
        }],
    },
    'system.maintenance': {
        toastType: 'warning',
        title: () => 'Scheduled Maintenance',
        message: (p) => `The platform will be briefly offline at <strong>${p.time}</strong>.`,
        duration: 12000,
    },
};

export class EventToastBridge {
    private toasts: ToastNotificationManager;

    constructor(toasts: ToastNotificationManager) {
        this.toasts = toasts;
    }

    handle(event: PlatformEvent): string {
        const mapping = EVENT_MAP[event.type];

        if (!mapping) {
            return this.toasts.info(event.payload.message as string ?? `Unknown event: ${event.type}`);
        }

        const { toastType, title, message, duration, actions } = mapping;

        return this.toasts.create(message(event.payload), toastType, {
            title: title(event.payload),
            duration,
            actions: actions?.(event.payload),
        });
    }
}
