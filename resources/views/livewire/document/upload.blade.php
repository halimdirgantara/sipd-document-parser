<!-- resources/views/livewire/rka-uploader.blade.php -->
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow-xl rounded-lg p-6 mb-4">
        <h2 class="text-2xl font-bold mb-6">Upload Documents</h2>

        @if ($processing)
            <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded">
                <div class="flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Processing documents... ({{ $processedCount }}/{{ $totalFiles }})
                </div>
            </div>
        @endif

        @if ($success)
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                Documents processed successfully!
            </div>
        @endif

        @if ($error)
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ $error }}
            </div>
        @endif

        <form wire:submit="save">
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                <div class="flex flex-col items-center justify-center space-y-2">
                    <label for="documents" class="cursor-pointer">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="mt-2 text-base text-gray-600">
                                Click to select PDFs or drag and drop
                            </span>
                            <span class="mt-1 text-sm text-gray-500">
                                You can select multiple files
                            </span>
                        </div>
                        <input id="documents" type="file" wire:model="documents" class="hidden" accept=".pdf" multiple>
                    </label>
                </div>

                @error('documents')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('documents.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if (count($documents) > 0)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Selected files:</h4>
                        <ul class="space-y-2">
                            @foreach($documents as $document)
                                <li class="text-sm text-gray-600">
                                    {{ $document->getClientOriginalName() }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <button type="submit"
                class="w-full mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Upload and Process</span>
                <span wire:loading>Processing...</span>
            </button>
        </form>
    </div>

    <div class="bg-white shadow-xl rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Parsed Documents</h2>

        @foreach ($entities as $entity)
            <div class="mb-8 border rounded-lg p-4">
                <div class="mb-4">
                    <h3 class="text-xl font-semibold mb-4">Entity Details</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Affair
                                    </th>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $entity->affair }}</td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sector
                                    </th>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $entity->sector }}</td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Organization</th>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $entity->organization }}</td>
                                </tr>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub
                                        Organization</th>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $entity->sub_organization }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach ($entity->programs as $program)
                    <div class="ml-4 mb-4">
                        <h4 class="text-lg font-semibold mb-2">{{ $program->name }}</h4>

                        @foreach ($program->activities as $activity)
                            <div class="mb-4">
                                <h5 class="text-md font-semibold mb-2">{{ $activity->name }}</h5>

                                @foreach ($activity->subActivities as $subActivity)
                                    <div class="mb-4">
                                        <h6 class="font-semibold mb-2">{{ $subActivity->name }}</h6>

                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Account Code</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Name</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Specification</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Quantity</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Unit</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Price</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Tax</th>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach ($subActivity->items as $item)
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ $item->account_code }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ $item->specification }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ $item->quantity }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ number_format($item->price, 2) }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ number_format($item->tax, 2) }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                {{ number_format($item->total, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
