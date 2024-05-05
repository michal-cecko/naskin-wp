<ul>
    @foreach($services as $service)
        <li>{{ $service->name }} ({{ $service->duration }}min / {{ $service->price }}€)</li>
    @endforeach
</ul>