export interface AppEvents {
    'app:ready': void;
    'auth:login': { userId: string };
    'auth:logout': void;
    'subsidebar:open': { id: string };
    'subsidebar:close': { id: string };
    'panel:open': { id: string };
    'panel:close': { id: string };
    'modal:open': { id: string };
    'modal:close': { id: string };
    // 'route:change': { path: string; params: Record<string, string> };
    'notification:show': { message: string; level: 'info' | 'warn' | 'error' };
}