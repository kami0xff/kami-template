import { EventBannerBridge, type PlatformEvent } from './EventBannerBridge';

const MOCK_EVENTS: PlatformEvent[] = [
    { type: 'promo.sitewide', payload: { discount: 40, duration: '3 hours', url: '#' } },
    { type: 'system.maintenance', payload: { time: '03:00 UTC' } },
    { type: 'event.global', payload: { name: 'Friday Night Showdown', description: 'Vote for your favorite performer!', url: '#' } },
    { type: 'announcement', payload: { message: 'New feature: You can now receive Telegram notifications!' } },
    { type: 'system.outage', payload: { service: 'video streaming' } },
    { type: 'system.resolved', payload: {} },
    { type: 'promo.sitewide', payload: { discount: 25, duration: '1 hour', url: '#' } },
    { type: 'event.global', payload: { name: 'Weekend Marathon', description: '48h of non-stop shows', url: '#' } },
];

export class MockBannerEventSource {
    private bridge: EventBannerBridge;
    private timer: ReturnType<typeof setInterval> | null = null;
    private index = 0;

    constructor(bridge: EventBannerBridge) {
        this.bridge = bridge;
    }

    start(intervalMs = 8000): void {
        this.stop();
        this.timer = setInterval(() => {
            const event = MOCK_EVENTS[this.index % MOCK_EVENTS.length];
            this.bridge.handle(event);
            this.index++;
        }, intervalMs);
    }

    stop(): void {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    fire(type: string, payload: Record<string, unknown> = {}): string {
        return this.bridge.handle({ type, payload });
    }
}
