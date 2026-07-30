export interface NotificationSettings {
    isEmail: boolean;
    isPush: boolean;
    isTelegram: boolean;
}

export class NotificationApi {
    private static getMeta(name: string): string {
        return document.querySelector<HTMLMetaElement>(`meta[name="${name}"]`)?.content ?? '';
    }

    static async updateSettings(
        modelUsername: string,
        settings: NotificationSettings,
    ): Promise<Response> {
        const response = await fetch(`/api/notification-settings/${encodeURIComponent(modelUsername)}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getMeta('csrf-token'),
            },
            body: JSON.stringify(settings),
        });

        if (!response.ok) {
            throw new Error(`Failed to update notification settings: ${response.status}`);
        }

        return response;
    }
}