{{--
    Generation settings — form-system fields + CivitAI-style modes.
--}}
@props([
    'steps' => 28,
    'cfg' => 5.5,
    'width' => 832,
    'height' => 1216,
    'seed' => -1,
    'clipSkip' => 2,
    'batchSize' => 1,
    'denoise' => 0.65,
    'sampler' => 'euler_a',
    'scheduler' => 'normal',
    'vae' => 'baked',
])

<div {{ $attributes->class(['ai-studio-settings']) }} data-ai-settings>
    <div>
        <h3 class="ai-studio-section-title">Mode</h3>
        <div class="ai-mode-tabs" data-ai-modes>
            <button type="button" class="ai-mode-tab is-active" data-ai-mode="txt2img">txt2img</button>
            <button type="button" class="ai-mode-tab" data-ai-mode="img2img">img2img</button>
            <button type="button" class="ai-mode-tab" data-ai-mode="inpaint">Inpaint</button>
            <button type="button" class="ai-mode-tab" data-ai-mode="video">Video</button>
        </div>
        <input type="hidden" data-ai-setting="mode" value="txt2img">
    </div>

    <div data-ai-img2img-fields hidden>
        <h3 class="ai-studio-section-title">Source image</h3>
        <label class="ai-dropzone" data-ai-source-drop>
            <input type="file" accept="image/*" data-ai-setting="source_image_file" hidden>
            <span data-ai-source-label>Drop or choose a reference image</span>
            <img data-ai-source-preview hidden alt="">
        </label>
        <div class="ai-setting" style="margin-top:10px;">
            <div class="ai-setting-row">
                <span class="ai-setting-label">Denoise strength</span>
                <span class="ai-setting-value" data-ai-setting-display="denoise">{{ $denoise }}</span>
            </div>
            <input class="ai-range" type="range" min="0" max="1" step="0.01" value="{{ $denoise }}" data-ai-setting="denoise" data-ai-range>
        </div>
    </div>

    <div>
        <h3 class="ai-studio-section-title">Prompts</h3>
        <div class="form-field">
            <label class="form-label" for="ai-pos">Positive</label>
            <x-form.textarea
                name="prompt"
                id="ai-pos"
                :rows="3"
                placeholder="subject, style, lighting…"
                value="cinematic portrait, soft rim light, detailed skin, 85mm"
                data-ai-setting="prompt"
            />
        </div>
        <div class="form-field" style="margin-top:10px;">
            <label class="form-label" for="ai-neg">Negative</label>
            <x-form.textarea
                name="negative_prompt"
                id="ai-neg"
                :rows="2"
                placeholder="blurry, lowres…"
                value="blurry, lowres, watermark, extra fingers, deformed"
                data-ai-setting="negative_prompt"
            />
        </div>
    </div>

    <div>
        <h3 class="ai-studio-section-title">Sampling</h3>
        <div class="ai-setting-grid">
            <div class="form-field">
                <label class="form-label">Sampler</label>
                <x-form.select
                    name="sampler"
                    :selected="$sampler"
                    data-ai-setting="sampler"
                    :options="[
                        'euler_a' => 'Euler a',
                        'euler' => 'Euler',
                        'dpmpp_2m' => 'DPM++ 2M',
                        'dpmpp_2m_sde' => 'DPM++ 2M SDE',
                        'dpmpp_3m_sde' => 'DPM++ 3M SDE',
                        'uni_pc' => 'UniPC',
                        'ddim' => 'DDIM',
                        'lcm' => 'LCM',
                    ]"
                />
            </div>
            <div class="form-field">
                <label class="form-label">Scheduler</label>
                <x-form.select
                    name="scheduler"
                    :selected="$scheduler"
                    data-ai-setting="scheduler"
                    :options="[
                        'normal' => 'Normal',
                        'karras' => 'Karras',
                        'exponential' => 'Exponential',
                        'sgm_uniform' => 'SGM Uniform',
                        'simple' => 'Simple',
                        'ddim_uniform' => 'DDIM Uniform',
                    ]"
                />
            </div>
        </div>

        <div class="ai-setting" style="margin-top:10px;">
            <div class="ai-setting-row">
                <span class="ai-setting-label">Steps</span>
                <span class="ai-setting-value" data-ai-setting-display="steps">{{ $steps }}</span>
            </div>
            <input class="ai-range" type="range" min="1" max="150" step="1" value="{{ $steps }}" data-ai-setting="steps" data-ai-range>
        </div>

        <div class="ai-setting" style="margin-top:10px;">
            <div class="ai-setting-row">
                <span class="ai-setting-label">CFG scale</span>
                <span class="ai-setting-value" data-ai-setting-display="cfg">{{ $cfg }}</span>
            </div>
            <input class="ai-range" type="range" min="1" max="30" step="0.5" value="{{ $cfg }}" data-ai-setting="cfg" data-ai-range>
        </div>
    </div>

    <div>
        <h3 class="ai-studio-section-title">Size</h3>
        <div class="ai-aspects" data-ai-aspects>
            <button type="button" class="ai-aspect" data-ai-aspect="512x768">2:3</button>
            <button type="button" class="ai-aspect is-active" data-ai-aspect="832x1216">2:3 HD</button>
            <button type="button" class="ai-aspect" data-ai-aspect="1024x1024">1:1</button>
            <button type="button" class="ai-aspect" data-ai-aspect="1216x832">3:2</button>
            <button type="button" class="ai-aspect" data-ai-aspect="1344x768">16:9</button>
        </div>
        <div class="ai-setting-grid" style="margin-top:10px;">
            <div class="form-field">
                <label class="form-label">Width</label>
                <x-form.number name="width" :value="$width" :min="256" :max="2048" :step="64" data-ai-setting="width" />
            </div>
            <div class="form-field">
                <label class="form-label">Height</label>
                <x-form.number name="height" :value="$height" :min="256" :max="2048" :step="64" data-ai-setting="height" />
            </div>
        </div>
    </div>

    <div>
        <h3 class="ai-studio-section-title">Seed & batch</h3>
        <div class="ai-setting-grid">
            <div class="form-field">
                <label class="form-label">Seed (−1 = random)</label>
                <x-form.number name="seed" :value="$seed" :step="1" data-ai-setting="seed" />
            </div>
            <div class="form-field">
                <label class="form-label">Batch size</label>
                <x-form.number name="batch_size" :value="$batchSize" :min="1" :max="8" :step="1" data-ai-setting="batch_size" />
            </div>
        </div>
        <div class="ai-setting" style="margin-top:10px;">
            <div class="ai-setting-row">
                <span class="ai-setting-label">Clip skip</span>
                <span class="ai-setting-value" data-ai-setting-display="clip_skip">{{ $clipSkip }}</span>
            </div>
            <input class="ai-range" type="range" min="1" max="12" step="1" value="{{ $clipSkip }}" data-ai-setting="clip_skip" data-ai-range>
        </div>
    </div>

    <div>
        <h3 class="ai-studio-section-title">VAE</h3>
        <x-form.select
            name="vae"
            :selected="$vae"
            data-ai-setting="vae"
            :options="[
                'baked' => 'Baked in checkpoint',
                'sdxl_vae' => 'SDXL VAE',
                'ft_mse' => 'vae-ft-mse-840000',
                'none' => 'None',
            ]"
        />
    </div>

    <div>
        <h3 class="ai-studio-section-title">ControlNet</h3>
        <div class="ai-toggle-row">
            <span class="ai-setting-label">Enable ControlNet</span>
            <x-form.toggle name="controlnet" :checked="false" data-ai-setting="controlnet" />
        </div>
        <div data-ai-controlnet-fields hidden>
            <div class="form-field">
                <label class="form-label">Preprocessor / model</label>
                <x-form.select
                    name="controlnet_model"
                    selected="canny"
                    data-ai-setting="controlnet_model"
                    :options="[
                        'canny' => 'Canny',
                        'depth' => 'Depth',
                        'openpose' => 'OpenPose',
                        'softedge' => 'SoftEdge',
                        'ipadapter' => 'IP-Adapter',
                    ]"
                />
            </div>
            <div class="ai-setting" style="margin-top:10px;">
                <div class="ai-setting-row">
                    <span class="ai-setting-label">Strength</span>
                    <span class="ai-setting-value" data-ai-setting-display="controlnet_strength">0.75</span>
                </div>
                <input class="ai-range" type="range" min="0" max="2" step="0.05" value="0.75" data-ai-setting="controlnet_strength" data-ai-range>
            </div>
        </div>
    </div>

    <div>
        <h3 class="ai-studio-section-title">Advanced</h3>
        <div class="ai-toggle-row">
            <span class="ai-setting-label">Hires. fix</span>
            <x-form.toggle name="hires" :checked="false" data-ai-setting="hires" />
        </div>
        <div class="ai-setting-grid" data-ai-hires-fields hidden>
            <div class="form-field">
                <label class="form-label">Upscaler</label>
                <x-form.select
                    name="hires_upscaler"
                    selected="latent"
                    data-ai-setting="hires_upscaler"
                    :options="[
                        'latent' => 'Latent',
                        '4x_ultrasharp' => '4x-UltraSharp',
                        'esrgan_4x' => 'ESRGAN 4x',
                    ]"
                />
            </div>
            <div class="form-field">
                <label class="form-label">Scale</label>
                <x-form.number name="hires_scale" :value="1.5" :min="1" :max="4" :step="0.05" data-ai-setting="hires_scale" />
            </div>
            <div class="form-field">
                <label class="form-label">Hires steps</label>
                <x-form.number name="hires_steps" :value="12" :min="0" :max="100" :step="1" data-ai-setting="hires_steps" />
            </div>
        </div>

        <div class="ai-toggle-row">
            <span class="ai-setting-label">Refiner</span>
            <x-form.toggle name="refiner" :checked="false" data-ai-setting="refiner" />
        </div>
        <div class="form-field" data-ai-refiner-fields hidden>
            <label class="form-label">Switch at</label>
            <x-form.number name="refiner_switch" :value="0.8" :min="0" :max="1" :step="0.05" data-ai-setting="refiner_switch" />
        </div>

        <div class="ai-toggle-row">
            <span class="ai-setting-label">Advanced Mode</span>
            <x-form.toggle name="advanced_mode" :checked="false" data-ai-setting="advanced_mode" />
        </div>
        <p class="ai-studio-hint">Allows mismatched base models (e.g. Pony LoRA on Illustrious). Results not guaranteed.</p>
    </div>

    <div class="ai-compat" data-ai-compat hidden role="status"></div>

    <div class="ai-cost" data-ai-cost>
        <span>Est. cost</span>
        <strong><span data-ai-cost-value>5</span> tokens</strong>
    </div>
</div>
