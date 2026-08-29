@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-surface-900">UI Component Library</h1>
@endsection

@section('content')
<div class="space-y-12">

    <!-- 1. Buttons -->
    <section>
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">1. Buttons (&lt;x-button&gt;)</h2>
        
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-surface-500 mb-3">Variants</h3>
                <div class="flex flex-wrap gap-4">
                    <x-button variant="primary">Primary</x-button>
                    <x-button variant="secondary">Secondary</x-button>
                    <x-button variant="danger">Danger</x-button>
                    <x-button variant="ghost">Ghost</x-button>
                </div>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-surface-500 mb-3">Sizes</h3>
                <div class="flex flex-wrap items-center gap-4">
                    <x-button size="sm">Small</x-button>
                    <x-button size="md">Medium</x-button>
                    <x-button size="lg">Large</x-button>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium text-surface-500 mb-3">States & Icons</h3>
                <div class="flex flex-wrap gap-4">
                    <x-button variant="primary" icon="plus">With Icon</x-button>
                    <x-button variant="secondary" icon="download" size="icon" aria-label="Download"></x-button>
                    <x-button variant="primary" disabled>Disabled</x-button>
                    <x-button variant="primary" loading>Loading</x-button>
                    <x-button variant="secondary" loading>Saving...</x-button>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Form Groups -->
    <section>
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">2. Form Groups (&lt;x-form-group&gt;, &lt;x-input&gt;, dll)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form-group name="name" label="Standard Input">
                <x-input type="text" id="name" name="name" placeholder="John Doe" />
            </x-form-group>
            
            <x-form-group name="email" label="Required Input" required helpText="We will never share your email.">
                <x-input type="email" id="email" name="email" placeholder="john@example.com" required />
            </x-form-group>
            
            <x-form-group name="status" label="Select Input">
                <x-select id="status" name="status">
                    <option>Active</option>
                    <option>Inactive</option>
                </x-select>
            </x-form-group>
            
            <x-form-group name="notes" label="Textarea">
                <x-textarea id="notes" name="notes" rows="3" placeholder="Enter notes..."></x-textarea>
            </x-form-group>

            <x-form-group name="disabled" label="Disabled Input">
                <x-input type="text" id="disabled" name="disabled" value="Can't touch this" disabled />
            </x-form-group>
            
            <x-form-group name="error_demo" label="Error State">
                <x-input type="text" id="error_demo" name="error_demo" value="Wrong input" error />
                <p class="mt-1 text-sm text-danger-600">This field is required.</p>
            </x-form-group>
        </div>
    </section>

    <!-- 3. Cards -->
    <section>
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">3. Cards (&lt;x-card&gt;)</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Full Card -->
            <x-card title="Card with Title & Footer" subtitle="This is a standardized subtitle using props.">
                <p class="text-surface-700">This is the standard card body content. Use this to group related information or forms. It has comfortable padding by default.</p>
                
                <x-slot name="footer">
                    <x-button variant="ghost">Cancel</x-button>
                    <x-button variant="primary">Save Changes</x-button>
                </x-slot>
            </x-card>

            <!-- Simple Card -->
            <x-card>
                <h3 class="text-lg font-medium text-surface-900 mb-2">Simple Card (No Header)</h3>
                <p class="text-surface-700 mb-4">A simple card without a distinct header or footer background.</p>
                <x-button variant="secondary" size="sm">Learn More</x-button>
            </x-card>
            
            <!-- No Padding Card (usually for tables) -->
            <x-card noPadding class="lg:col-span-2" title="Data Grid Card (noPadding)">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-200">
                        <thead class="bg-surface-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-surface-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-900">Alice</td>
                                <td class="px-6 py-4 whitespace-nowrap"><x-badge variant="success">Active</x-badge></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-900">Bob</td>
                                <td class="px-6 py-4 whitespace-nowrap"><x-badge variant="neutral">Offline</x-badge></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </section>

    <!-- 4. Badges -->
    <section>
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">4. Badges (&lt;x-badge&gt;)</h2>
        <div class="flex flex-wrap gap-4">
            <x-badge variant="success">Active</x-badge>
            <x-badge variant="neutral">Draft</x-badge>
            <x-badge variant="warning">Pending</x-badge>
            <x-badge variant="danger">Deleted</x-badge>
            <!-- Badge as a counter -->
            <x-badge variant="primary" class="rounded-full px-2 py-0.5">12 New</x-badge>
        </div>
    </section>

    <!-- 5. Empty State -->
    <section>
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">5. Empty State (&lt;x-empty-state&gt;)</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card>
                <x-empty-state 
                    type="empty"
                    title="Belum ada data siswa" 
                    description="Mulai dengan menambahkan data siswa baru atau import dari Excel."
                >
                    <x-slot name="action">
                        <x-button variant="primary" icon="plus">Tambah Siswa</x-button>
                    </x-slot>
                </x-empty-state>
            </x-card>
            
            <x-card>
                <x-empty-state 
                    type="search"
                    title="Hasil pencarian tidak ditemukan" 
                    description="Coba ubah filter atau kata kunci pencarian Anda."
                >
                    <x-slot name="action">
                        <x-button variant="secondary">Reset Filter</x-button>
                    </x-slot>
                </x-empty-state>
            </x-card>

            <x-card>
                <x-empty-state 
                    type="error"
                    title="Gagal memuat data" 
                    description="Terjadi kesalahan jaringan atau server saat memuat data. Silakan coba lagi."
                >
                    <x-slot name="action">
                        <x-button variant="secondary">Coba Lagi</x-button>
                    </x-slot>
                </x-empty-state>
            </x-card>
        </div>
    </section>

    <!-- 6. Modals -->
    <section>
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">6. Modals (&lt;x-modal&gt;)</h2>
        <div class="space-y-4">
            <x-button variant="primary" x-data="" x-on:click="$dispatch('open-modal', 'demo-modal')">Open Demo Modal</x-button>
            <x-button variant="danger" x-data="" x-on:click="$dispatch('open-modal', 'delete-modal')">Open Delete Modal</x-button>

            <!-- Demo Modal -->
            <x-modal name="demo-modal" title="Term of Service" maxWidth="lg">
                <div class="space-y-4">
                    <p class="text-surface-700">Please read our terms of service before continuing. By clicking accept, you agree to our terms.</p>
                </div>
                <x-slot name="footer">
                    <x-button variant="primary" x-on:click="$dispatch('close-modal', 'demo-modal')">Accept</x-button>
                    <x-button variant="ghost" x-on:click="$dispatch('close-modal', 'demo-modal')">Cancel</x-button>
                </x-slot>
            </x-modal>

            <!-- Delete Modal -->
            <x-modal name="delete-modal" title="Delete Confirmation" maxWidth="sm">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-danger-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-heroicon-o-exclamation-circle class="h-6 w-6 text-danger-600" />
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-surface-900">Deactivate account</h4>
                        <p class="mt-1 text-sm text-surface-500">Are you sure you want to deactivate your account? All of your data will be permanently removed.</p>
                    </div>
                </div>
                <x-slot name="footer">
                    <x-button variant="danger" x-on:click="$dispatch('close-modal', 'delete-modal')">Deactivate</x-button>
                    <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'delete-modal')">Cancel</x-button>
                </x-slot>
            </x-modal>
        </div>
    </section>

    <!-- 7. Toasts -->
    <section>
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">7. Toasts (&lt;x-toast&gt;)</h2>
        <div class="space-y-4">
            <x-toast type="success" message="Data berhasil disimpan!" />
            <x-toast type="error" message="Gagal menyimpan data.">
                <x-slot name="description">Pastikan koneksi internet Anda stabil.</x-slot>
            </x-toast>
            <x-toast type="warning" message="Perhatian: Kuota storage hampir penuh." />
            <x-toast type="info" message="Update sistem tersedia." />
        </div>
    </section>

</div>
@endsection
