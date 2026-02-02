<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; line-height: 1.6;">
    <p>Bonjour <strong>{{ $registration->player_firstname }} {{ $registration->player_lastname }}</strong>,</p>
    
    <p>Votre inscription a bien été prise en compte pour le <strong>{{ $registration->subTable->label }}</strong> 
       commençant à <strong>{{ \Carbon\Carbon::parse($registration->subTable->superTable->start_time)->format('H\hi') }}</strong>.</p>
    
    <p>Pour vous désinscrire, rendez-vous sur votre espace personnel sur notre plateforme.</p>
    
    <p>En cas de besoin, n'hésitez pas à nous contacter à : 
        📞 Téléphone : 0759522323,
       <a href="mailto:tennisdetableouistreham@gmail.com">tennisdetableouistreham@gmail.com</a></p>
    
    <p>Sportivement,<br>Camille Humbert, Sécrétaire Adjointe</p>
</body>
</html>