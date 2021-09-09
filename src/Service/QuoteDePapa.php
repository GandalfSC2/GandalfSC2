<?php

namespace App\Service;

class QuoteDePapa
{

    /**
     * Retourne des citations de devs de manière aléatoire
     *
     * @return string
     */
    public function randomQuote(): string
    {
        $randomQuoteArray = [
            "Le gras c'est la vie", // index 0
            "OK", // index 1
            "C'est pas faux !", // index 2
            "Vouala",
            "Des customs curry",
            "RTFM",
            "auto waouwaouwaing",
            "search('homme poilu')",
            "Toutou beignet",
            "cateogry",
            "Quand tu ajoutes un attribut à la manau, c'est l'attribut de Dana",
            "j'oublie vite les choses",
            "un clavier AZERTY en vaux deux",
            "Flash sur Firefox, c’est de l’Adobe !",
            "la technique du saumon hydrater un objet",
            "peut-être= sûr",
            "on est les ylusses!"
        ];

        // Retourne un index aléatoire
        $randomIndex = array_rand($randomQuoteArray);

        // On retourne une citation aléatoire parmi la liste
        // $randomQuoteArray
        return $randomQuoteArray[$randomIndex];
    }
}
