<div x-data="{ show: false, actionUrl: '', title: '', message: '' }" 
     @open-confirm-modal.window="show = true; actionUrl = $event.detail.actionUrl; title = $event.detail.title; message = $event.detail.message"
     @keydown.escape.window="show = false"
     x-show="show" 
     class="relative z-50" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true" 
     x-cloak>
    
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-surface-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="show" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="show = false"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-danger-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-danger-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-surface-900" id="modal-title" x-text="title">Konfirmasi</h3>
                            <div class="mt-2">
                                <p class="text-sm text-surface-500" x-text="message">Apakah Anda yakin ingin melakukan tindakan ini?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form :action="actionUrl" method="POST" class="inline-flex w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-danger-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-danger-500 sm:ml-3 sm:w-auto">
                            Ya, Lanjutkan
                        </button>
                    </form>
                    <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
