<?php

class Easqy_Records_Common
{
    const GENRES = array( 'Femme', 'Homme', 'Mixte' );
    const CATEGORIES = array( 'Mixte', 'Masters','Séniors','Espoirs','Juniors','Cadets','Minimes','Benjamins');
    const EPREUVE_FAMILIES = array('Sprint','Demi Fond','Hors Stade','Haies','Sauts','Lancers','Épreuves Combinées',
        'Relais','Marche');

    const EPREUVES = array(
        '50 m'
    ,'60 m'
    ,'80 m'
    ,'100 m'
    ,'120 m'
    ,'200 m'
    ,'400 m' // 6
    ,'800 m'
    ,'1000 m'
    ,'1500 m'
    ,'2000 m'
    ,'3000 m'
    ,'5000 m'
    ,'10000 m'
    ,'15000 m' // 14
    ,'10 km route'
    ,'15 km route'
    ,'20 km route'
    ,'Semi Marathon'
    ,'Marathon'
    ,'100 km route'
    ,'24 heures' // 21
    ,'50 m haies'
    ,'60 m haies'
    ,'80 m haies'
    ,'100 m haies'
    ,'110 m haies'
    ,'200 m haies'
    ,'400 m haies'
    ,'2000 m steeple'
    ,'3000 m steeple' // 30
    ,'Hauteur'
    ,'Perche'
    ,'Longueur'
    ,'Triple Saut' //34
    ,'Poids'
    ,'Disque'
    ,'Javelot'
    ,'Marteau' // 38
    ,'Triathlon'
    ,'Pentathlon'
    ,'Heptathlon'
    ,'Octathlon'
    ,'Décathlon'
    ,'Heptathlon' // 44
    ,'Ekiden'
    ,'Relais 4x60 m'
    ,'Relais 4x100 m'
    ,'Relais 4x200 m'
    ,'Relais 4x400 m'
    ,'Relais 4x800 m'
    ,'Relais 4x1000 m'
    ,'Relais 4x1500 m'
    ,'8x2x2x8'
    ,'Medley court'
    ,'Medley long' // 55
    ,'2000 m marche'
    ,'3000 m marche'
    ,'5000 m marche'
    ,'10000 m marche'
    ,'20000 m marche'
    ,'10 km marche'
    ,'20 km marche'
    ,'50 km marche'
    ,'Gd fond marche' // 64
    ,'Combinées'
    );

    public static function getFamily( int $epreuve ): int
    {
        if ($epreuve <=  6) return 0; // Sprint
        if ($epreuve <= 14) return 1; // Demi Fond
        if ($epreuve <= 21) return 2; // Hors Stade
        if ($epreuve <= 30) return 3; // Haies
        if ($epreuve <= 34) return 4; // Sauts
        if ($epreuve <= 38) return 5; // Lances
        if ($epreuve <= 44) return 6; // Epreuves combinees
        if ($epreuve <= 55) return 7; // Relais
        if ($epreuve <= 64) return 8; // Marche
        return 6;
    }

}
