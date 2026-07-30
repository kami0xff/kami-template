import { EventToastBridge, type PlatformEvent } from './EventToastBridge';

const MOCK_EVENTS: PlatformEvent[] = [
    { type: 'performer.online', payload: { name: 'MarcusLive', url: '#' } },
    { type: 'promo.discount', payload: { discount: 30, duration: '2 hours', url: '#' } },
    { type: 'event.started', payload: { name: 'Friday Night Showdown', url: '#' } },
    { type: 'tokens.received', payload: { amount: 50, from: 'AnonymousUser' } },
    { type: 'performer.new', payload: { name: 'JakeStream', url: '#' } },
    { type: 'tokens.low', payload: { remaining: 12 } },
    { type: 'event.winner', payload: { prize: '500 tokens', event: 'Spin the Wheel' } },
    { type: 'event.ended', payload: { name: 'Friday Night Showdown' } },
    { type: 'system.maintenance', payload: { time: '03:00 UTC' } },
    { type: 'performer.online', payload: { name: 'AlexFitness', url: '#' } },
];

export class MockEventSource {
    private bridge: EventToastBridge;
    private timer: ReturnType<typeof setInterval> | null = null;
    private index = 0;

    constructor(bridge: EventToastBridge) {
        this.bridge = bridge;
    }

    start(intervalMs = 4000): void {
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
