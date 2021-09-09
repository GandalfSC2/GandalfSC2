<?php

namespace App\Tests;

use App\Service\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * On va vérifier que la méthode addition du service
     * Calculator fonctionne correctement
     *
     * @return void
     */
    public function testAddition(): void
    {
        // Prenons les chiffres 1 et 2
        // la méthode addition est censé me retourner le chiffre 3
        $calculator = new Calculator();
        $result = $calculator->addition(1, 2); // 3

        // La méthode assertEquals prend 3 arguments :
        // 1)  $expected : la valeur à laquelle on s'entend
        // 2) $actual : la valeur actuellement par la méthode du service
        // 3) $message : qui est le message retourné en cas d'echec du test unitaire
        $this->assertEquals(3, $result);
        // $this->assertTrue(true);
    }
}
