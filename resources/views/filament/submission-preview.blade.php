@php
    /** @var \App\Models\Submission $record */
    $record = $getRecord();
    $headers = $record->previewHeaders();
    $rows = $record->previewRows();
    $error = $record->parsed_preview_data['error'] ?? null;
@endphp

<div class="space-y-3">
    @if ($error)
        {{-- A file that would not parse still reaches review; the admin just
             downloads it instead of reading a preview. --}}
        <div class="flex items-start gap-3 rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800
                    dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
            <x-filament::icon icon="heroicon-m-exclamation-triangle" class="mt-0.5 h-5 w-5" />
            <div>
                <p class="font-semibold">This file could not be read</p>
                <p class="mt-1 opacity-90">Download it to check it by hand. Reported: {{ $error }}</p>
            </div>
        </div>
    @elseif (empty($headers) && empty($rows))
        <p class="text-sm text-gray-500 dark:text-gray-400">No preview was captured for this upload.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10" dir="ltr">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="w-10 border-b border-r border-gray-200 px-2 py-2 text-center font-mono text-[10px]
                                   font-medium text-gray-400 dark:border-white/10">#</th>
                        @foreach ($headers as $header)
                            <th class="whitespace-nowrap border-b border-r border-gray-200 px-3 py-2 text-left text-xs
                                       font-semibold text-gray-700 last:border-r-0 dark:border-white/10 dark:text-gray-200">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($rows as $index => $row)
                        <tr class="odd:bg-white even:bg-gray-50/60 dark:odd:bg-transparent dark:even:bg-white/[0.02]">
                            <td class="border-b border-r border-gray-200 bg-gray-50 px-2 py-1.5 text-center font-mono
                                       text-[10px] text-gray-400 dark:border-white/10 dark:bg-white/5">
                                {{ $index + 2 }}
                            </td>
                            @foreach ($headers as $column => $_)
                                <td class="max-w-[220px] truncate border-b border-r border-gray-200 px-3 py-1.5 font-mono
                                           text-xs text-gray-600 last:border-r-0 dark:border-white/10 dark:text-gray-300"
                                    title="{{ $row[$column] ?? '' }}">
                                    {{ $row[$column] ?? '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
            <span>
                Showing <span class="font-mono font-medium">{{ count($rows) }}</span>
                of <span class="font-mono font-medium">{{ $record->previewRowCount() }}</span> rows
            </span>
            <span>Sheet <span class="font-mono">{{ $record->parsed_preview_data['sheet'] ?? '—' }}</span></span>
            <span>File <span class="font-mono">{{ $record->fileName() }}</span></span>
        </div>
    @endif
</div>
