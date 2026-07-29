<div class="fixed inset-0 bg-[#FDFDFC] dark:bg-[#0a0a0a] flex flex-col" wire:poll.3s>
    <div class="bg-white dark:bg-[#161615] border-b border-[#e3e3e0] dark:border-[#3E3E3A] px-4 py-3 flex items-center justify-between shrink-0">
        <div>
            <h1 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Room: {{ $room->room_id }}</h1>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Chatting as <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $name }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            @if ($isCreator)
                <button
                    wire:click="deleteRoom"
                    wire:confirm="Are you sure you want to delete this room?"
                    class="px-3 py-1.5 text-sm border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                >
                    Delete Room
                </button>
            @endif
            <a
                href="{{ route('home') }}"
                wire:navigate
                class="px-3 py-1.5 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f4] dark:hover:bg-[#1c1c1a] transition-colors"
            >
                Leave
            </a>
        </div>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto px-4 py-4 flex flex-col-reverse gap-3" x-data x-ref="messages">
        @forelse ($messages as $message)
            @php $canEdit = $message->name === $name && $message->created_at->diffInMinutes(now()) < 10; @endphp
            <div class="flex {{ $message->name === $name ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] {{ $message->name === $name ? 'bg-[#f53003] text-white' : 'bg-white dark:bg-[#1c1c1a] border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]' }} rounded-lg px-3 py-2">
                    @if ($message->name !== $name)
                        <p class="text-xs font-medium text-[#f53003] mb-1">{{ $message->name }}</p>
                    @endif
                    @if ($message->image_path)
                        <div class="grid {{ count($message->imageUrls()) > 1 ? 'grid-cols-2 gap-1' : '' }} mb-1">
                            @foreach ($message->imageUrls() as $url)
                                <img src="{{ $url }}" alt="Shared image" class="w-full rounded-lg" loading="lazy">
                            @endforeach
                        </div>
                    @endif
                    @if ($editingMessageId === $message->id)
                        <div class="flex gap-1">
                            <input
                                wire:model="editBody"
                                type="text"
                                class="flex-1 px-2 py-1 text-sm rounded border border-[#e3e3e0] dark:border-[#3E3E3A] bg-transparent focus:outline-none focus:ring-1 focus:ring-[#f53003] {{ $message->name === $name ? 'text-white' : 'text-[#1b1b18] dark:text-[#EDEDEC]' }}"
                                wire:keydown.enter="updateMessage({{ $message->id }})"
                                wire:keydown.escape="cancelEdit"
                                wire:keydown.tab="cancelEdit"
                            >
                            <button wire:click="updateMessage({{ $message->id }})" class="text-xs px-1.5 py-1 rounded bg-[#f53003] text-white">Save</button>
                            <button wire:click="cancelEdit" class="text-xs px-1.5 py-1 rounded opacity-60">X</button>
                        </div>
                    @elseif ($message->body)
                        <p class="text-sm">{!! preg_replace('/@(\S+)/', '<span class="font-semibold text-[#f53003]">@$1</span>', e($message->body)) !!}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-xs opacity-60">{{ $message->created_at->format('h:i A') }}</p>
                        @if ($canEdit && $editingMessageId !== $message->id)
                            <button wire:click="editMessage({{ $message->id }})" class="text-xs opacity-60 hover:opacity-100 transition-opacity">Edit</button>
                            <button wire:click="deleteMessage({{ $message->id }})" wire:confirm="Delete this message?" class="text-xs opacity-60 hover:opacity-100 transition-opacity">Delete</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-[#706f6c] dark:text-[#A1A09A] py-8">
                <p>No messages yet. Start the conversation!</p>
            </div>
        @endforelse
    </div>

    <div class="bg-white dark:bg-[#161615] border-t border-[#e3e3e0] dark:border-[#3E3E3A] px-4 py-3 shrink-0" x-data="mention($wire)" data-participants='@json($participants)'>
        <form @submit.prevent="send()" class="flex gap-2 items-end">
            <div class="flex-1 space-y-2 relative">
                @if (count($images) > 0)
                    <div class="flex gap-1 flex-wrap shrink-0">
                        @foreach ($images as $index => $img)
                            <div class="relative">
                                <img src="{{ $img->temporaryUrl() }}" alt="Preview" class="h-10 w-10 rounded object-cover border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                <button type="button" wire:click="$set('images.{{ $index }}', null)" class="absolute inset-0 bg-black/40 rounded flex items-center justify-center text-white text-lg opacity-0 hover:opacity-100 transition-opacity">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="flex gap-2 items-end">
                    <div class="flex-1 relative">
                        <input
                            x-model="text"
                            x-ref="input"
                            @keydown="onKeydown"
                            @input="onInput"
                            type="text"
                            placeholder="Type @ to mention someone..."
                            class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-transparent text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] focus:outline-none focus:ring-2 focus:ring-[#f53003]"
                        />
                    </div>
                    <label class="shrink-0 px-2 py-2 cursor-pointer text-[#706f6c] dark:text-[#A1A09A] hover:text-[#f53003] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <input type="file" wire:model="images" accept="image/*" multiple class="hidden">
                    </label>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-[#f53003] hover:bg-[#d42a00] text-white font-medium rounded-lg transition-colors shrink-0"
                    >
                        Send
                    </button>
                </div>
                @error('images.*') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                @error('body') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
            </div>
        </form>
        <ul
            x-show="showDropdown && filtered.length > 0"
            class="fixed z-[999] bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg shadow-lg overflow-hidden py-1"
            x-bind:style="dropdownStyle"
        >
            <template x-for="(p, i) in filtered" :key="p">
                <li
                    @mousedown.prevent="select(p)"
                    @mouseenter="hoverIndex = i"
                    :class="i === hoverIndex ? 'bg-[#f53003] text-white' : 'text-[#1b1b18] dark:text-[#EDEDEC]'"
                    class="px-3 py-1.5 text-sm cursor-pointer transition-colors"
                    x-text="p"
                ></li>
            </template>
        </ul>
    </div>

    @script
    <script>
        Alpine.data('mention', ($wire) => ({
            text: '',
            showDropdown: false,
            hoverIndex: 0,

            init() {
                this.$watch('text', (val) => {
                    this.$wire.body = val;
                });
            },

            send() {
                this.$wire.sendMessage(this.text);
                this.text = '';
            },

            get participants() {
                try {
                    return JSON.parse(this.$el.closest('[data-participants]')?.dataset?.participants || '[]');
                } catch {
                    return [];
                }
            },

            get filtered() {
                if (!this.showDropdown) return [];
                const query = this.getMentionQuery();
                if (!query && query !== '') return this.participants;
                return this.participants.filter(p =>
                    p.toLowerCase().includes(query.toLowerCase())
                );
            },

            get dropdownStyle() {
                const el = this.$refs.input;
                if (!el) return 'display:none';
                const rect = el.getBoundingClientRect();
                return `bottom:${window.innerHeight - rect.top + 4}px; left:${rect.left}px; width:${rect.width}px`;
            },

            getMentionQuery() {
                const cursor = this.$refs.input?.selectionStart ?? this.text.length;
                const before = this.text.slice(0, cursor);
                const atIndex = before.lastIndexOf('@');
                if (atIndex === -1) return null;
                return before.slice(atIndex + 1);
            },

            onInput() {
                const query = this.getMentionQuery();
                if (query !== null && this.participants.some(p => p.toLowerCase().includes(query.toLowerCase()))) {
                    this.showDropdown = true;
                    this.hoverIndex = 0;
                } else {
                    this.showDropdown = false;
                }
            },

            onKeydown(e) {
                if (!this.showDropdown) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.send();
                    }
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    this.hoverIndex = Math.min(this.hoverIndex + 1, this.filtered.length - 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    this.hoverIndex = Math.max(this.hoverIndex - 1, 0);
                } else if (e.key === 'Enter' || e.key === 'Tab') {
                    e.preventDefault();
                    if (this.filtered[this.hoverIndex]) {
                        this.select(this.filtered[this.hoverIndex]);
                    }
                } else if (e.key === 'Escape') {
                    this.showDropdown = false;
                }
            },

            select(name) {
                const cursor = this.$refs.input?.selectionStart ?? this.text.length;
                const before = this.text.slice(0, cursor);
                const after = this.text.slice(cursor);
                const atIndex = before.lastIndexOf('@');
                const newText = before.slice(0, atIndex) + '@' + name + ' ' + after;
                this.text = newText;
                this.$wire.body = newText;
                this.showDropdown = false;
                this.$nextTick(() => {
                    const pos = atIndex + name.length + 2;
                    this.$refs.input?.setSelectionRange(pos, pos);
                    this.$refs.input?.focus();
                });
            },
        }));
    </script>
    @endscript
</div>
