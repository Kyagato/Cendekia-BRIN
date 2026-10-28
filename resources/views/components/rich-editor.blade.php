@props([
    'name',
    'id' => null,
    'value' => '',
    'placeholder' => 'Tuliskan di sini...',
    'rows' => 4
])

@php
    $elementId = $id ?? $name;
@endphp

<style>
    .rich-editor-content ul {
        list-style-type: disc !important;
        padding-left: 1.5rem !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
    .rich-editor-content ol {
        list-style-type: decimal !important;
        padding-left: 1.5rem !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
    .rich-editor-content ol[style*="lower-alpha"] {
        list-style-type: lower-alpha !important;
    }
    .rich-editor-content li {
        display: list-item !important;
    }
</style>

<div x-data="{
    content: @js($value ?? ''),
    active: {
        bold: false,
        italic: false,
        underline: false,
        strikeThrough: false,
        justifyLeft: false,
        justifyCenter: false,
        justifyRight: false,
        justifyFull: false,
        insertUnorderedList: false,
        insertOrderedList: false,
        isAlphaList: false,
        fontSize: ''
    },
    init() {
        if (this.$refs.editor) {
            this.$refs.editor.innerHTML = this.content || '';
        }
        if (this.$refs.hiddenInput) {
            this.$refs.hiddenInput.value = this.content || '';
        }
        // Pastikan sync sebelum form disubmit
        const form = this.$el.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                this.sync();
            });
        }
        document.addEventListener('selectionchange', () => {
            if (this.$refs.editor && (this.$refs.editor === document.activeElement || this.$refs.editor.contains(document.activeElement))) {
                this.updateActiveStates();
            }
        });
    },
    updateActiveStates() {
        if (!this.$refs.editor) return;
        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;

        const anchor = sel.anchorNode;
        if (!anchor || (anchor !== this.$refs.editor && !this.$refs.editor.contains(anchor))) {
            return;
        }

        try {
            this.active.bold = document.queryCommandState('bold');
            this.active.italic = document.queryCommandState('italic');
            this.active.underline = document.queryCommandState('underline');
            this.active.strikeThrough = document.queryCommandState('strikeThrough');
            this.active.justifyLeft = document.queryCommandState('justifyLeft');
            this.active.justifyCenter = document.queryCommandState('justifyCenter');
            this.active.justifyRight = document.queryCommandState('justifyRight');
            this.active.justifyFull = document.queryCommandState('justifyFull');
            this.active.insertUnorderedList = document.queryCommandState('insertUnorderedList');
            this.active.insertOrderedList = document.queryCommandState('insertOrderedList');

            let node = sel.getRangeAt(0).startContainer;
            let isAlpha = false;
            while (node && node !== this.$refs.editor) {
                if (node.tagName === 'OL' && (node.style.listStyleType === 'lower-alpha' || node.getAttribute('type') === 'a')) {
                    isAlpha = true;
                    break;
                }
                node = node.parentNode;
            }
            this.active.isAlphaList = isAlpha;
            this.active.fontSize = document.queryCommandValue('fontSize') || '';
        } catch (e) {
            // Ignore if queryCommand fails
        }
    },
    exec(cmd, arg = null) {
        this.$refs.editor.focus();
        document.execCommand(cmd, false, arg);
        this.sync();
        this.updateActiveStates();
    },
    changeFontSize(val) {
        if (val) {
            this.$refs.editor.focus();
            document.execCommand('fontSize', false, val);
            this.sync();
            this.updateActiveStates();
        }
    },
    execOrderedLetters() {
        this.$refs.editor.focus();
        document.execCommand('insertOrderedList', false, null);
        const selection = window.getSelection();
        if (selection && selection.rangeCount > 0) {
            let node = selection.getRangeAt(0).startContainer;
            while (node && node !== this.$refs.editor) {
                if (node.tagName === 'OL') {
                    node.style.listStyleType = 'lower-alpha';
                    break;
                }
                node = node.parentNode;
            }
        }
        this.sync();
        this.updateActiveStates();
    },
    sync() {
        if (this.$refs.editor && this.$refs.hiddenInput) {
            this.content = this.$refs.editor.innerHTML;
            this.$refs.hiddenInput.value = this.content;
        }
    }
}" class="rounded-lg border border-slate-300 dark:border-slate-600 overflow-hidden bg-white dark:bg-slate-900 focus-within:border-slate-400 dark:focus-within:border-slate-500 transition shadow-sm">

    {{-- Interactive Toolbar --}}
    <div class="bg-slate-100 dark:bg-slate-800 border-b border-slate-300 dark:border-slate-700 p-2 flex flex-wrap items-center gap-1 text-slate-700 dark:text-slate-300 select-none">
        
        {{-- Styling: B, I, U, S --}}
        <button type="button" @mousedown.prevent @click="exec('bold')" title="Bold (Tebal)"
            :class="active.bold ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner font-black' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded font-extrabold text-sm transition">B</button>
        <button type="button" @mousedown.prevent @click="exec('italic')" title="Italic (Miring)"
            :class="active.italic ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner font-bold' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded italic text-sm transition">I</button>
        <button type="button" @mousedown.prevent @click="exec('underline')" title="Underline (Garis Bawah)"
            :class="active.underline ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner font-bold' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded underline text-sm transition">U</button>
        <button type="button" @mousedown.prevent @click="exec('strikeThrough')" title="Strikethrough (Coret)"
            :class="active.strikeThrough ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner font-bold' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded line-through text-sm transition">S</button>

        <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 mx-1"></div>

        {{-- Ukuran Font Selection --}}
        <select :value="active.fontSize" @change="changeFontSize($event.target.value)" title="Ukuran Font" class="h-8 pl-3 pr-7 text-xs rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer font-medium min-w-[105px]">
            <option value="">Ukuran Font</option>
            <option value="1">10</option>
            <option value="2">12</option>
            <option value="3">14</option>
            <option value="4">18</option>
            <option value="5">24</option>
            <option value="6">32</option>
        </select>

        <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 mx-1"></div>

        {{-- Alignment: Left, Center/Middle, Right, Justify --}}
        <button type="button" @mousedown.prevent @click="exec('justifyLeft')" title="Rata Kiri (Align Left)"
            :class="active.justifyLeft ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
        </button>
        <button type="button" @mousedown.prevent @click="exec('justifyCenter')" title="Rata Tengah (Align Middle)"
            :class="active.justifyCenter ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
        </button>
        <button type="button" @mousedown.prevent @click="exec('justifyRight')" title="Rata Kanan (Align Right)"
            :class="active.justifyRight ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"/></svg>
        </button>
        <button type="button" @mousedown.prevent @click="exec('justifyFull')" title="Rata Kiri Kanan (Justify)"
            :class="active.justifyFull ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 mx-1"></div>

        {{-- Lists: Bullets, Numbering (Angka & Huruf) --}}
        <button type="button" @mousedown.prevent @click="exec('insertUnorderedList')" title="List Bullets (Peluru)"
            :class="active.insertUnorderedList ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
        </button>
        <button type="button" @mousedown.prevent @click="exec('insertOrderedList')" title="List Angka (1, 2, 3)"
            :class="active.insertOrderedList && !active.isAlphaList ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded text-xs font-bold transition">1.</button>
        <button type="button" @mousedown.prevent @click="execOrderedLetters()" title="List Huruf (a, b, c)"
            :class="active.isAlphaList ? 'bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white shadow-inner' : 'hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded text-xs font-bold transition">a.</button>

    </div>

    {{-- ContentEditable Editor Area --}}
    <div x-ref="editor"
         contenteditable="true"
         @input="sync()"
         @blur="sync()"
         @keyup="sync(); updateActiveStates()"
         @click="updateActiveStates()"
         @mouseup="updateActiveStates()"
         style="min-height: {{ $rows * 32 }}px;"
         class="p-4 focus:outline-none text-slate-800 dark:text-slate-100 text-sm leading-relaxed overflow-y-auto rich-editor-content"
         placeholder="{{ $placeholder }}">
    </div>

    {{-- Hidden Input synced with Form Submission --}}
    <textarea name="{{ $name }}" id="{{ $elementId }}" x-ref="hiddenInput" class="hidden">{{ $value }}</textarea>
</div>
