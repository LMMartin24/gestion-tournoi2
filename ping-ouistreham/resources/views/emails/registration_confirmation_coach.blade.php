<h2>Nouvelle inscription enregistrée</h2>
<p>Un coach vient d'inscrire un joueur :</p>
<ul>
    <li><strong>Joueur :</strong> {{ $registration->player_firstname }} {{ $registration->player_lastname }}</li>
    <li><strong>Licence :</strong> {{ $registration->player_license }}</li>
    <li><strong>Points :</strong> {{ $registration->player_points }}</li>
    <li><strong>Tableau :</strong> {{ $registration->subTable->name }}</li>
    <li><strong>Statut :</strong> {{ $registration->status == 'confirmed' ? 'Confirmé' : 'Liste d\'attente' }}</li>
</ul>
<p>Date de l'action : {{ $registration->registered_at }}</p>