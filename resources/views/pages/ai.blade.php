@extends('layouts.app')

@section('title', 'AI Studio')

@section('content')
@php
    $resources = [
        ['id' => 'ckpt-realvis', 'type' => 'checkpoint', 'name' => 'RealVisXL V5.0', 'version' => 'v5.0', 'base' => 'SDXL 1.0', 'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=520&fit=crop', 'selected' => true],
        ['id' => 'ckpt-jugger', 'type' => 'checkpoint', 'name' => 'Juggernaut XL', 'version' => 'XI', 'base' => 'SDXL 1.0', 'image' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=400&h=520&fit=crop'],
        ['id' => 'ckpt-pony', 'type' => 'checkpoint', 'name' => 'Pony Diffusion V6', 'version' => 'XL', 'base' => 'Pony', 'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=520&fit=crop'],
        ['id' => 'ckpt-flux', 'type' => 'checkpoint', 'name' => 'Flux Dev', 'version' => 'dev', 'base' => 'Flux', 'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=520&fit=crop'],
        ['id' => 'lora-detail', 'type' => 'lora', 'name' => 'Add More Details', 'version' => 'v1.5', 'base' => 'SDXL', 'image' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&h=520&fit=crop'],
        ['id' => 'lora-skin', 'type' => 'lora', 'name' => 'Skin Texture XL', 'version' => 'v2', 'base' => 'SDXL', 'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400&h=520&fit=crop'],
        ['id' => 'lora-cine', 'type' => 'lora', 'name' => 'Cinematic Lighting', 'version' => 'v3', 'base' => 'SDXL', 'image' => 'https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?w=400&h=520&fit=crop'],
        ['id' => 'lora-film', 'type' => 'lora', 'name' => 'Analog Film Grain', 'version' => 'v1', 'base' => 'SDXL', 'image' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=400&h=520&fit=crop&sat=-40'],
        ['id' => 'emb-badhands', 'type' => 'embedding', 'name' => 'bad-hands-5', 'version' => 'neg', 'base' => 'SD 1.5', 'image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400&h=520&fit=crop'],
        ['id' => 'emb-easyneg', 'type' => 'embedding', 'name' => 'EasyNegative', 'version' => 'v2', 'base' => 'SD 1.5', 'image' => 'https://images.unsplash.com/photo-1550684848-fac1c5b4b392?w=400&h=520&fit=crop'],
        ['id' => 'vae-sdxl', 'type' => 'vae', 'name' => 'SDXL VAE', 'version' => 'fp16', 'base' => 'SDXL', 'image' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=400&h=520&fit=crop'],
        ['id' => 'vae-mse', 'type' => 'vae', 'name' => 'vae-ft-mse-840000', 'version' => 'ema', 'base' => 'SD 1.5', 'image' => 'https://images.unsplash.com/photo-1618005198919-d3d4b5a92ead?w=400&h=520&fit=crop'],
    ];
@endphp

<x-app-shell>
    <x-slot:subnav>
        <a href="#ai-configs" class="subnavbar-link">My models</a>
        <a href="#ai-create" class="subnavbar-link">Create</a>
        <a href="#ai-chat" class="subnavbar-link">Chat</a>
        <a href="#ai-studio" class="subnavbar-link">Model studio</a>
        <a href="{{ route('design') }}#ai-chat" class="subnavbar-link">Design system</a>
    </x-slot:subnav>

    <div style="padding: var(--space5) var(--space4) var(--space10); display:grid; gap: var(--space8); max-width: 1280px; margin: 0 auto;">
        <header style="display:grid; gap: var(--space2);">
            <h1 style="margin:0; font-size: clamp(1.5rem, 3vw, 2rem); letter-spacing:-0.02em;">AI workspace</h1>
            <p style="margin:0; color:var(--text-muted); max-width:42rem; line-height:1.5;">
                Create and save model configs, then talk to them in the same bottom chat dock
                as people chats — or deep-configure stacks in the studio.
            </p>
        </header>

        <section id="ai-configs" style="padding-bottom: var(--space4);">
            <x-ai.configs />
        </section>

        <section id="ai-create">
            <x-ai.studio.create-wizard />
        </section>

        <section id="ai-chat">
            <h2 style="margin:0 0 var(--space3); font-size:var(--text-subheading);">Chat</h2>
            <x-ai.chat.layout title="Generate" model-label="RealVisXL V5.0">
                <x-slot:sidebar>
                    <ul class="ai-chat-conv-list">
                        <x-ai.chat.conversation id="c1" title="Cinematic portrait" meta="RealVisXL · 2h ago" :active="true" />
                        <x-ai.chat.conversation id="c2" title="Neon street look" meta="Juggernaut · Yesterday" />
                        <x-ai.chat.conversation id="c3" title="Product packshot" meta="Flux · Mon" />
                    </ul>
                </x-slot:sidebar>

                <x-ai.chat.message role="system" content="Config loaded: RealVisXL V5.0 · 2 LoRAs · Euler a · 28 steps · CFG 5.5" />
                <x-ai.chat.message role="user" content="Soft rim-lit portrait, 85mm, shallow depth of field, warm skin tones" />
                <x-ai.chat.message
                    role="assistant"
                    content="Here’s a pass with your active stack."
                    :images="['https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=512&h=512&fit=crop']"
                />

                <x-slot:composer>
                    <x-ai.chat.composer
                        :models="[
                            'realvis' => 'RealVisXL V5.0',
                            'jugger' => 'Juggernaut XL',
                            'pony' => 'Pony Diffusion V6',
                            'flux' => 'Flux Dev',
                        ]"
                        model="realvis"
                    />
                </x-slot:composer>

                <x-slot:aside>
                    <div class="ai-config-chip">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&h=100&fit=crop" alt="">
                        <div class="ai-config-chip-meta">
                            <div class="ai-config-chip-title">RealVisXL V5.0</div>
                            <div class="ai-config-chip-sub">Checkpoint · SDXL</div>
                        </div>
                    </div>
                    <div class="ai-config-chip">
                        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=80&h=100&fit=crop" alt="">
                        <div class="ai-config-chip-meta">
                            <div class="ai-config-chip-title">Add More Details</div>
                            <div class="ai-config-chip-sub">LoRA · weight 0.8</div>
                        </div>
                    </div>
                    <div class="ai-kv">
                        <div class="ai-kv-row"><span>Sampler</span><strong>Euler a</strong></div>
                        <div class="ai-kv-row"><span>Steps</span><strong>28</strong></div>
                        <div class="ai-kv-row"><span>CFG</span><strong>5.5</strong></div>
                        <div class="ai-kv-row"><span>Size</span><strong>832×1216</strong></div>
                    </div>
                    <a href="#ai-studio" class="btn btn-muted btn-sm btn-block">Edit in studio</a>
                </x-slot:aside>
            </x-ai.chat.layout>
        </section>

        <section id="ai-studio">
            <h2 style="margin:0 0 var(--space3); font-size:var(--text-subheading);">Model studio</h2>
            <x-ai.studio.studio
                checkpoint-name="RealVisXL V5.0"
                checkpoint-meta="v5.0 · SDXL 1.0"
            >
                <x-slot:resources>
                    @foreach($resources as $r)
                        <x-ai.studio.resource
                            :id="$r['id']"
                            :type="$r['type']"
                            :name="$r['name']"
                            :version="$r['version'] ?? null"
                            :base="$r['base'] ?? null"
                            :image="$r['image'] ?? null"
                            :selected="$r['selected'] ?? false"
                        />
                    @endforeach
                </x-slot:resources>

                <x-slot:loras>
                    <x-ai.studio.lora-item
                        id="lora-detail"
                        name="Add More Details"
                        family="SDXL"
                        image="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=80&h=100&fit=crop"
                        :weight="0.8"
                    />
                    <x-ai.studio.lora-item
                        id="lora-cine"
                        name="Cinematic Lighting"
                        family="SDXL"
                        image="https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?w=80&h=100&fit=crop"
                        :weight="0.55"
                    />
                </x-slot:loras>
            </x-ai.studio.studio>
        </section>
    </div>

    <script>
        document.addEventListener('ai-studio:export', () => {
            window.toasts?.success('Config JSON copied');
        });
    </script>
</x-app-shell>
@endsection
