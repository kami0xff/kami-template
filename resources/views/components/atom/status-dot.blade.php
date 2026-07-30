@props([
    'status' => 'offline', // live | busy | away | offline
])

<span {{ $attributes->class(['status-dot', "status-dot--$status"]) }} aria-hidden="true"></span>
