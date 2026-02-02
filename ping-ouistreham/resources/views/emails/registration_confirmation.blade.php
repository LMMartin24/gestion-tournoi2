<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; line-height: 1.6;">
    <p>Bonjour <strong>{{ $registration->player_firstname }} {{ $registration->player_lastname }}</strong>,</p>
    
    <p>Votre inscription a bien été prise en compte pour le tableau <strong>{{ $registration->subTable->label }}</strong> 
       commençant à <strong>{{ \Carbon\Carbon::parse($registration->subTable->superTable->start_time)->format('H\hi') }}</strong>.</p>
    
    <p>Pour vous désinscrire, rendez-vous sur votre espace personnel sur notre plateforme.</p>
    
    <p>En cas de besoin, n'hésitez pas à nous contacter à : 
       <a href="mailto:contact@tennisdetabledeouistreham.fr">contact@tennisdetabledeouistreham.fr</a></p>
    
    <p>Sportivement,<br>L'équipe organisatrice</p>
</body>
</html>