{{--
    Create model wizard — register a checkpoint / LoRA / embedding (upload stubbed).
--}}
@props([])

<div {{ $attributes->class(['ai-wizard']) }} data-ai-create-wizard>
    <div class="ai-wizard-head">
        <h2 class="ai-wizard-title">Create model</h2>
        <p class="ai-wizard-lead">Publish a checkpoint, LoRA, or embedding to your library. Files stay local in this demo.</p>
    </div>

    <div class="ai-wizard-steps" data-ai-wizard-steps>
        <button type="button" class="ai-wizard-step is-active" data-ai-wizard-goto="1">1 · Type</button>
        <button type="button" class="ai-wizard-step" data-ai-wizard-goto="2">2 · Files</button>
        <button type="button" class="ai-wizard-step" data-ai-wizard-goto="3">3 · Details</button>
        <button type="button" class="ai-wizard-step" data-ai-wizard-goto="4">4 · Review</button>
    </div>

    <form class="ai-wizard-form" data-ai-wizard-form>
        <section class="ai-wizard-panel" data-ai-wizard-panel="1">
            <h3 class="ai-studio-section-title">Resource type</h3>
            <div class="ai-mode-tabs">
                <label class="ai-mode-tab is-active"><input type="radio" name="type" value="checkpoint" checked class="sr-only"> Checkpoint</label>
                <label class="ai-mode-tab"><input type="radio" name="type" value="lora" class="sr-only"> LoRA</label>
                <label class="ai-mode-tab"><input type="radio" name="type" value="embedding" class="sr-only"> Embedding</label>
                <label class="ai-mode-tab"><input type="radio" name="type" value="vae" class="sr-only"> VAE</label>
            </div>
        </section>

        <section class="ai-wizard-panel" data-ai-wizard-panel="2" hidden>
            <h3 class="ai-studio-section-title">Files</h3>
            <label class="ai-dropzone">
                <input type="file" name="weights" accept=".safetensors,.ckpt,.pt,.bin" data-ai-wizard-file>
                <span data-ai-wizard-file-label>Drop weights (.safetensors) — not uploaded in demo</span>
            </label>
            <label class="ai-dropzone" style="margin-top:10px;">
                <input type="file" name="cover" accept="image/*" data-ai-wizard-cover>
                <span data-ai-wizard-cover-label>Cover image (optional)</span>
            </label>
        </section>

        <section class="ai-wizard-panel" data-ai-wizard-panel="3" hidden>
            <h3 class="ai-studio-section-title">Details</h3>
            <div class="form-field">
                <label class="form-label">Name</label>
                <input class="form-input" type="text" name="name" required placeholder="My custom model">
            </div>
            <div class="form-field" style="margin-top:10px;">
                <label class="form-label">Version</label>
                <input class="form-input" type="text" name="version" placeholder="v1.0" value="v1.0">
            </div>
            <div class="form-field" style="margin-top:10px;">
                <label class="form-label">Base model</label>
                <x-form.select
                    name="base"
                    selected="SDXL"
                    :options="[
                        'SD 1.5' => 'SD 1.5',
                        'SDXL' => 'SDXL',
                        'Pony' => 'Pony',
                        'Illustrious' => 'Illustrious',
                        'Flux' => 'Flux',
                    ]"
                />
            </div>
            <div class="form-field" style="margin-top:10px;">
                <label class="form-label">Description</label>
                <x-form.textarea name="description" :rows="3" placeholder="What is this trained for?" />
            </div>
            <div class="form-field" style="margin-top:10px;">
                <label class="form-label">Tags (comma separated)</label>
                <input class="form-input" type="text" name="tags" placeholder="portrait, realistic, photoreal">
            </div>
            <div class="ai-toggle-row" style="margin-top:10px;">
                <span class="ai-setting-label">Public in library</span>
                <x-form.toggle name="public" :checked="true" />
            </div>
        </section>

        <section class="ai-wizard-panel" data-ai-wizard-panel="4" hidden>
            <h3 class="ai-studio-section-title">Review</h3>
            <dl class="ai-wizard-review" data-ai-wizard-review></dl>
            <p class="ai-studio-hint">Demo only — we’ll add the card to your local library. Wire this form to your upload API later.</p>
        </section>

        <div class="ai-wizard-actions">
            <button type="button" class="btn btn-muted" data-ai-wizard-back hidden>Back</button>
            <button type="button" class="btn btn-primary" data-ai-wizard-next>Continue</button>
            <button type="submit" class="btn btn-money" data-ai-wizard-submit hidden>Create model</button>
        </div>
    </form>
</div>
