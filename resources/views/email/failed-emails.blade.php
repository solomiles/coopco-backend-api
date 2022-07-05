<h3>Could not send this email to the following users, </h3>
<p><strong>Subject:</strong> {{ $data['subject'] }}</p>
<p><strong>Cooperative:</strong> {{ $data['cooperative'] }}</p>
<p><strong>Emails:</strong></p>
<ul>
    @php
        $emails = $data['emails'];
    @endphp

    @foreach($emails as $email)
    <li>
        {{ $email }}
    </li>
    @endforeach
</ul>
