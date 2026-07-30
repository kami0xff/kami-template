{{--
    AI Model Studio — CivitAI-style browser + stack + settings + generate.
--}}
@props([
    'checkpointName' => 'No checkpoint selected',
    'checkpointMeta' => 'Pick a model from the library',
])

<div {{ $attributes->class(['ai-studio']) }} data-ai-studio data-checkpoint-base="">
    <div class="ai-studio-main">
        <div class="ai-studio-head">
            <div class="ai-studio-tabs" role="tablist">
                <button type="button" class="ai-studio-tab is-active" data-ai-studio-tab="checkpoint" role="tab">Checkpoints</button>
                <button type="button" class="ai-studio-tab" data-ai-studio-tab="lora" role="tab">LoRAs</button>
                <button type="button" class="ai-studio-tab" data-ai-studio-tab="embedding" role="tab">Embeddings</button>
                <button type="button" class="ai-studio-tab" data-ai-studio-tab="vae" role="tab">VAE</button>
            </div>

            <label class="ai-studio-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
                <input type="search" class="form-input" placeholder="Search models, LoRAs, bases…" data-ai-studio-search>
            </label>

            <a href="#ai-create" class="btn btn-muted btn-sm">Create model</a>
        </div>

        <div class="ai-studio-filters" data-ai-base-filters>
            <button type="button" class="ai-aspect is-active" data-ai-base-filter="">All bases</button>
            <button type="button" class="ai-aspect" data-ai-base-filter="SDXL">SDXL</button>
            <button type="button" class="ai-aspect" data-ai-base-filter="Pony">Pony</button>
            <button type="button" class="ai-aspect" data-ai-base-filter="Flux">Flux</button>
            <button type="button" class="ai-aspect" data-ai-base-filter="SD 1.5">SD 1.5</button>
        </div>

        <div class="ai-studio-body">
            <div class="ai-studio-grid" data-ai-studio-grid>
                {{ $resources ?? $slot }}
            </div>
        </div>
    </div>

    <aside class="ai-studio-aside">
        <div class="ai-studio-aside-head">
            <h2 class="ai-studio-aside-title">Configuration</h2>
        </div>
        <div class="ai-studio-aside-body">
            <div>
                <h3 class="ai-studio-section-title">Checkpoint</h3>
                <div class="ai-config-chip" data-ai-active-checkpoint>
                    <img src="" alt="" data-ai-checkpoint-image hidden>
                    <div class="ai-config-chip-meta">
                        <div class="ai-config-chip-title" data-ai-checkpoint-name>{{ $checkpointName }}</div>
                        <div class="ai-config-chip-sub" data-ai-checkpoint-meta>{{ $checkpointMeta }}</div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="ai-studio-section-title">LoRA stack <span class="ai-studio-hint" style="text-transform:none;letter-spacing:0;">(max 5)</span></h3>
                <div class="ai-lora-stack" data-ai-lora-stack>
                    {{ $loras ?? '' }}
                    <p class="ai-studio-empty" data-ai-lora-empty>Add LoRAs from the library. Adjust weights per item.</p>
                </div>
            </div>

            <x-ai.studio.settings />

            <div class="ai-studio-actions">
                <button type="button" class="btn btn-money btn-block" data-ai-studio-generate>Generate</button>
                <button type="button" class="btn btn-primary btn-block" data-ai-studio-use>Use in dock</button>
                <button type="button" class="btn btn-muted btn-block" data-ai-studio-save>Save configuration</button>
                <button type="button" class="btn btn-muted btn-block" data-ai-studio-export>Copy JSON</button>
            </div>

            <x-ai.studio.history />
        </div>
    </aside>
</div>
