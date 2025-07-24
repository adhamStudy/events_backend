<div class="p-6">
    <div class="space-y-4">
        <!-- Sender Information -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">From</h4>
            <div class="mt-2">
                <p class="text-lg font-semibold text-gray-900">{{ $message->user->name }}</p>
                <p class="text-sm text-gray-600">{{ $message->user->email }}</p>
            </div>
        </div>

        <!-- Message Content -->
        <div>
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Message</h4>
            <div class="mt-2 bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-gray-900 whitespace-pre-wrap leading-relaxed">{{ $message->body }}</p>
            </div>
        </div>

        <!-- Timestamp -->
        <div class="text-sm text-gray-500 border-t pt-4">
            <span>Received: {{ $message->created_at->format('M d, Y \a\t g:i A') }}</span>
            @if($message->created_at != $message->updated_at)
                <span class="ml-4">Updated: {{ $message->updated_at->format('M d, Y \a\t g:i A') }}</span>
            @endif
        </div>

        <!-- Status Badge -->
        <div class="flex justify-end border-t pt-4">
            @if($message->status === 'pending')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pending Review
                </span>
            @elseif($message->status === 'success')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Accepted
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Rejected
                </span>
            @endif
        </div>
    </div>
</div>