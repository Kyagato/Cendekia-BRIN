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
    init() {
        if (this.$refs.editor) {
            this.$refs.editor.innerHTML = this.content || '';
        }
        if (this.$refs.hiddenInput) {
            this.$refs.hiddenInput.value = this.content || '';
        }
    },
    exec(cmd, arg = null) {
        this.$refs.editor.focus();
        document.execCommand(cmd, false, arg);
        this.sync();
    },
    changeFontSize(val) {
        if (val) {
            this.$refs.editor.focus();
            document.execCommand('fontSize', false, val);
            this.sync();
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
    },
    sync() {
        if (this.$refs.editor && this.$refs.hiddenInput) {
            this.content = this.$refs.editor.innerHTML;
            this.$refs.hiddenInput.value = this.content;
        }
    }
}" class="rounded-lg border border-slate-300 dark:border-slate-600 overflow-hidden bg-white dark:bg-slate-900 focus-within:ring-2 focus-within:ring-red-600 focus-within:border-red-600 transition shadow-sm">

    {{-- Interactive Toolbar --}}
    <div class="bg-slate-100 dark:bg-slate-800 border-b border-slate-300 dark:border-slate-700 p-2 flex flex-wrap items-center gap-1 text-slate-700 dark:text-slate-300 select-none">
        
        {{-- Styling: B, I, U, S --}}
        <button type="button" @click="exec('bold')" title="Bold (Tebal)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 font-extrabold text-sm transition">B</button>
        <button type="button" @click="exec('italic')" title="Italic (Miring)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 italic text-sm transition">I</button>
        <button type="button" @click="exec('underline')" title="Underline (Garis Bawah)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 underline text-sm transition">U</button>
        <button type="button" @click="exec('strikeThrough')" title="Strikethrough (Coret)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 line-through text-sm transition">S</button>

        <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 mx-1"></div>

        {{-- Ukuran Font Selection --}}
        <select @change="changeFontSize($event.target.value)" title="Ukuran Font" class="h-8 pl-3 pr-7 text-xs rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer font-medium min-w-[105px]">
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
        <button type="button" @click="exec('justifyLeft')" title="Rata Kiri (Align Left)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
        </button>
        <button type="button" @click="exec('justifyCenter')" title="Rata Tengah (Align Middle)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
        </button>
        <button type="button" @click="exec('justifyRight')" title="Rata Kanan (Align Right)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"/></svg>
        </button>
        <button type="button" @click="exec('justifyFull')" title="Rata Kiri Kanan (Justify)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 mx-1"></div>

        {{-- Lists: Bullets, Numbering (Angka & Huruf) --}}
        <button type="button" @click="exec('insertUnorderedList')" title="List Bullets (Peluru)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
        </button>
        <button type="button" @click="exec('insertOrderedList')" title="List Angka (1, 2, 3)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition">1.</button>
        <button type="button" @click="execOrderedLetters()" title="List Huruf (a, b, c)" class="w-8 h-8 flex items-center justify-center rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition">a.</button>

    </div>

    {{-- ContentEditable Editor Area --}}
    <div x-ref="editor"
         contenteditable="true"
         @input="sync()"
         @blur="sync()"
         @keyup="sync()"
         style="min-height: {{ $rows * 32 }}px;"
         class="p-4 focus:outline-none text-slate-800 dark:text-slate-100 text-sm leading-relaxed overflow-y-auto rich-editor-content"
         placeholder="{{ $placeholder }}">
    </div>

    {{-- Hidden Input synced with Form Submission --}}
    <textarea name="{{ $name }}" id="{{ $elementId }}" x-ref="hiddenInput" class="hidden">{{ $value }}</textarea>
</div>
