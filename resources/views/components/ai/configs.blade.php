{{--
    Saved AI configs library + create form.
    Managed by AiConfigManager ([data-ai-configs]).
--}}
@props([])

<section {{ $attributes->class(['ai-configs']) }} data-ai-configs>
    <div class="ai-configs-toolbar">
        <div>
            <h2 style="margin:0; font-size:var(--text-subheading);">My AI models</h2>
            <p style="margin:4px 0 0; color:var(--text-muted); font-size:var(--text-small);">
                Saved presets &amp; girlfriends — open them in the shared chat dock anytime.
            </p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-ai-config-new-toggle>Create config</button>
    </div>

    <form class="ai-config-form" data-ai-config-form hidden>
        <label>
            Name
            <input type="text" name="name" required placeholder="Luna, Studio pack…">
        </label>
        <label>
            Subtitle
            <input type="text" name="subtitle" placeholder="AI girlfriend · RealVisXL">
        </label>
        <label>
            Avatar URL
            <input type="url" name="avatar" placeholder="https://…">
        </label>
        <label>
            Checkpoint name
            <input type="text" name="checkpoint_name" required placeholder="RealVisXL V5.0">
        </label>
        <label>
            Checkpoint id
            <input type="text" name="checkpoint_id" placeholder="ckpt-realvis">
        </label>
        <label>
            Checkpoint meta
            <input type="text" name="checkpoint_meta" placeholder="v5.0 · SDXL">
        </label>
        <label>
            Default prompt
            <textarea name="prompt" placeholder="soft rim light, intimate portrait…"></textarea>
        </label>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-money btn-sm">Save &amp; open dock</button>
            <button type="button" class="btn btn-muted btn-sm" data-ai-config-new-toggle>Cancel</button>
        </div>
    </form>

    <div class="ai-configs-grid" data-ai-configs-grid></div>
</section>
