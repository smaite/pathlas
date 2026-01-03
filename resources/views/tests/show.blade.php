@extends('layouts.app')
@section('title', $test->name)
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Test Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold">{{ $test->name }}</h2>
                    <span class="px-3 py-1 text-sm {{ $test->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full">
                        {{ $test->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-gray-500 mt-1">{{ $test->code }} • {{ $test->category?->name }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tests.edit', $test) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Edit Test</a>
                <a href="{{ route('tests.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Back</a>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-6 mt-6">
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-2xl font-bold text-primary-600">₹{{ number_format($test->price) }}</p>
                <p class="text-sm text-gray-500">Price</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-2xl font-bold text-primary-600">{{ $test->parameters->count() }}</p>
                <p class="text-sm text-gray-500">Parameters</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-lg font-medium">{{ ucfirst($test->sample_type) }}</p>
                <p class="text-sm text-gray-500">Sample Type</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-lg font-medium">{{ $test->turnaround_time ?? 1 }} day</p>
                <p class="text-sm text-gray-500">TAT</p>
            </div>
        </div>
    </div>

    <!-- Clinical Notes / Interpretation Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="font-semibold text-lg">Clinical Notes / Interpretation</h3>
                <p class="text-sm text-gray-500">Appears on report below test results - supports tables!</p>
            </div>
            <button id="saveInterpretationBtn" onclick="saveInterpretation()" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 hidden">
                Save Changes
            </button>
        </div>
        <div id="interpretation-editor" class="border border-gray-200 rounded-xl"></div>
        <p class="text-xs text-gray-500 mt-2">💡 Click table icon to add diagnostic range tables like Glucose levels.</p>
    </div>

    <!-- Test Parameters - Grouped & Draggable -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-lg">Test Parameters</h3>
                <p class="text-sm text-gray-500">Drag to reorder • Click group header to expand/collapse</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('add-group-modal').classList.remove('hidden')"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Group
                </button>
                <button onclick="document.getElementById('add-param-modal').classList.remove('hidden')"
                    class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Parameter
                </button>
            </div>
        </div>

        <div class="p-6" id="params-container">
            @php
            $groups = $test->parameters->groupBy('group_name');
            $ungrouped = $groups->pull('') ?? collect();
            if($groups->has(null)) {
            $ungrouped = $ungrouped->merge($groups->pull(null));
            }
            @endphp

            <!-- Grouped Parameters -->
            @foreach($groups as $groupName => $params)
            <div class="group-section mb-4 bg-gray-50 rounded-xl overflow-hidden" data-group="{{ $groupName }}">
                <div class="group-header flex items-center justify-between px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white cursor-pointer" onclick="toggleGroup(this)">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 transform transition-transform group-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span class="font-semibold">{{ $groupName }}</span>
                        <span class="px-2 py-0.5 bg-white/20 rounded text-xs">{{ $params->count() }} params</span>
                    </div>
                    <div class="flex gap-2" onclick="event.stopPropagation()">
                        <button onclick="editGroup('{{ $groupName }}')" class="p-1 hover:bg-white/20 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg></button>
                    </div>
                </div>
                <div class="group-content sortable-list p-3 space-y-2">
                    @foreach($params->sortBy('sort_order') as $param)
                    @include('tests.partials.param-block', ['param' => $param, 'test' => $test])
                    @endforeach
                </div>
            </div>
            @endforeach

            <!-- Parameters without group (display flat) -->
            @if($ungrouped->count() > 0)
            <div class="space-y-2 mb-4" data-group="">
                @foreach($ungrouped->sortBy('sort_order') as $param)
                @include('tests.partials.param-block', ['param' => $param, 'test' => $test])
                @endforeach
            </div>
            @endif

            @if($test->parameters->count() === 0)
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h4 class="font-medium text-gray-600 mb-2">No Parameters Yet</h4>
                <p class="text-gray-500 mb-4">Add groups and parameters like Hemoglobin, WBC, RBC, etc.</p>
                <button onclick="document.getElementById('add-param-modal').classList.remove('hidden')" class="px-6 py-2 bg-primary-600 text-white rounded-xl">Add First Parameter</button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Group Modal -->
<div id="add-group-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-md m-4 p-6">
        <h3 class="text-lg font-semibold mb-4">Add New Group</h3>
        <form action="{{ route('tests.parameters.store', $test) }}" method="POST">
            @csrf
            <input type="hidden" name="is_group_header" value="1">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Group Name *</label>
                <input type="text" name="group_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="e.g. RBC INDICES">
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-xl">Create Group</button>
                <button type="button" onclick="document.getElementById('add-group-modal').classList.add('hidden')" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Parameter Modal -->
<div id="add-param-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Add Parameter</h3>
        </div>
        <form action="{{ route('tests.parameters.store', $test) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group *</label>
                    <select name="group_name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="">-- Select Group --</option>
                        @foreach($groups->keys() as $gn)
                        <option value="{{ $gn }}">{{ $gn }}</option>
                        @endforeach
                        <option value="__new__">+ Create New Group</option>
                    </select>
                </div>
                <div class="col-span-2 hidden" id="new-group-input">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Group Name</label>
                    <input type="text" id="new_group_name_input" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="e.g. RBC INDICES">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parameter Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="e.g. Hemoglobin">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="e.g. HB">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" name="unit" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="e.g. g/dL">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Min (Male)</label>
                    <input type="number" step="0.01" name="normal_min_male" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Max (Male)</label>
                    <input type="number" step="0.01" name="normal_max_male" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Min (Female)</label>
                    <input type="number" step="0.01" name="normal_min_female" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Max (Female)</label>
                    <input type="number" step="0.01" name="normal_max_female" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>

                <div class="col-span-2 bg-green-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-green-800 mb-2">⚡ Auto-Calculate Formula</label>
                    <div class="flex flex-wrap items-center gap-2 mb-3" id="formula-builder">
                        <select id="formula-param1" class="px-3 py-2 border border-green-300 rounded-lg bg-white text-sm">
                            <option value="">Select Parameter</option>
                            @foreach($test->parameters->where('code', '!=', null) as $p)
                            <option value="{{ $p->code }}">{{ $p->name }} ({{ $p->code }})</option>
                            @endforeach
                        </select>
                        <select id="formula-operator" class="px-3 py-2 border border-green-300 rounded-lg bg-white text-sm font-bold">
                            <option value="">Op</option>
                            <option value="+">+</option>
                            <option value="-">−</option>
                            <option value="*">×</option>
                            <option value="/">/</option>
                        </select>
                        <select id="formula-param2" class="px-3 py-2 border border-green-300 rounded-lg bg-white text-sm">
                            <option value="">Select Parameter</option>
                            @foreach($test->parameters->where('code', '!=', null) as $p)
                            <option value="{{ $p->code }}">{{ $p->name }} ({{ $p->code }})</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="buildFormula()" class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                            Build
                        </button>
                        <button type="button" onclick="clearFormula()" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                            Clear
                        </button>
                    </div>
                    <input type="text" name="formula" id="formula-input" class="w-full px-4 py-2 border border-green-200 rounded-lg bg-white" placeholder="Formula will appear here..." readonly>
                    <p class="text-xs text-green-600 mt-1">Select parameters and operator, then click Build. Or type manually: {T.Prot} - {Alb}</p>
                </div>

                <!-- Parameter Interpretation -->
                <div class="col-span-2 bg-amber-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-amber-800 mb-2">📋 Parameter Interpretation</label>
                    <textarea name="interpretation" rows="3" class="w-full px-4 py-2 border border-amber-200 rounded-lg bg-white" placeholder="Interpretation notes for this parameter (appears below parameter in report)"></textarea>
                    <p class="text-xs text-amber-600 mt-1">This will appear below the parameter value in the report</p>
                </div>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-xl">Add Parameter</button>
                <button type="button" onclick="document.getElementById('add-param-modal').classList.add('hidden')" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Parameter Modal -->
<div id="edit-param-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Edit Parameter</h3>
        </div>
        <form id="edit-param-form" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                    <input type="text" name="group_name" id="edit-group_name" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parameter Name *</label>
                    <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" id="edit-code" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" name="unit" id="edit-unit" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Min (Male)</label>
                    <input type="number" step="0.01" name="normal_min_male" id="edit-normal_min_male" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Max (Male)</label>
                    <input type="number" step="0.01" name="normal_max_male" id="edit-normal_max_male" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Min (Female)</label>
                    <input type="number" step="0.01" name="normal_min_female" id="edit-normal_min_female" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Normal Max (Female)</label>
                    <input type="number" step="0.01" name="normal_max_female" id="edit-normal_max_female" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                </div>


                <div class="col-span-2 bg-green-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-green-800 mb-2">⚡ Auto-Calculate Formula</label>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <select id="edit-formula-param1" class="px-3 py-2 border border-green-300 rounded-lg bg-white text-sm">
                            <option value="">Select Parameter</option>
                            @foreach($test->parameters->where('code', '!=', null) as $p)
                            <option value="{{ $p->code }}">{{ $p->name }} ({{ $p->code }})</option>
                            @endforeach
                        </select>
                        <select id="edit-formula-operator" class="px-3 py-2 border border-green-300 rounded-lg bg-white text-sm font-bold">
                            <option value="">Op</option>
                            <option value="+">+</option>
                            <option value="-">−</option>
                            <option value="*">×</option>
                            <option value="/">/</option>
                        </select>
                        <select id="edit-formula-param2" class="px-3 py-2 border border-green-300 rounded-lg bg-white text-sm">
                            <option value="">Select Parameter</option>
                            @foreach($test->parameters->where('code', '!=', null) as $p)
                            <option value="{{ $p->code }}">{{ $p->name }} ({{ $p->code }})</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="buildEditFormula()" class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                            Build
                        </button>
                        <button type="button" onclick="clearEditFormula()" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                            Clear
                        </button>
                    </div>
                    <input type="text" name="formula" id="edit-formula" class="w-full px-4 py-2 border border-green-200 rounded-lg bg-white" placeholder="Formula will appear here...">
                    <p class="text-xs text-green-600 mt-1">Select params and operator, click Build. Or type: {Param1} + {Param2}</p>
                </div>

                <!-- Parameter Interpretation -->
                <div class="col-span-2 bg-amber-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-amber-800 mb-2">📋 Parameter Interpretation</label>
                    <textarea name="interpretation" id="edit-interpretation" rows="3" class="w-full px-4 py-2 border border-amber-200 rounded-lg bg-white" placeholder="Interpretation notes for this parameter (appears below parameter in report)"></textarea>
                    <p class="text-xs text-amber-600 mt-1">This interpretation will appear below the parameter value in the report</p>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="edit-is_active" value="1" class="w-5 h-5 text-primary-600 rounded">
                        <span>Active</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-xl">Update Parameter</button>
                <button type="button" onclick="document.getElementById('edit-param-modal').classList.add('hidden')" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Toggle group collapse
    function toggleGroup(header) {
        const content = header.nextElementSibling;
        const arrow = header.querySelector('.group-arrow');
        content.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Initialize Sortable on each group
    document.querySelectorAll('.sortable-list').forEach(list => {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'opacity-50',
            group: 'params',
            onEnd: function(evt) {
                updateParamOrder();
            }
        });
    });

    function updateParamOrder() {
        const data = [];
        document.querySelectorAll('.param-block').forEach((block, idx) => {
            const groupSection = block.closest('.group-section');
            const groupName = groupSection ? groupSection.dataset.group : '';
            data.push({
                id: block.dataset.id,
                sort_order: idx + 1,
                group_name: groupName
            });
        });

        fetch('/tests/{{ $test->id }}/parameters/reorder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                parameters: data
            })
        }).then(response => {
            if (response.ok) {
                showSaveNotification();
            }
        });
    }

    function showSaveNotification() {
        let toast = document.getElementById('save-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'save-toast';
            toast.className = 'fixed bottom-4 right-4 px-4 py-3 bg-green-600 text-white rounded-xl shadow-lg flex items-center gap-2 transform translate-y-20 opacity-0 transition-all duration-300 z-50';
            toast.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Order saved!';
            document.body.appendChild(toast);
        }

        setTimeout(() => {
            toast.classList.remove('translate-y-20', 'opacity-0');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 2000);
    }

    // Edit parameter
    function editParam(param) {
        document.getElementById('edit-param-form').action = '/tests/{{ $test->id }}/parameters/' + param.id;
        document.getElementById('edit-name').value = param.name || '';
        document.getElementById('edit-code').value = param.code || '';
        document.getElementById('edit-unit').value = param.unit || '';
        document.getElementById('edit-group_name').value = param.group_name || '';
        document.getElementById('edit-normal_min_male').value = param.normal_min_male || '';
        document.getElementById('edit-normal_max_male').value = param.normal_max_male || '';
        document.getElementById('edit-normal_min_female').value = param.normal_min_female || '';
        document.getElementById('edit-normal_max_female').value = param.normal_max_female || '';
        document.getElementById('edit-formula').value = param.formula || '';
        document.getElementById('edit-interpretation').value = param.interpretation || '';
        document.getElementById('edit-is_active').checked = param.is_active;
        document.getElementById('edit-param-modal').classList.remove('hidden');
    }

    // Group select change handler
    document.querySelector('select[name="group_name"]').addEventListener('change', function() {
        const newGroupDiv = document.getElementById('new-group-input');
        if (this.value === '__new__') {
            newGroupDiv.classList.remove('hidden');
            document.getElementById('new_group_name_input').required = true;
        } else {
            newGroupDiv.classList.add('hidden');
            document.getElementById('new_group_name_input').required = false;
        }
    });

    // Handle new group creation in form
    document.querySelector('#add-param-modal form').addEventListener('submit', function(e) {
        const select = this.querySelector('select[name="group_name"]');
        if (select.value === '__new__') {
            const newName = document.getElementById('new_group_name_input').value;
            if (newName) {
                select.value = '';
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'group_name';
                hidden.value = newName;
            }
        }
    });

    // Formula Builder Functions (Add Modal)
    function buildFormula() {
        const param1 = document.getElementById('formula-param1').value;
        const operator = document.getElementById('formula-operator').value;
        const param2 = document.getElementById('formula-param2').value;
        const input = document.getElementById('formula-input');

        if (param1 && operator && param2) {
            input.value = `{${param1}} ${operator} {${param2}}`;
            input.removeAttribute('readonly');
        } else if (param1 && !operator && !param2) {
            input.value = `{${param1}}`;
            input.removeAttribute('readonly');
        }
    }

    function clearFormula() {
        document.getElementById('formula-param1').value = '';
        document.getElementById('formula-operator').value = '';
        document.getElementById('formula-param2').value = '';
        document.getElementById('formula-input').value = '';
    }

    // Formula Builder Functions (Edit Modal)
    function buildEditFormula() {
        const param1 = document.getElementById('edit-formula-param1').value;
        const operator = document.getElementById('edit-formula-operator').value;
        const param2 = document.getElementById('edit-formula-param2').value;
        const input = document.getElementById('edit-formula');

        if (param1 && operator && param2) {
            input.value = `{${param1}} ${operator} {${param2}}`;
        } else if (param1 && !operator && !param2) {
            input.value = `{${param1}}`;
        }
    }

    function clearEditFormula() {
        document.getElementById('edit-formula-param1').value = '';
        document.getElementById('edit-formula-operator').value = '';
        document.getElementById('edit-formula-param2').value = '';
        document.getElementById('edit-formula').value = '';
    }
</script>

{{-- CKEditor 5 for Clinical Notes --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<style>
    .ck-editor__editable {
        min-height: 200px;
    }

    .ck-content table {
        border-collapse: collapse;
        width: 100%;
        margin: 10px 0;
    }

    .ck-content table td,
    .ck-content table th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .ck-content table th {
        background: #f5f5f5;
        font-weight: bold;
    }
</style>
<script>
    let interpretationEditor;
    let originalContent = '';

    ClassicEditor
        .create(document.querySelector('#interpretation-editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'insertTable', '|', 'undo', 'redo'],
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            },
            placeholder: 'Add clinical notes, diagnostic ranges, or interpretation tables here...'
        })
        .then(editor => {
            interpretationEditor = editor;
            // Load existing content
            @if($test - > interpretation)
            editor.setData(@json($test - > interpretation));
            originalContent = @json($test - > interpretation);
            @endif

            // Show save button when content changes
            editor.model.document.on('change:data', () => {
                const currentContent = editor.getData();
                const saveBtn = document.getElementById('saveInterpretationBtn');
                if (currentContent !== originalContent) {
                    saveBtn.classList.remove('hidden');
                } else {
                    saveBtn.classList.add('hidden');
                }
            });
        })
        .catch(error => console.error(error));

    function saveInterpretation() {
        const content = interpretationEditor.getData();
        const btn = document.getElementById('saveInterpretationBtn');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        fetch('{{ route('
                tests.update ', $test) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        interpretation: content,
                        name: '{{ $test->name }}',
                        code: '{{ $test->code }}',
                        category_id: {
                            {
                                $test - > category_id
                            }
                        },
                        price: {
                            {
                                $test - > price
                            }
                        }
                    })
                })
            .then(response => response.json())
            .then(data => {
                btn.textContent = 'Saved!';
                btn.classList.add('bg-green-600');
                originalContent = content;
                setTimeout(() => {
                    btn.textContent = 'Save Changes';
                    btn.classList.remove('bg-green-600');
                    btn.classList.add('hidden');
                    btn.disabled = false;
                }, 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                btn.textContent = 'Error - Retry';
                btn.disabled = false;
            });
    }
</script>
@endsection