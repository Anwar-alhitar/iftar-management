@props(['status'])

@if($status)
    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg mt-4">
        <div class="flex items-center">
            <x-heroicon-s-exclamation-circle class="w-6 h-6 mr-2 text-red-600"/>
            <span>⚠️ تنبيه: هذا المستفيد قد استلم وجبة اليوم!</span>
        </div>
    </div>
@endif
