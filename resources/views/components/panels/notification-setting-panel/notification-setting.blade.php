@props([
    'modelUsername' => '',
    'modelName' => 'this model',
    'notifyAll' => true,
    'notifyEmail' => true,
    'notifyWeb' => true,
    'notifyTelegram' => false,
])

<div class="notification-setting-panel" data-model="{{ $modelUsername }}">
    {{-- Hero: bell icon + description + master toggle --}}
    <div class="notif-setting-hero">
        <div class="notif-setting-bell">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
        </div>
        <p class="notif-setting-desc">
            Do you want to be notified when {{ $modelName }} is live or has sent you a message?
        </p>
        <x-form.toggle name="notify_all" :checked="$notifyAll" data-switch-id="main" />
    </div>

    <x-panels.panel-divider />

    {{-- Notify me by --}}
    <div class="notif-setting-section">
        <h4 class="notif-setting-title">Notify me by:</h4>

        <div class="notif-setting-options">
            <x-form.toggle-row name="notify_email" :checked="$notifyEmail" label="E-mail" data-switch-id="email" />
            <x-form.toggle-row name="notify_web" :checked="$notifyWeb" label="Web Notifications" data-switch-id="web" />
        </div>

        <div class="notif-setting-link-row">
            <a href="#" class="notif-setting-link">Connect Telegram</a>
            <x-form.toggle name="notify_telegram" :checked="$notifyTelegram" :disabled="! $notifyTelegram" data-switch-id="telegram" />
        </div>
    </div>

    {{-- Install app promo --}}
    <div class="notif-setting-app">
        <div class="notif-setting-app-icon">
            {{-- Placeholder for your app icon --}}
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="5" y="2" width="14" height="20" rx="2"/>
                <line x1="12" y1="18" x2="12.01" y2="18"/>
            </svg>
        </div>
        <div class="notif-setting-app-meta">
            <strong>Get the app</strong>
            <span>Fast mobile access & push notifications</span>
        </div>
        <svg class="notif-setting-app-caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>
</div>
