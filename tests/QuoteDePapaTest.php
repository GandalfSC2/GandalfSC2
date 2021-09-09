<?php

namespace App\Tests;

use App\Service\QuoteDePapa;
use PHPUnit\Framework\TestCase;

class QuoteDePapaTest extends TestCase
{
    /**
     * Vérifie que la méthode randomQuote du service QuoteDePapa
     * fonctionne comme prévu
     *
     * @return void
     */
    public function testRandomQuote(): void
    {
        $quoteDePapa = new QuoteDePapa();
        $result = $quoteDePapa->randomQuote(); // une citation parmi la liste

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

        # $result = 'Une citation non présente';

        // Pour que le test fonctionne, le resultat de la méthode
        // doit se trouver dans le tableau randomQuoteArray
        $this->assertContains($result, $randomQuoteArray);
    }
}
