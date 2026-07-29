<div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] flex items-center justify-center p-6">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Live Chat</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A] mt-2">Create or join a room to start chatting</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-[#161615] rounded-xl shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 space-y-4">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Create Room</h2>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Your Name</label>
                    <input
                        wire:model="name"
                        type="text"
                        placeholder="Enter your name"
                        class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-transparent text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] focus:outline-none focus:ring-2 focus:ring-[#f53003]"
                    />
                    @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
                <button
                    wire:click="createRoom"
                    class="w-full px-4 py-3 bg-[#f53003] hover:bg-[#d42a00] text-white font-medium rounded-lg transition-colors"
                >
                    Create Room
                </button>
            </div>

            <div class="bg-white dark:bg-[#161615] rounded-xl shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 space-y-4">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Join Room</h2>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Your Name</label>
                    <input
                        wire:model="name"
                        type="text"
                        placeholder="Enter your name"
                        class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-transparent text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] focus:outline-none focus:ring-2 focus:ring-[#f53003]"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Room ID</label>
                    <input
                        wire:model="joinRoomId"
                        type="text"
                        placeholder="Enter room ID"
                        maxlength="6"
                        class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-transparent text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] focus:outline-none focus:ring-2 focus:ring-[#f53003] uppercase"
                    />
                    @error('joinRoomId') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
                <button
                    wire:click="joinRoom"
                    class="w-full px-4 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] font-medium rounded-lg hover:bg-[#f5f5f4] dark:hover:bg-[#1c1c1a] transition-colors"
                >
                    Join Room
                </button>
            </div>
        </div>
    </div>
</div>
