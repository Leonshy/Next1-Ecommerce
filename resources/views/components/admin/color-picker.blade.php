@props([
    'name'        => 'color',
    'value'       => '',
    'label'       => 'Color',
    'listenEvent' => null,
])

@php $uid = 'cp_' . Str::random(8); @endphp

<div x-data="colorPicker_{{ $uid }}()"
     x-init="init()"
     @if($listenEvent) x-on:{{ $listenEvent }}.window="setValue($event.detail.value)" @endif>

    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    @endif

    <div class="flex items-center gap-2">
        {{-- Swatch --}}
        <button type="button" x-ref="trigger" @click="togglePicker()"
                class="w-9 h-9 rounded-lg border border-gray-300 flex-shrink-0 relative overflow-hidden shadow-sm">
            <span class="absolute inset-0" style="background-image:linear-gradient(45deg,#ccc 25%,transparent 25%),linear-gradient(-45deg,#ccc 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#ccc 75%),linear-gradient(-45deg,transparent 75%,#ccc 75%);background-size:8px 8px;background-position:0 0,0 4px,4px -4px,-4px 0"></span>
            <span class="absolute inset-0" :style="{ background: cssColor }"></span>
        </button>

        {{-- Texto editable --}}
        <input type="text" x-model="textVal" @input.debounce.500ms="onTextInput()"
               placeholder="Ej: #1a4a6b  /  rgba(0,0,0,.5)"
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
        <input type="hidden" name="{{ $name }}" :value="textVal">
    </div>

    {{-- ─── Popover ──────────────────────────────────────────── --}}
    <div x-show="open" x-cloak
         @click.outside="open = false"
         @keydown.escape.window="open = false"
         class="fixed z-[90] bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 select-none"
         style="width:272px"
         :style="pos">

        {{-- SV (saturación × valor) --}}
        <div x-ref="sv"
             class="relative w-full rounded-lg overflow-hidden mb-3 cursor-crosshair"
             style="height:148px"
             :style="{ background: 'linear-gradient(to right,#fff,hsl('+h+',100%,50%))' }"
             @mousedown.prevent="dragStart($event, $refs.sv, 'sv')"
             @touchstart.prevent="dragStart($event.touches[0], $refs.sv, 'sv')">
            <div class="absolute inset-0 rounded-lg" style="background:linear-gradient(to bottom,transparent,#000)">
                <div class="absolute w-4 h-4 rounded-full border-2 border-white shadow pointer-events-none"
                     :style="{ left: s+'%', top: (100-v)+'%', transform:'translate(-50%,-50%)' }"></div>
            </div>
        </div>

        {{-- Hue --}}
        <div class="mb-1">
            <div x-ref="hSlider"
                 class="relative h-4 rounded-full cursor-pointer overflow-hidden"
                 style="background:linear-gradient(to right,#f00 0%,#ff0 17%,#0f0 33%,#0ff 50%,#00f 67%,#f0f 83%,#f00 100%)"
                 @mousedown.prevent="dragStart($event, $refs.hSlider, 'h')"
                 @touchstart.prevent="dragStart($event.touches[0], $refs.hSlider, 'h')">
                <div class="absolute top-1/2 w-4 h-4 rounded-full border-2 border-white shadow pointer-events-none"
                     :style="{ left: (h/360*100)+'%', transform:'translate(-50%,-50%)' }"></div>
            </div>
            <p class="text-[10px] text-gray-400 text-right mt-0.5">Tono</p>
        </div>

        {{-- Alpha --}}
        <div class="mb-3">
            <div x-ref="aSlider"
                 class="relative h-4 rounded-full cursor-pointer overflow-hidden"
                 style="background-image:linear-gradient(45deg,#ccc 25%,transparent 25%),linear-gradient(-45deg,#ccc 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#ccc 75%),linear-gradient(-45deg,transparent 75%,#ccc 75%);background-size:8px 8px;background-position:0 0,0 4px,4px -4px,-4px 0"
                 @mousedown.prevent="dragStart($event, $refs.aSlider, 'a')"
                 @touchstart.prevent="dragStart($event.touches[0], $refs.aSlider, 'a')">
                <div class="absolute inset-0 rounded-full" :style="{ background: 'linear-gradient(to right,transparent,'+rgbStr+')' }"></div>
                <div class="absolute top-1/2 w-4 h-4 rounded-full border-2 border-white shadow pointer-events-none"
                     :style="{ left: (a*100)+'%', transform:'translate(-50%,-50%)' }"></div>
            </div>
            <p class="text-[10px] text-gray-400 text-right mt-0.5">Transparencia</p>
        </div>

        {{-- Tabs --}}
        <div class="flex border border-gray-200 rounded-lg overflow-hidden text-xs mb-3">
            <button type="button" @click="mode='hex'"  :class="mode==='hex'  ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="flex-1 py-1.5 font-medium transition-colors">HEX</button>
            <button type="button" @click="mode='rgb'"  :class="mode==='rgb'  ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="flex-1 py-1.5 font-medium transition-colors">RGB</button>
            <button type="button" @click="mode='cmyk'" :class="mode==='cmyk' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'" class="flex-1 py-1.5 font-medium transition-colors">CMYK</button>
        </div>

        {{-- HEX inputs --}}
        <div x-show="mode==='hex'" class="flex gap-2">
            <div class="flex-1">
                <input type="text" x-model="hexIn" @change="fromHex()" maxlength="9"
                       class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center font-mono focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30">
                <p class="text-[10px] text-gray-400 text-center mt-0.5">HEX</p>
            </div>
            <div class="w-16">
                <input type="number" min="0" max="100" x-model.number="aNum" @change="fromAlpha()"
                       class="w-full border border-gray-200 rounded-lg px-1 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30">
                <p class="text-[10px] text-gray-400 text-center mt-0.5">Opac. %</p>
            </div>
        </div>

        {{-- RGB inputs --}}
        <div x-show="mode==='rgb'" class="grid grid-cols-4 gap-1.5">
            <div class="text-center"><input type="number" min="0" max="255" x-model.number="rIn" @change="fromRgb()" class="w-full border border-gray-200 rounded px-1 py-1.5 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">R</p></div>
            <div class="text-center"><input type="number" min="0" max="255" x-model.number="gIn" @change="fromRgb()" class="w-full border border-gray-200 rounded px-1 py-1.5 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">G</p></div>
            <div class="text-center"><input type="number" min="0" max="255" x-model.number="bIn" @change="fromRgb()" class="w-full border border-gray-200 rounded px-1 py-1.5 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">B</p></div>
            <div class="text-center"><input type="number" min="0" max="100" x-model.number="aNum" @change="fromAlpha()" class="w-full border border-gray-200 rounded px-1 py-1.5 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">A%</p></div>
        </div>

        {{-- CMYK inputs --}}
        <div x-show="mode==='cmyk'" class="grid grid-cols-5 gap-1">
            <div class="text-center"><input type="number" min="0" max="100" x-model.number="cIn" @change="fromCmyk()" class="w-full border border-gray-200 rounded px-1 py-1 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">C</p></div>
            <div class="text-center"><input type="number" min="0" max="100" x-model.number="mIn" @change="fromCmyk()" class="w-full border border-gray-200 rounded px-1 py-1 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">M</p></div>
            <div class="text-center"><input type="number" min="0" max="100" x-model.number="yIn" @change="fromCmyk()" class="w-full border border-gray-200 rounded px-1 py-1 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">Y</p></div>
            <div class="text-center"><input type="number" min="0" max="100" x-model.number="kIn" @change="fromCmyk()" class="w-full border border-gray-200 rounded px-1 py-1 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">K</p></div>
            <div class="text-center"><input type="number" min="0" max="100" x-model.number="aNum" @change="fromAlpha()" class="w-full border border-gray-200 rounded px-1 py-1 text-xs text-center focus:outline-none focus:ring-1 focus:ring-[#1a4a6b]/40"><p class="text-[10px] text-gray-400 mt-0.5">A%</p></div>
        </div>

        {{-- Preview final --}}
        <div class="mt-3 flex items-center gap-2">
            <div class="relative h-6 w-20 rounded overflow-hidden border border-gray-200 flex-shrink-0">
                <span class="absolute inset-0" style="background-image:linear-gradient(45deg,#ccc 25%,transparent 25%),linear-gradient(-45deg,#ccc 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#ccc 75%),linear-gradient(-45deg,transparent 75%,#ccc 75%);background-size:8px 8px;background-position:0 0,0 4px,4px -4px,-4px 0"></span>
                <span class="absolute inset-0" :style="{ background: cssColor }"></span>
            </div>
            <code class="text-[10px] text-gray-500 font-mono truncate" x-text="cssColor"></code>
        </div>
    </div>
</div>

<script>
function colorPicker_{{ $uid }}() {

    /* ── Utilidades de color ──────────────────────────────────────────── */
    function hsvToRgb(h, s, v) {
        s /= 100; v /= 100;
        const c = v * s, x = c * (1 - Math.abs((h / 60) % 2 - 1)), m = v - c;
        let r = 0, g = 0, b = 0;
        if      (h <  60) { r = c; g = x; }
        else if (h < 120) { r = x; g = c; }
        else if (h < 180) { g = c; b = x; }
        else if (h < 240) { g = x; b = c; }
        else if (h < 300) { r = x; b = c; }
        else              { r = c; b = x; }
        return [Math.round((r+m)*255), Math.round((g+m)*255), Math.round((b+m)*255)];
    }

    function rgbToHsv(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r,g,b), min = Math.min(r,g,b), d = max - min;
        let h = 0, s = max === 0 ? 0 : d / max, v = max;
        if (d !== 0) {
            if      (max === r) h = ((g - b) / d + 6) % 6;
            else if (max === g) h = (b - r) / d + 2;
            else                h = (r - g) / d + 4;
            h *= 60;
        }
        return [Math.round(h), Math.round(s * 100), Math.round(v * 100)];
    }

    function rgbToCmyk(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        const k = 1 - Math.max(r, g, b);
        if (k >= 1) return [0, 0, 0, 100];
        return [
            Math.round((1 - r - k) / (1 - k) * 100),
            Math.round((1 - g - k) / (1 - k) * 100),
            Math.round((1 - b - k) / (1 - k) * 100),
            Math.round(k * 100),
        ];
    }

    function cmykToRgb(c, m, y, k) {
        return [
            Math.round(255 * (1 - c/100) * (1 - k/100)),
            Math.round(255 * (1 - m/100) * (1 - k/100)),
            Math.round(255 * (1 - y/100) * (1 - k/100)),
        ];
    }

    function h2(n) { return Math.max(0, Math.min(255, Math.round(n))).toString(16).padStart(2, '0'); }

    function parseColor(val) {
        if (!val) return null;
        val = String(val).trim();
        // rgb / rgba
        const m = val.match(/rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*([\d.]+))?\s*\)/i);
        if (m) return { r: +m[1], g: +m[2], b: +m[3], a: m[4] !== undefined ? +m[4] : 1 };
        // hex
        let hex = val.replace(/^#/, '');
        if (/^[0-9a-f]{3}$/i.test(hex)) hex = hex.split('').map(c => c+c).join('');
        if (/^[0-9a-f]{6}$/i.test(hex)) return { r: parseInt(hex.slice(0,2),16), g: parseInt(hex.slice(2,4),16), b: parseInt(hex.slice(4,6),16), a: 1 };
        if (/^[0-9a-f]{8}$/i.test(hex)) return { r: parseInt(hex.slice(0,2),16), g: parseInt(hex.slice(2,4),16), b: parseInt(hex.slice(4,6),16), a: parseInt(hex.slice(6,8),16)/255 };
        // colores nombrados (white, black, red…) via elemento temporal
        try {
            const el = document.createElement('div');
            el.style.color = val;
            document.body.appendChild(el);
            const comp = getComputedStyle(el).color;
            document.body.removeChild(el);
            const mc = comp.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
            if (mc) return { r: +mc[1], g: +mc[2], b: +mc[3], a: mc[4] !== undefined ? +mc[4] : 1 };
        } catch {}
        return null;
    }

    /* ── Componente Alpine ───────────────────────────────────────────── */
    return {
        open: false,
        pos:  '',
        mode: 'hex',
        // estado interno en HSV + alpha
        h: 0, s: 0, v: 100, a: 1,
        // inputs por modo
        hexIn: '#ffffff',
        rIn: 255, gIn: 255, bIn: 255,
        cIn: 0,   mIn: 0,   yIn: 0,   kIn: 0,
        aNum: 100,
        // valor del input de texto y del hidden
        textVal: '{{ addslashes($value) }}',

        get rgb()    { return hsvToRgb(this.h, this.s, this.v); },
        get rgbStr() { const [r,g,b] = this.rgb; return `rgb(${r},${g},${b})`; },
        get cssColor() {
            const [r, g, b] = this.rgb;
            return this.a < 1
                ? `rgba(${r},${g},${b},${parseFloat(this.a.toFixed(3))})`
                : `#${h2(r)}${h2(g)}${h2(b)}`;
        },

        init() { this.setValue(this.textVal); },

        /* Actualiza el estado interno desde un string de color */
        setValue(val) {
            const p = parseColor(val);
            if (p) {
                [this.h, this.s, this.v] = rgbToHsv(p.r, p.g, p.b);
                this.a = Math.min(1, Math.max(0, p.a));
                this._syncInputs();
                this.textVal = this.cssColor;
            } else {
                this.textVal = val ?? '';
            }
        },

        _syncInputs() {
            const [r, g, b] = this.rgb;
            this.hexIn = `#${h2(r)}${h2(g)}${h2(b)}`;
            this.rIn = r; this.gIn = g; this.bIn = b;
            [this.cIn, this.mIn, this.yIn, this.kIn] = rgbToCmyk(r, g, b);
            this.aNum = Math.round(this.a * 100);
        },

        _commit() {
            this._syncInputs();
            this.textVal = this.cssColor;
        },

        togglePicker() {
            this.open = !this.open;
            if (this.open) this.$nextTick(() => this._position());
        },

        _position() {
            const btn  = this.$refs.trigger;
            if (!btn) return;
            const r    = btn.getBoundingClientRect();
            const W    = window.innerWidth, H = window.innerHeight;
            let top    = r.bottom + 6;
            let left   = r.left;
            if (top + 460 > H) top  = Math.max(4, r.top - 460 - 6);
            if (left + 288 > W) left = Math.max(4, W - 288);
            this.pos = `top:${top}px;left:${left}px`;
        },

        onTextInput() {
            const p = parseColor(this.textVal);
            if (p) {
                [this.h, this.s, this.v] = rgbToHsv(p.r, p.g, p.b);
                this.a = p.a;
                this._syncInputs();
            }
        },

        /* ── Drag genérico ───────────────────────────────────────────── */
        dragStart(e, el, type) {
            const rect = el.getBoundingClientRect(); // captura antes del drag
            const update = (clientX, clientY) => {
                const ratio = (x, w) => Math.max(0, Math.min(1, x / w));
                if (type === 'sv') {
                    this.s = Math.round(ratio(clientX - rect.left, rect.width)  * 100);
                    this.v = Math.round((1 - ratio(clientY - rect.top, rect.height)) * 100);
                } else if (type === 'h') {
                    this.h = Math.round(ratio(clientX - rect.left, rect.width) * 360);
                } else if (type === 'a') {
                    this.a = Math.round(ratio(clientX - rect.left, rect.width) * 100) / 100;
                }
                this._commit();
            };
            update(e.clientX, e.clientY);
            const onMove = ev => update(ev.clientX, ev.clientY);
            const onUp   = ()  => { window.removeEventListener('mousemove', onMove); window.removeEventListener('mouseup', onUp); };
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup',   onUp);
        },

        /* ── Desde inputs de texto ───────────────────────────────────── */
        fromHex()   { const p = parseColor(this.hexIn); if (p) { [this.h,this.s,this.v] = rgbToHsv(p.r,p.g,p.b); this._commit(); } },
        fromAlpha() { this.a = Math.min(1, Math.max(0, this.aNum / 100)); this._commit(); },
        fromRgb()   { [this.h,this.s,this.v] = rgbToHsv(Math.min(255,Math.max(0,this.rIn)), Math.min(255,Math.max(0,this.gIn)), Math.min(255,Math.max(0,this.bIn))); this._commit(); },
        fromCmyk()  { const [r,g,b] = cmykToRgb(Math.min(100,Math.max(0,this.cIn)), Math.min(100,Math.max(0,this.mIn)), Math.min(100,Math.max(0,this.yIn)), Math.min(100,Math.max(0,this.kIn))); [this.h,this.s,this.v] = rgbToHsv(r,g,b); this._commit(); },
    };
}
</script>
