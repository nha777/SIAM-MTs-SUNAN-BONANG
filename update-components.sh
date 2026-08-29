sed -i '/<!-- 2. Inputs -->/!b;n;c\
    <section>\
        <h2 class="text-xl font-semibold mb-6 pb-2 border-b border-surface-200 text-surface-900">2. Form Groups (\&lt;x-form-group\&gt;)</h2>\
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">\
            <x-form-group name="name" label="Standard Input">\
                <x-input type="text" id="name" name="name" placeholder="John Doe" />\
            </x-form-group>\
            <x-form-group name="email" label="Required Input" required helpText="We will never share your email.">\
                <x-input type="email" id="email" name="email" placeholder="john@example.com" required />\
            </x-form-group>\
            <x-form-group name="status" label="Select Input">\
                <x-select id="status" name="status">\
                    <option>Active</option>\
                    <option>Inactive</option>\
                </x-select>\
            </x-form-group>\
            <x-form-group name="notes" label="Textarea">\
                <x-textarea id="notes" name="notes" rows="3" placeholder="Enter notes..."></x-textarea>\
            </x-form-group>\
        </div>\
    </section>\
' resources/views/dev/components.blade.php
