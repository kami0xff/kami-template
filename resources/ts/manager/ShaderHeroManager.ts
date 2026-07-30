/**
 * ShaderHeroManager — lightweight WebGL2 marketing backdrops.
 *
 * SEO contract:
 *   - Never paints copy onto the canvas
 *   - Respects prefers-reduced-motion
 *   - Pauses when off-screen
 *   - Falls back to CSS gradient on WebGL failure
 *
 * Variants (data-variant): chrome | glow | studio | aurora | plasma | mesh | silk | ember
 */

type HeroRoot = HTMLElement & {
    __shaderHeroBound?: boolean;
    __shaderHeroDispose?: () => void;
};

type Variant = 'chrome' | 'glow' | 'studio' | 'aurora' | 'plasma' | 'mesh' | 'silk' | 'ember';

const VERT = `#version 300 es
in vec2 a_pos;
void main(){ gl_Position = vec4(a_pos, 0.0, 1.0); }
`;

const COMMON = `
precision highp float;
out vec4 fragColor;
uniform vec2 u_res;
uniform float u_time;
uniform vec2 u_pointer;
uniform float u_pointer_strength;

float hash(vec2 p){
  p = fract(p * vec2(123.34, 456.21));
  p += dot(p, p + 45.32);
  return fract(p.x * p.y);
}

float noise(vec2 p){
  vec2 i = floor(p);
  vec2 f = fract(p);
  float a = hash(i);
  float b = hash(i + vec2(1.0, 0.0));
  float c = hash(i + vec2(0.0, 1.0));
  float d = hash(i + vec2(1.0, 1.0));
  vec2 u = f * f * (3.0 - 2.0 * f);
  return mix(a, b, u.x) + (c - a) * u.y * (1.0 - u.x) + (d - b) * u.x * u.y;
}

float fbm(vec2 p){
  float v = 0.0;
  float a = 0.5;
  mat2 m = mat2(1.6, 1.2, -1.2, 1.6);
  for (int i = 0; i < 5; i++) {
    v += a * noise(p);
    p = m * p;
    a *= 0.5;
  }
  return v;
}

vec2 domain(vec2 uv){
  float aspect = u_res.x / max(u_res.y, 1.0);
  vec2 p = (uv - 0.5) * vec2(aspect, 1.0);
  vec2 pointer = (u_pointer - 0.5) * vec2(aspect, 1.0);
  p += (pointer - p) * 0.05 * u_pointer_strength;
  return p;
}
`;

const FRAG: Record<Variant, string> = {
    // Flat silver chrome tiles — SDF illusion of thick liquid metal (no 3D mesh)
    chrome: `#version 300 es
${COMMON}
float sdRoundBox(vec2 p, vec2 b, float r){
  vec2 q = abs(p) - b + r;
  return length(max(q, 0.0)) + min(max(q.x, q.y), 0.0) - r;
}

float sdTile(vec2 p, vec2 c, float halfSize, float rot, float corner){
  float s = sin(rot);
  float co = cos(rot);
  vec2 q = p - c;
  q = mat2(co, -s, s, co) * q;
  return sdRoundBox(q, vec2(halfSize), corner);
}

float chromeField(vec2 lp, float t){
  float d0 = sdTile(lp, vec2( 0.00,  0.42) + 0.008 * vec2(sin(t*0.7), cos(t*0.5)), 0.175,  0.08, 0.085);
  float d1 = sdTile(lp, vec2(-0.34,  0.02) + 0.01  * vec2(cos(t*0.6), sin(t*0.8)), 0.22,  -0.12, 0.095);
  float d2 = sdTile(lp, vec2( 0.32, -0.02) + 0.008 * vec2(sin(t*0.5), cos(t*0.65)), 0.235,  0.16, 0.1);
  float d3 = sdTile(lp, vec2( 0.02, -0.42) + 0.009 * vec2(cos(t*0.55), sin(t*0.45)), 0.2,   -0.05, 0.09);
  return min(min(d0, d1), min(d2, d3));
}

void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  float aspect = u_res.x / max(u_res.y, 1.0);
  vec2 p = (uv - 0.5) * vec2(aspect, 1.0);
  vec2 pointer = (u_pointer - 0.5) * vec2(aspect, 1.0);
  p += (pointer - p) * 0.025 * u_pointer_strength;

  float t = u_time * 0.18;
  vec2 origin = vec2(-0.38 * min(aspect, 1.4), 0.02);
  vec2 lp = p - origin;

  float d = chromeField(lp, t);
  float fill = 1.0 - smoothstep(-0.008, 0.006, d);
  float edge = exp(-abs(d) * 70.0);
  float bevel = smoothstep(0.035, -0.012, d) * (1.0 - smoothstep(-0.012, 0.006, d));
  float bloom = exp(-max(d, 0.0) * 9.0) * 0.7 + exp(-max(d, 0.0) * 3.0) * 0.28;

  vec2 e = vec2(0.0018, 0.0);
  float dx = chromeField(lp + e.xy, t) - chromeField(lp - e.xy, t);
  float dy = chromeField(lp + e.yx, t) - chromeField(lp - e.yx, t);
  vec3 n = normalize(vec3(-dx, -dy, 0.38 + bevel * 0.45));

  vec3 coolDir = normalize(vec3(-0.65, 0.4, 0.55));
  vec3 warmDir = normalize(vec3( 0.8, -0.1, 0.4));
  vec3 viewDir = normalize(vec3(0.1, 0.05, 1.0));
  float ndc = max(dot(n, coolDir), 0.0);
  float ndw = max(dot(n, warmDir), 0.0);
  float fres = pow(1.0 - max(dot(n, viewDir), 0.0), 2.5);
  vec3 hCool = normalize(coolDir + viewDir);
  float spec = pow(max(dot(n, hCool), 0.0), 48.0);
  float hot  = pow(max(dot(n, hCool), 0.0), 140.0);

  float envY = n.y * 0.5 + 0.5;
  float streak = pow(clamp(n.x * 0.5 + 0.55, 0.0, 1.0), 3.0);
  vec3 env = mix(vec3(0.04, 0.05, 0.09), vec3(0.55, 0.65, 0.82), envY);
  env += vec3(0.9, 0.95, 1.0) * streak * 0.55;
  vec3 silver = vec3(0.62, 0.68, 0.78);
  vec3 metal = mix(silver * 0.22, env, 0.7);
  metal *= 0.35 + ndc * 0.55 + ndw * 0.25;
  metal += vec3(0.2, 0.5, 1.0) * (fres * 0.9 + ndc * 0.35);
  metal += vec3(1.0, 0.55, 0.2) * (ndw * 0.65 + fres * 0.2 * ndw);
  metal += vec3(0.95, 0.98, 1.0) * (spec * 0.9 + hot * 1.2);

  float core = pow(max(-d, 0.0) / 0.18, 0.7);
  metal = mix(metal, silver * 0.92 + vec3(0.12, 0.22, 0.4) * 0.18, core * fill * 0.28);

  vec3 blue  = vec3(0.25, 0.55, 1.0);
  vec3 amber = vec3(1.0, 0.58, 0.22);
  vec3 base  = vec3(0.02, 0.025, 0.04);
  vec3 col = base;
  col += blue  * bloom * 0.5;
  col += amber * bloom * 0.24;
  col += vec3(0.85, 0.9, 1.0) * bloom * 0.28;
  col = mix(col, metal, fill);
  col += blue  * edge * 0.95;
  col += amber * edge * ndw * 0.6;
  col += vec3(1.0) * edge * fres * 0.4;

  float right = smoothstep(-0.02, 0.9, p.x);
  col = mix(col, base * 1.15, right * 0.62);

  float vignette = smoothstep(1.7, 0.4, length(p * vec2(0.7, 1.05)));
  col *= mix(0.35, 1.0, vignette);
  col += (hash(uv * u_res + t) - 0.5) * 0.02;

  fragColor = vec4(max(col, 0.0), 1.0);
}
`,

    // Soft mesh-gradient backlight (UMBRA-style halo)
    glow: `#version 300 es
${COMMON}
float gaussian(float d, float sigma){
  return exp(-(d * d) / (2.0 * sigma * sigma));
}

// Soft rounded-rect SDF (center card silhouette)
float sdRoundBox(vec2 p, vec2 b, float r){
  vec2 q = abs(p) - b + r;
  return length(max(q, 0.0)) + min(max(q.x, q.y), 0.0) - r;
}

void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  float aspect = u_res.x / max(u_res.y, 1.0);
  vec2 p = (uv - 0.5) * vec2(aspect, 1.0);
  vec2 pointer = (u_pointer - 0.5) * vec2(aspect, 1.0);
  p += (pointer - p) * 0.03 * u_pointer_strength;

  float t = u_time * 0.12;

  // Slow drift for the glow lobes
  vec2 aPos = vec2(-0.42,  0.38) + 0.04 * vec2(sin(t * 0.7), cos(t * 0.5));
  vec2 bPos = vec2( 0.48,  0.12) + 0.05 * vec2(cos(t * 0.6), sin(t * 0.8));
  vec2 cPos = vec2(-0.18, -0.42) + 0.03 * vec2(sin(t * 0.9), cos(t * 0.4));
  vec2 dPos = vec2( 0.10,  0.52) + 0.03 * vec2(cos(t * 0.5), sin(t * 0.65));

  // Very soft "blurred" colored lights
  float a = gaussian(length(p - aPos), 0.38);
  float b = gaussian(length(p - bPos), 0.42);
  float c = gaussian(length(p - cPos), 0.40);
  float d = gaussian(length(p - dPos), 0.28);
  // Extra wide wash for diffusion
  float wash = gaussian(length(p - vec2(0.05, 0.08)), 0.75);

  vec3 amber  = vec3(1.00, 0.58, 0.18);
  vec3 gold   = vec3(1.00, 0.82, 0.38);
  vec3 peach  = vec3(1.00, 0.48, 0.32);
  vec3 magenta= vec3(0.95, 0.28, 1.00);
  vec3 violet = vec3(0.52, 0.18, 1.00);
  vec3 indigo = vec3(0.25, 0.08, 0.55);

  vec3 glowCol = vec3(0.0);
  glowCol += amber   * a * 1.45;
  glowCol += magenta * b * 1.35;
  glowCol += peach   * c * 1.05;
  glowCol += gold    * d * 0.95;
  glowCol += mix(violet, indigo, 0.45) * wash * 0.7;

  // Subtle mesh warping so edges feel organic (not hard orbs)
  float n = fbm(p * 1.4 + t * 0.25);
  glowCol *= 0.88 + 0.28 * n;

  // Concentric depth rings in the dark field
  float rings = abs(sin(length(p) * 7.5 - t * 0.4));
  rings = smoothstep(0.85, 1.0, rings) * 0.055;
  vec3 base = vec3(0.035, 0.035, 0.04);
  vec3 col = base + glowCol;
  col += glowCol * rings;

  // Dark frosted panel in the center — glow becomes a backlight rim
  vec2 panelHalf = vec2(0.38 * min(aspect, 1.35), 0.46);
  float panel = sdRoundBox(p, panelHalf, 0.16);
  float inside = 1.0 - smoothstep(-0.02, 0.08, panel);
  float rim = exp(-abs(panel) * 14.0);
  float bleed = exp(-max(panel, 0.0) * 9.0);
  vec3 panelCol = vec3(0.05, 0.05, 0.055);
  col = mix(col, panelCol, inside * 0.94);
  col += glowCol * (rim * 1.35 + bleed * 0.45);
  col += vec3(1.0, 0.9, 1.0) * rim * 0.22;

  // Outer falloff to pure black
  float vignette = smoothstep(1.55, 0.45, length(p * vec2(0.75, 1.0)));
  col *= mix(0.15, 1.0, vignette);

  // Fine grain to hide banding
  col += (hash(uv * u_res + t) - 0.5) * 0.02;

  fragColor = vec4(max(col, 0.0), 1.0);
}
`,

    studio: `#version 300 es
${COMMON}
void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  vec2 p = domain(uv);
  float t = u_time * 0.08;
  float b1 = fbm(p * 0.9 + vec2(t * 0.4, -t * 0.2));
  float b2 = fbm(p * 1.15 + vec2(-t * 0.25, t * 0.35) + 3.1);
  float b3 = fbm(p * 0.7 + vec2(t * 0.15, t * 0.1) + 7.7);
  vec2 c1 = vec2(-0.55, 0.55) + 0.08 * vec2(sin(t), cos(t * 0.7));
  vec2 c2 = vec2(0.65, 0.35) + 0.06 * vec2(cos(t * 0.8), sin(t * 0.5));
  float d1 = exp(-2.2 * length(p - c1));
  float d2 = exp(-2.8 * length(p - c2));
  float wash = smoothstep(0.25, 0.85, b1 * 0.55 + b2 * 0.35);
  wash *= 0.55 + d1 * 0.9 + d2 * 0.45;
  vec3 base = vec3(0.059, 0.059, 0.063);
  vec3 violet = vec3(0.42, 0.18, 0.72);
  vec3 indigo = vec3(0.25, 0.22, 0.85);
  vec3 cyan = vec3(0.12, 0.55, 0.85);
  vec3 mist = vec3(0.55, 0.45, 0.95);
  vec3 col = base;
  col = mix(col, violet * 0.55, wash * 0.55);
  col = mix(col, indigo * 0.4, d1 * 0.65);
  col = mix(col, cyan * 0.35, d2 * 0.45);
  col = mix(col, mist * 0.25, smoothstep(0.55, 0.9, b3) * 0.25);
  float vignette = smoothstep(1.55, 0.35, length(p * vec2(0.85, 1.05)));
  col = mix(base, col, vignette * 0.92);
  col += (hash(uv * u_res * 0.5 + t) - 0.5) * 0.015;
  fragColor = vec4(col, 1.0);
}
`,

    aurora: `#version 300 es
${COMMON}
void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  vec2 p = domain(uv) * 1.6;
  float t = u_time * 0.12;
  float n = fbm(p + vec2(0.0, t));
  float curtain = smoothstep(0.35, 0.9, n + 0.15 * sin(p.x * 2.5 + t));
  float rays = pow(max(curtain, 0.0), 1.6) * (0.65 + 0.35 * sin(p.x * 8.0 + n * 4.0));
  vec3 base = vec3(0.059, 0.059, 0.063);
  vec3 a = vec3(0.35, 0.2, 0.85);
  vec3 b = vec3(0.15, 0.75, 0.7);
  vec3 c = vec3(0.35, 0.55, 1.0);
  vec3 col = mix(base, a, curtain * 0.55);
  col = mix(col, b, rays * 0.45);
  col = mix(col, c, pow(rays, 2.0) * 0.25);
  col *= mix(0.55, 1.0, smoothstep(1.4, 0.2, length(p * 0.5)));
  fragColor = vec4(col, 1.0);
}
`,
    plasma: `#version 300 es
${COMMON}
void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  vec2 p = domain(uv) * 1.35;
  float t = u_time * 0.18;
  float n = fbm(p * 1.1 + t * 0.5);
  float field = smoothstep(0.2, 0.85, n);
  vec3 base = vec3(0.059, 0.059, 0.063);
  vec3 a = vec3(0.45, 0.15, 0.75);
  vec3 b = vec3(0.1, 0.55, 0.85);
  vec3 col = mix(base, mix(a, b, field), field * 0.7);
  col += vec3(0.7, 0.4, 0.9) * pow(field, 3.0) * 0.2;
  fragColor = vec4(col, 1.0);
}
`,
    mesh: `#version 300 es
${COMMON}
void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  vec2 p = domain(uv) * 2.4;
  float t = u_time * 0.08;
  float n = fbm(p * 0.8 + t);
  vec2 g = p + 0.35 * vec2(fbm(p + n), fbm(p - n));
  float lines = abs(sin(g.x * 4.5)) * abs(sin(g.y * 4.5));
  float grid = smoothstep(0.82, 0.98, lines);
  vec3 base = vec3(0.059, 0.059, 0.063);
  vec3 accent = vec3(0.3, 0.85, 0.7);
  vec3 glow = vec3(0.25, 0.45, 0.95);
  vec3 col = mix(base, glow, n * 0.28);
  col = mix(col, accent, grid * 0.55);
  fragColor = vec4(col, 1.0);
}
`,
    silk: `#version 300 es
${COMMON}
void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  vec2 p = domain(uv) * 1.5;
  float t = u_time * 0.1;
  float n = fbm(p * vec2(0.8, 1.8) + vec2(0.0, t));
  float sheen = smoothstep(0.4, 0.9, n);
  vec3 base = vec3(0.059, 0.059, 0.063);
  vec3 mid = vec3(0.35, 0.2, 0.5);
  vec3 light = vec3(0.75, 0.55, 0.85);
  vec3 col = mix(base, mid, n * 0.65);
  col = mix(col, light, sheen * 0.35);
  fragColor = vec4(col, 1.0);
}
`,
    ember: `#version 300 es
${COMMON}
void main(){
  vec2 uv = gl_FragCoord.xy / u_res;
  vec2 p = domain(uv) * 1.5;
  float t = u_time * 0.14;
  float n = fbm(p + vec2(0.0, -t * 0.6));
  float field = smoothstep(0.35, 0.9, n);
  vec3 base = vec3(0.059, 0.059, 0.063);
  vec3 red = vec3(0.65, 0.15, 0.1);
  vec3 orange = vec3(0.9, 0.4, 0.12);
  vec3 col = mix(base, red, field * 0.55);
  col = mix(col, orange, pow(field, 2.2) * 0.35);
  fragColor = vec4(col, 1.0);
}
`,
};

function compile(gl: WebGL2RenderingContext, type: number, src: string): WebGLShader | null {
    const shader = gl.createShader(type);
    if (!shader) return null;
    gl.shaderSource(shader, src);
    gl.compileShader(shader);
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
        gl.deleteShader(shader);
        return null;
    }
    return shader;
}

function createProgram(gl: WebGL2RenderingContext, variant: Variant): WebGLProgram | null {
    const vs = compile(gl, gl.VERTEX_SHADER, VERT);
    const fs = compile(gl, gl.FRAGMENT_SHADER, FRAG[variant] || FRAG.studio);
    if (!vs || !fs) return null;
    const program = gl.createProgram();
    if (!program) return null;
    gl.attachShader(program, vs);
    gl.attachShader(program, fs);
    gl.linkProgram(program);
    gl.deleteShader(vs);
    gl.deleteShader(fs);
    if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
        gl.deleteProgram(program);
        return null;
    }
    return program;
}

function resolveVariant(raw: string | null): Variant {
    const v = (raw || 'studio') as Variant;
    return FRAG[v] ? v : 'studio';
}

function prefersReducedMotion(): boolean {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export class ShaderHeroManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-shader-hero]').forEach((root) => this.mount(root));
        this.bindPickers();
    }

    private bindPickers(): void {
        document.querySelectorAll<HTMLElement>('[data-shader-hero-picker]').forEach((picker) => {
            const targetSel = picker.getAttribute('data-shader-hero-picker');
            const target = targetSel
                ? document.querySelector<HTMLElement>(targetSel)
                : picker.querySelector<HTMLElement>('[data-shader-hero]');
            if (!target) return;

            picker.querySelectorAll<HTMLElement>('[data-shader-variant]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const variant = resolveVariant(btn.getAttribute('data-shader-variant'));
                    picker.querySelectorAll<HTMLElement>('[data-shader-variant]').forEach((b) => {
                        const on = b === btn;
                        b.classList.toggle('is-active', on);
                        b.setAttribute('aria-pressed', on ? 'true' : 'false');
                    });
                    target.setAttribute('data-variant', variant);
                    target.className = target.className
                        .split(/\s+/)
                        .filter((c) => !c.startsWith('shader-hero--variant-'))
                        .concat(`shader-hero--variant-${variant}`)
                        .join(' ');
                    this.remount(target);
                });
            });
        });
    }

    remount(root: HTMLElement): void {
        const el = root as HeroRoot;
        el.__shaderHeroDispose?.();
        el.__shaderHeroBound = false;
        el.classList.remove('is-ready', 'is-static', 'is-fallback');

        // Replace canvas so a fresh WebGL2 context can be acquired.
        const old = root.querySelector<HTMLCanvasElement>('[data-shader-hero-canvas]');
        if (old?.parentElement) {
            const next = document.createElement('canvas');
            next.className = old.className;
            next.setAttribute('data-shader-hero-canvas', '');
            old.parentElement.replaceChild(next, old);
        }

        this.mount(root);
    }

    mount(root: HTMLElement): void {
        const el = root as HeroRoot;
        if (el.__shaderHeroBound) return;
        el.__shaderHeroBound = true;

        const variant = resolveVariant(root.getAttribute('data-variant'));
        root.classList.add(`shader-hero--variant-${variant}`);

        const canvas = root.querySelector<HTMLCanvasElement>('[data-shader-hero-canvas]');
        if (!canvas) {
            root.classList.add('is-fallback');
            return;
        }

        if (prefersReducedMotion()) {
            root.classList.add('is-static');
            return;
        }

        const gl = canvas.getContext('webgl2', {
            alpha: false,
            antialias: false,
            depth: false,
            stencil: false,
            powerPreference: 'low-power',
        });
        if (!gl) {
            root.classList.add('is-fallback');
            return;
        }

        const program = createProgram(gl, variant);
        if (!program) {
            root.classList.add('is-fallback');
            return;
        }

        const buf = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, buf);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1]), gl.STATIC_DRAW);

        const loc = gl.getAttribLocation(program, 'a_pos');
        gl.enableVertexAttribArray(loc);
        gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);

        const uRes = gl.getUniformLocation(program, 'u_res');
        const uTime = gl.getUniformLocation(program, 'u_time');
        const uPointer = gl.getUniformLocation(program, 'u_pointer');
        const uPointerStrength = gl.getUniformLocation(program, 'u_pointer_strength');

        gl.useProgram(program);

        let raf = 0;
        let running = false;
        let start = performance.now();
        let pointer = { x: 0.5, y: 0.5 };
        let target = { x: 0.5, y: 0.5 };
        let pointerStrength = 0;

        const resize = () => {
            const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
            const w = Math.max(1, Math.floor(root.clientWidth * dpr));
            const h = Math.max(1, Math.floor(root.clientHeight * dpr));
            if (canvas.width !== w || canvas.height !== h) {
                canvas.width = w;
                canvas.height = h;
                gl.viewport(0, 0, w, h);
            }
        };

        const frame = (now: number) => {
            if (!running) return;
            resize();
            pointer.x += (target.x - pointer.x) * 0.05;
            pointer.y += (target.y - pointer.y) * 0.05;
            pointerStrength += ((target.x !== 0.5 || target.y !== 0.5 ? 1 : 0) - pointerStrength) * 0.04;

            gl.uniform2f(uRes, canvas.width, canvas.height);
            gl.uniform1f(uTime, (now - start) / 1000);
            gl.uniform2f(uPointer, pointer.x, 1 - pointer.y);
            gl.uniform1f(uPointerStrength, pointerStrength);
            gl.drawArrays(gl.TRIANGLES, 0, 6);
            raf = requestAnimationFrame(frame);
        };

        const startLoop = () => {
            if (running) return;
            running = true;
            root.classList.add('is-ready');
            raf = requestAnimationFrame(frame);
        };

        const stopLoop = () => {
            running = false;
            cancelAnimationFrame(raf);
        };

        const onPointer = (e: PointerEvent) => {
            const rect = root.getBoundingClientRect();
            if (!rect.width || !rect.height) return;
            target.x = (e.clientX - rect.left) / rect.width;
            target.y = (e.clientY - rect.top) / rect.height;
        };

        const onLeave = () => {
            target.x = 0.5;
            target.y = 0.5;
        };

        root.addEventListener('pointermove', onPointer, { passive: true });
        root.addEventListener('pointerleave', onLeave);

        const io = new IntersectionObserver(
            (entries) => {
                const visible = entries.some((e) => e.isIntersecting);
                if (visible) startLoop();
                else stopLoop();
            },
            { threshold: 0.05 },
        );
        io.observe(root);

        const ro = typeof ResizeObserver !== 'undefined' ? new ResizeObserver(() => resize()) : null;
        ro?.observe(root);

        const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        const onMotion = () => {
            if (motionQuery.matches) {
                stopLoop();
                root.classList.add('is-static');
                root.classList.remove('is-ready');
            } else {
                root.classList.remove('is-static');
                startLoop();
            }
        };
        motionQuery.addEventListener?.('change', onMotion);

        el.__shaderHeroDispose = () => {
            stopLoop();
            io.disconnect();
            ro?.disconnect();
            motionQuery.removeEventListener?.('change', onMotion);
            root.removeEventListener('pointermove', onPointer);
            root.removeEventListener('pointerleave', onLeave);
            gl.deleteProgram(program);
            gl.deleteBuffer(buf);
            el.__shaderHeroDispose = undefined;
        };

        resize();
        startLoop();
    }
}
