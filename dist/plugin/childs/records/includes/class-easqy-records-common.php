<?php

class Easqy_Records_Common
{
    const GENRES = array( 'Femme', 'Homme', 'Mixte' );
    const CATEGORIES = array( 'Mixte', 'Masters','Séniors','Espoirs','Juniors','Cadets','Minimes','Benjamins');
	const ENVIRONNEMENT = array( 'Outdoor', 'Indoor', 'Hors stade');
	const FAMILIES = ['Sprint','Demi Fond','Fond','Haies','Sauts','Lancers','Épreuves Combinées','Relais','Marche'];
    const EPREUVES = array(

    // Sprint
     '50 m'
    ,'60 m'
    ,'80 m'
    ,'100 m'
    ,'120 m'
    ,'200 m'
    ,'400 m'/* 6 */

	// Demi Fond
    ,'800 m'
    ,'1000 m'
    ,'1500 m'
    ,'2000 m'
    ,'3000 m'
    ,'5000 m'
    ,'10000 m'
    ,'15000 m' /* 14 */

	// Fond
    ,'10 km route'
    ,'15 km route'
    ,'20 km route'
    ,'Semi Marathon'
    ,'Marathon'
    ,'100 km route'
    ,'24 heures' // 21

	// Haies
    ,'50 m haies'
    ,'60 m haies'
    ,'80 m haies'
    ,'100 m haies'
    ,'110 m haies'
    ,'200 m haies'
    ,'400 m haies'
    ,'2000 m steeple'
    ,'3000 m steeple' // 30

	// Sauts
    ,'Hauteur'
    ,'Perche'
    ,'Longueur'
    ,'Triple Saut'  //34

	// Lancers
    ,'Poids'
    ,'Disque'
    ,'Javelot'
    ,'Marteau'// 38

	// Epreuves combinées
    ,'Triathlon'
    ,'Pentathlon'
    ,'Heptathlon'
    ,'Octathlon'
    ,'Décathlon' //43

	// Relais
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
    ,'Medley long' // 54

	// Marche
    ,'2000 m marche'
    ,'3000 m marche'
    ,'5000 m marche'
    ,'10000 m marche'
    ,'20000 m marche'
    ,'10 km marche'
    ,'20 km marche'
    ,'50 km marche'
    ,'Gd fond marche' // 63

    /*64*/ ,'Combinées', // Epreuves combinées

	/*65*/ 'Haies - Challenge', // haies
    /*66*/ 'Hauteur - Challenge', // Sauts
    /*67*/ 'Perche - Challenge', // Sauts
    /*68*/ 'Longueur - Challenge', // Sauts
    /*69*/ 'Triple Saut - Challenge', // Sauts
    /*70*/ 'Poids - Challenge', // Lancers
    /*71*/ 'Disque - Challenge', // Lancers
    /*72*/ 'Javelot - Challenge', // Lancers
    /*73*/ 'Marteau - Challenge',// Lancers
	/*74*/ 'Relais - Challenge' // Relais

    );

	const EPREUVE_FAMILIES = array(
		/*0 Sprint*/    [0,1,2,3,4,5,6],
		/*1 Demi Fond*/ [7,8,9,10,11,12,13,14],
		/*2 Hors Stade*/[15,16,17,18,19,20,21],
		/*3 Haies*/     [22,23,24,25,26,27,28,29,30,65],
		/*4 Sauts*/     [31,32,33,34, 66,67,68,69],
		/*5 Lancers*/   [35,36,37,38, 70,71,72,73],
		/*6 Épreuves Combinées*/ [39,40,41,42,43, 64],
		/*7 Relais*/    [44,45,46,47,48,49,50,51,52,53,54, 74],
		/*8 Marche*/    [55,56,57,58,59,60,61,62,63]
	);

	public static function getFamily( int $epreuve ): int
    {
    	for ($f = 0; $f < count(self::EPREUVE_FAMILIES); ++$f)
	    	if (in_array($epreuve, self::EPREUVE_FAMILIES[$f]))
	    		return $f;

    	return -1;
    }

}
