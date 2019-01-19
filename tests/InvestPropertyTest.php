<?php

namespace App\Tests;

use App\Model\Investor;
use App\Model\Loan;
use App\Model\Tranche;
use PHPUnit\Framework\TestCase;

class InvestPropertyTest extends TestCase
{
    /**
     * @var int
     */
    private $firstInvestorId = 1;
    /**
     * @var float
     */
    private $amountMoneyWallet = 1000.0;
    /**
     * @var int
     */
    private $interestRateFirstTranche = 3;
    /**
     * @var int
     */
    private $interestRateSecondTranche = 6;
    /**
     * @var Loan
     */
    private $loan;

    public function setUp()
    {
        parent::setUp();
        $this->loan = new Loan('2015/10/01', '2015/11/15');
    }

    public function testAddNewInvestorNewTrancheWithInvestedSumMoreThanHaveWallet(): void
    {

        $investor = new Investor($this->firstInvestorId, $this->amountMoneyWallet);
        $investor->setInvestedSumMoney(1200);

        $this->assertEquals(1000.0, $investor->getInvestmentMoney());
    }

    public function testAddNewInvestorNewTranche(): Tranche
    {
        $investor = $this->initInvestor();
        $dateAddInvestor = '2015/10/03';
        $tranche = $this->initTranche(1, $this->interestRateFirstTranche);
        $tranche->addInvestor($investor, $dateAddInvestor);

        $investors = $this->getInvestorIdsFromTranche($tranche);

        $this->assertContains($this->firstInvestorId, $investors);

        return $tranche;
    }

    /**
     * @depends testAddNewInvestorNewTranche
     * @param Tranche $tranche
     * @return Tranche
     */
    public function testAddNewInvestorCurrentTranche(Tranche $tranche): Tranche
    {
        $newInvestorId = 2;

        $investor = $this->initInvestor();
        $investor->setInvestedSumMoney(1);

        $dateAddInvestor = '2015/10/04';
        $tranche->addInvestor($investor, $dateAddInvestor);

        $investors = $this->getInvestorIdsFromTranche($tranche);

        $this->assertNotContains($newInvestorId, $investors);

        return $tranche;
    }

    public function testAddNewInvestorNewTrancheWithFiveHundredTranche(): Tranche
    {
        $investedSum = 500;
        $investorId = 3;
        $investor = $this->initInvestor($investorId);
        $investor->setInvestedSumMoney($investedSum);

        $tranche = $this->initTranche(2, $this->interestRateSecondTranche);
        $dateAddInvestor = '2015/10/10';
        $tranche->addInvestor($investor, $dateAddInvestor);

        $investors = $this->getInvestorIdsFromTranche($tranche);

        $this->assertContains($investorId, $investors);

        $this->assertEquals($investedSum, $investor->getInvestmentMoney());

        return $tranche;
    }

    /**
     * @depends testAddNewInvestorNewTrancheWithFiveHundredTranche
     * @param Tranche $tranche
     *
     * @return Tranche
     */
    public function testAddNewInvestorNewTrancheWithOneThousandOneHundredTranche(Tranche $tranche): Tranche
    {
        $investedSum = 1100;
        $newInvestorId = 2;
        $investor = $this->initInvestor();
        $investor->setInvestedSumMoney($investedSum);

        $dateAddInvestor = '2015/10/25';
        $tranche->addInvestor($investor, $dateAddInvestor);

        $investors = $this->getInvestorIdsFromTranche($tranche);

        $this->assertNotContains($newInvestorId, $investors);

        return $tranche;
    }

    /**
     * @depends testAddNewInvestorCurrentTranche
     * @param Tranche $tranche
     */
    public function testTwoTimesAddInLoanSameTrache(Tranche $tranche)
    {
        $loan = clone $this->loan;
        $loan->addTranche($tranche);
        $loan->addTranche($tranche);

        $tranches = $loan->getTranches();

        $this->assertCount(1, $tranches);

    }

    /**
     * @depends testAddNewInvestorCurrentTranche
     * @depends testAddNewInvestorNewTrancheWithOneThousandOneHundredTranche
     * @param Tranche $trancheOne
     * @param Tranche $trancheTwo
     */
    public function testGetLoanFromTrunches(Tranche $trancheOne, Tranche $trancheTwo)
    {
        $this->loan->addTranche($trancheOne);
        $this->loan->addTranche($trancheTwo);

        $resultInterectPeriodTime = $this->loan->getResultInterectPeriodTime('2015/10/01', '2015/11/01');

        $this->assertCount(2, $resultInterectPeriodTime);

        $firstTranche = $resultInterectPeriodTime[0];
        $secondTranche = $resultInterectPeriodTime[1];

        $this->assertTrue(array_key_exists(1, $firstTranche));
        $this->assertTrue(array_key_exists(3, $secondTranche));
        $this->assertEquals(28.06, $firstTranche[1]);
        $this->assertEquals(21.29, $secondTranche[3]);
    }

    /**
     * @return Tranche
     */
    private function initTranche($id, $interestRate): Tranche
    {
        return new Tranche($id, $this->amountMoneyWallet, $interestRate);
    }

    /**
     * @param int $investorId
     * @return Investor
     */
    private function initInvestor($investorId = 0): Investor
    {
        if ($investorId === 0) {
            $investorId = $this->firstInvestorId;
        }

        return new Investor($investorId, $this->amountMoneyWallet);
    }

    /**
     * @param Tranche $tranche
     * @return array
     */
    private function getInvestorIdsFromTranche(Tranche $tranche): array
    {
        return array_map(function (Investor $investor){
            return $investor->getId();
        }, $tranche->getInvestors());

    }
}