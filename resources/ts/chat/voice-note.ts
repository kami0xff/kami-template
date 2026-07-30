/**
 * Voice note recording for chat composers.
 * States: idle → recording → preview → (send | cancel → idle)
 *
 * Captures a scrolling amplitude waveform (peaks) during record so
 * preview + chat bubbles can render Messenger-style progress.
 */
export type VoiceNoteState = 'idle' | 'recording' | 'preview';

export interface VoiceNoteResult {
    blob: Blob;
    url: string;
    durationSec: number;
    /** Normalized 0–1 amplitude samples for waveform UI */
    peaks: number[];
}

type VoiceNoteCallbacks = {
    onState?: (state: VoiceNoteState) => void;
    onTick?: (elapsedSec: number, peaks: number[]) => void;
    onError?: (message: string) => void;
};

const PEAK_BARS = 40;

export class VoiceNoteRecorder {
    private media: MediaRecorder | null = null;
    private stream: MediaStream | null = null;
    private chunks: Blob[] = [];
    private startedAt = 0;
    private tickTimer: ReturnType<typeof setInterval> | null = null;
    private audioCtx: AudioContext | null = null;
    private analyser: AnalyserNode | null = null;
    private result: VoiceNoteResult | null = null;
    private state: VoiceNoteState = 'idle';
    private peaks: number[] = [];
    private readonly cb: VoiceNoteCallbacks;

    constructor(callbacks: VoiceNoteCallbacks = {}) {
        this.cb = callbacks;
    }

    getState(): VoiceNoteState {
        return this.state;
    }

    getResult(): VoiceNoteResult | null {
        return this.result;
    }

    async start(): Promise<void> {
        if (this.state !== 'idle') return;
        if (!navigator.mediaDevices?.getUserMedia) {
            this.cb.onError?.('Microphone not available in this browser.');
            return;
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                },
            });
        } catch {
            this.cb.onError?.('Microphone permission denied.');
            return;
        }

        this.chunks = [];
        this.peaks = [];
        const mime = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
            ? 'audio/webm;codecs=opus'
            : MediaRecorder.isTypeSupported('audio/webm')
                ? 'audio/webm'
                : '';
        this.media = mime ? new MediaRecorder(this.stream, { mimeType: mime }) : new MediaRecorder(this.stream);
        this.media.ondataavailable = (e) => {
            if (e.data.size > 0) this.chunks.push(e.data);
        };
        this.media.onstop = () => this.finalize();

        await this.setupAnalyser(this.stream);
        this.startedAt = Date.now();
        this.media.start(100);
        this.setState('recording');
        this.tickTimer = setInterval(() => {
            const elapsed = (Date.now() - this.startedAt) / 1000;
            const peak = this.samplePeak();
            this.peaks.push(peak);
            if (this.peaks.length > PEAK_BARS) this.peaks.shift();
            this.cb.onTick?.(elapsed, [...this.peaks]);
            if (elapsed >= 60) this.stop();
        }, 60);
    }

    stop(): void {
        if (this.state !== 'recording' || !this.media) return;
        if (this.tickTimer) {
            clearInterval(this.tickTimer);
            this.tickTimer = null;
        }
        if (this.media.state !== 'inactive') this.media.stop();
        this.teardownStream();
    }

    cancel(): void {
        if (this.tickTimer) {
            clearInterval(this.tickTimer);
            this.tickTimer = null;
        }
        if (this.media && this.media.state !== 'inactive') {
            this.media.onstop = null;
            this.media.stop();
        }
        this.teardownStream();
        this.revoke();
        this.chunks = [];
        this.peaks = [];
        this.result = null;
        this.setState('idle');
    }

    consume(): VoiceNoteResult | null {
        const out = this.result;
        this.result = null;
        this.peaks = [];
        this.setState('idle');
        return out;
    }

    destroy(): void {
        this.cancel();
        void this.audioCtx?.close();
        this.audioCtx = null;
    }

    private finalize(): void {
        const durationSec = Math.max(0.5, (Date.now() - this.startedAt) / 1000);
        const type = this.media?.mimeType || 'audio/webm';
        const blob = new Blob(this.chunks, { type });
        this.revoke();
        const url = URL.createObjectURL(blob);
        const peaks = normalizePeaks(this.peaks.length > 0 ? this.peaks : seedPeaks());
        this.result = {
            blob,
            url,
            durationSec: Math.max(1, Math.round(durationSec)),
            peaks,
        };
        this.chunks = [];
        this.media = null;
        this.setState('preview');
    }

    private async setupAnalyser(stream: MediaStream): Promise<void> {
        try {
            this.audioCtx = new AudioContext();
            if (this.audioCtx.state === 'suspended') await this.audioCtx.resume();
            const src = this.audioCtx.createMediaStreamSource(stream);
            this.analyser = this.audioCtx.createAnalyser();
            this.analyser.fftSize = 256;
            this.analyser.smoothingTimeConstant = 0.35;
            src.connect(this.analyser);
        } catch {
            this.analyser = null;
        }
    }

    /** Single amplitude sample for the scrolling waveform. */
    private samplePeak(): number {
        if (!this.analyser) return 0.15 + Math.random() * 0.35;
        const data = new Uint8Array(this.analyser.fftSize);
        this.analyser.getByteTimeDomainData(data);
        let peak = 0;
        for (let i = 0; i < data.length; i++) {
            const amp = Math.abs(data[i]! - 128) / 128;
            if (amp > peak) peak = amp;
        }
        // Boost quiet mics so bars are visible
        const boosted = Math.min(1, peak * 3.2);
        return Math.max(0.08, boosted);
    }

    private teardownStream(): void {
        this.stream?.getTracks().forEach((t) => t.stop());
        this.stream = null;
        void this.audioCtx?.close();
        this.audioCtx = null;
        this.analyser = null;
    }

    private revoke(): void {
        if (this.result?.url) URL.revokeObjectURL(this.result.url);
    }

    private setState(state: VoiceNoteState): void {
        this.state = state;
        this.cb.onState?.(state);
    }
}

function seedPeaks(): number[] {
    return Array.from({ length: PEAK_BARS }, (_, i) => 0.2 + ((i * 37) % 10) / 25);
}

function normalizePeaks(peaks: number[]): number[] {
    if (peaks.length >= PEAK_BARS) return peaks.slice(-PEAK_BARS);
    // Stretch short recordings to fill the bar strip
    const out: number[] = [];
    for (let i = 0; i < PEAK_BARS; i++) {
        const t = i / (PEAK_BARS - 1);
        const idx = t * (peaks.length - 1);
        const a = Math.floor(idx);
        const b = Math.min(peaks.length - 1, a + 1);
        const f = idx - a;
        out.push((peaks[a] ?? 0.2) * (1 - f) + (peaks[b] ?? 0.2) * f);
    }
    return out;
}

export function formatVoiceDuration(sec: number): string {
    const s = Math.max(0, Math.floor(sec));
    const m = Math.floor(s / 60);
    const r = s % 60;
    return `${m}:${r.toString().padStart(2, '0')}`;
}

export function waveBarsHtml(peaks: number[], count = PEAK_BARS): string {
    const src = peaks.length ? peaks : seedPeaks();
    const bars: string[] = [];
    for (let i = 0; i < count; i++) {
        const t = count === 1 ? 0 : i / (count - 1);
        const idx = t * (src.length - 1);
        const a = Math.floor(idx);
        const b = Math.min(src.length - 1, a + 1);
        const f = idx - a;
        const level = (src[a] ?? 0.2) * (1 - f) + (src[b] ?? 0.2) * f;
        const h = Math.round(18 + level * 82);
        bars.push(`<i style="--h:${h}%"></i>`);
    }
    return bars.join('');
}
