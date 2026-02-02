<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; line-height: 1.6;">
    <h2 style="color: #d9534f;">Alerte Désinscription</h2>
    <p>Le joueur suivant s'est désinscrit d'un tableau :</p>
    <ul>
        <li><strong>Joueur :</strong> {{ $registration->player_firstname }} {{ $registration->player_lastname }}</li>
        <li><strong>Tableau :</strong> {{ $registration->subTable->label }}</li>
        <li><strong>Série :</strong> {{ $registration->subTable->superTable->name }}</li>
        <li><strong>Ancien Statut :</strong> {{ $registration->status }}</li>
    </ul>
    <p>Si un Observer est en place, la liste d'attente a été mise à jour automatiquement.</p>
</body>
</html>