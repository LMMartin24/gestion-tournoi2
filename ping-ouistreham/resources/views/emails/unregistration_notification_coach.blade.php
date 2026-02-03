<h2>Notification de désinscription</h2>
<p>Un joueur a été désinscrit par son coach :</p>
<ul>
    <li><strong>Joueur :</strong> {{ $registration->player_firstname }} {{ $registration->player_lastname }}</li>
    <li><strong>Tableau :</strong> {{ $registration->subTable->name }}</li>
</ul>
<p>Cette place est désormais libérée.</p>