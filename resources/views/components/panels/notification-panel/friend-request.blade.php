@props(['friend-request'])

<div class="panel-friend-request">
    <img src="{{ $request->avatar }}" class="panel-av" />
    <div class="panel-item-body">
        <p><strong>{{ $request->name }}</strong> wants to be friends</p>
        <span class="panel-meta">{{ $request->date }}</span>
        <div class="panel-actions">
            <button class="panel-btn-accept">Accept</button>
            <button class="panel-btn-decline">Decline</button>
        </div>
    </div>
</div>
