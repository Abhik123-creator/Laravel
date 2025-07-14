<h2>New Submission for: {{ $formName }}</h2>
<ul>
@foreach($data as $key => $value)
    <li><strong>{{ $key }}:</strong> {{ is_array($value) ? implode(', ', $value) : $value }}</li>
@endforeach
</ul>
