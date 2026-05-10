<section class="rounded-2xl border bg-white p-5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold">Sessions</h2>
            <p class="text-sm text-slate-500">Online session dengan para students.</p>
        </div>

        @if($session)
            <button type="button"
                    wire:click="editSession"
                    class="rounded-xl border px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                Edit
            </button>
        @else
            <button type="button"
                    wire:click="editSession"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-700">
                Add Session
            </button>
        @endif
    </div>

    <div class="mt-5">
        @if($session)
            <div class="rounded-2xl border p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">{{ $session->title }}</div>
                        <div class="mt-1 text-xs text-slate-500">
                            {{ $session->start_at?->format('d M Y H:i') }} · {{ $session->end_at?->format('d M Y H:i') }}
                        </div>
                    </div>

                    <span class="rounded-full border px-2 py-1 text-[11px] uppercase tracking-wide text-slate-600">
                        {{ $session->status }}
                    </span>
                </div>

                <div class="mt-3 space-y-1 text-sm">
                    <div class="truncate">
                        <span class="text-slate-500">Zoom:</span>
                        <a href="{{ $session->zoom_link }}" target="_blank" class="font-medium underline">Open link</a>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-dashed p-6 text-sm text-slate-500">
                Belum ada session.
            </div>
        @endif
    </div>

    <x-ui.mentor.modal
        :show="$showSessionModal"
        title="{{ $session ? 'Edit Session' : 'Add Session' }}"
        subtitle="Session untuk topik terkini."
        wire:click="closeSessionModal"
    >
        <form wire:submit.prevent="saveSession" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Title</label>
                    <input wire:model.defer="sessionTitle" class="mt-1 w-full rounded-xl border px-4 py-2" placeholder="Session title">
                </div>
                
                <div>
                    <label class="text-xs font-medium text-slate-500">Start At</label>
                    <input wire:model.defer="sessionStartAt" type="datetime-local" class="mt-1 w-full rounded-xl border px-4 py-2">
                </div>
                
                <div>
                    <label class="text-xs font-medium text-slate-500">End At</label>
                    <input wire:model.defer="sessionEndAt" type="datetime-local" class="mt-1 w-full rounded-xl border px-4 py-2">
                </div>
                
                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Zoom Link</label>
                    <input wire:model.defer="sessionZoomLink" class="mt-1 w-full rounded-xl border px-4 py-2" placeholder="https://...">
                </div>
                
                <div class="sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Status</label>
                    <select wire:model.defer="sessionStatus" class="mt-1 w-full rounded-xl border px-4 py-2">
                        <option value="scheduled">Scheduled</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    @error('sessionTitle') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('sessionStartAt') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('sessionEndAt') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('sessionZoomLink') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @error('sessionStatus') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t pt-4">
                <button type="button" wire:click="closeSessionModal" class="rounded-xl border px-4 py-2 text-sm text-slate-600">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                    {{ $session ? 'Update Session' : 'Save Session' }}
                </button>
            </div>
        </form>
    </x-ui.mentor.modal>
</section>