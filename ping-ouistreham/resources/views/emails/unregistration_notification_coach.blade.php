<h2>Notification de désinscription</h2>

<p>Un joueur a été désinscrit :</p>

<ul>
    <li><strong>Joueur :</strong> {{ $registration->player_firstname }} {{ $registration->player_lastname }}</li>
    <li><strong>Tableau :</strong> {{ $registration->subTable->name ?? 'Non spécifié' }}</li>
    <li><strong>Tournoi :</strong> {{ $registration->subTable->superTable->tournament->name ?? 'Non spécifié' }}</li>
    <li><strong>Désinscrit par :</strong> {{ $coach->name ?? 'Le coach' }}</li>
</ul>

<p>Cette place est désormais libérée.</p>