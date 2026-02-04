<h2>Nouvelle inscription enregistrée</h2>
<p>Un coach vient d'inscrire un joueur :</p>
<ul>
    <li><strong>Coach :</strong> {{ $coach->name ?? 'Non précisé' }}</li>
    <li><strong>Joueur :</strong> {{ $registration->player_firstname }} {{ $registration->player_lastname }}</li>
    <li><strong>Tableau :</strong> {{ $registration->subTable->name ?? 'N/A' }}</li>
    <li><strong>Tournoi :</strong> {{ $registration->subTable->superTable->tournament->name ?? 'N/A' }}</li>
    <li><strong>Points :</strong> {{ $registration->player_points }}</li>
</ul>
<p>Date : {{ $registration->registered_at }}</p>