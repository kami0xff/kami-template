import { AppEvents } from './events';

type EventName = keyof AppEvents;
type Listener<E extends EventName> = (data: AppEvents[E]) => void;

class EventBus {
    private listeners: { [E in EventName]?: Listener<E>[] } = {};

    dispatch<E extends EventName>(event: E, data: AppEvents[E]): void {
        this.listeners[event]?.forEach((callback) => callback(data));
    }

    on<E extends EventName>(event: E, callback: Listener<E>): void {
        const list = (this.listeners[event] ??= []) as Listener<E>[];
        list.push(callback);
    }

    off<E extends EventName>(event: E, callback: Listener<E>): void {
        const list = this.listeners[event];
        if (!list) return;
        const index = list.indexOf(callback);
        if (index !== -1) list.splice(index, 1);
    }
}

export default EventBus;
