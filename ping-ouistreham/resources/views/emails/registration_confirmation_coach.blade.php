<h2>Nouvelle inscription enregistrée</h2>

<p>Une inscription vient d'être effectuée :</p>

<ul>
    <li><strong>Joueur :</strong> {{ $registration->player_firstname }} {{ $registration->player_lastname }}</li>
    <li><strong>Club :</strong> {{ $registration->user->club ?? 'Non renseigné' }}</li>
    <li><strong>Tableau :</strong> {{ $registration->subTable->name }}</li>
    <li><strong>Tournoi :</strong> {{ $registration->subTable->superTable->tournament->name }}</li>
    <li><strong>Statut :</strong> {{ $registration->status == 'confirmed' ? 'Confirmé' : 'Liste d\'attente' }}</li>
    <li><strong>Inscrit par :</strong> {{ $coach->name ?? 'Le coach' }}</li>
</ul>

<p>Date : {{ $registration->registered_at->format('d/m/Y H:i') }}</p>